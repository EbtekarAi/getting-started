// Minimal Node.js backend: POST /ask {"question": "..."} → {"answer": "..."}
// No dependencies. Node 20.12+.
import http from "node:http";

try { process.loadEnvFile?.(); } catch { /* no .env file */ }

const KEY = process.env.EBTEKAR_API_KEY;
const BASE = (process.env.EBTEKAR_BASE_URL || "https://ebtekarai.ir/api/developer-gateway").replace(/\/+$/, "");
const MODEL = process.env.EBTEKAR_MODEL || "ebtekar-smart";
if (!KEY) throw new Error("Set EBTEKAR_API_KEY");

async function ask(question) {
  const r = await fetch(`${BASE}/v1/messages`, {
    method: "POST",
    headers: { "x-api-key": KEY, "anthropic-version": "2023-06-01", "content-type": "application/json", "idempotency-key": crypto.randomUUID() },
    body: JSON.stringify({ model: MODEL, max_tokens: 1024, messages: [{ role: "user", content: question }] }),
    signal: AbortSignal.timeout(60_000),
  });
  const data = await r.json();
  if (!r.ok) throw Object.assign(new Error(data?.error?.message || `Gateway error ${r.status}`), { status: r.status });
  return data.content.filter((b) => b.type === "text").map((b) => b.text).join("");
}

http
  .createServer(async (req, res) => {
    const reply = (status, body) => { res.writeHead(status, { "content-type": "application/json; charset=utf-8" }); res.end(JSON.stringify(body)); };
    if (req.method !== "POST" || req.url !== "/ask") return reply(404, { error: "POST /ask" });
    let raw = "";
    for await (const chunk of req) { raw += chunk; if (raw.length > 50_000) return reply(413, { error: "Too large" }); }
    try {
      const { question } = JSON.parse(raw || "{}");
      if (typeof question !== "string" || !question.trim()) return reply(400, { error: "question is required" });
      reply(200, { answer: await ask(question.slice(0, 8000)) });
    } catch (e) {
      reply(e.status || 500, { error: e.message });
    }
  })
  .listen(process.env.PORT || 3000, () => console.log("✓ http://localhost:" + (process.env.PORT || 3000) + "/ask"));
