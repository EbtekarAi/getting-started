<div align="center">

<img src="https://raw.githubusercontent.com/EbtekarAi/ebtekar-ai/main/assets/logo.png" width="84">

# Getting started with Ebtekar

**The smallest possible backend in Node.js, Python (FastAPI) and PHP that calls the Ebtekar Developer Gateway.**

کوچک‌ترین بک‌اند ممکن در Node.js، پایتون (FastAPI) و PHP برای اتصال به درگاه ابتکار

![Node](https://img.shields.io/badge/Node.js-339933?logo=nodedotjs&logoColor=white)
![FastAPI](https://img.shields.io/badge/FastAPI-009688?logo=fastapi&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-7C3AED)

</div>

---

Each folder is a complete, standalone project. Pick your stack, set one environment variable and run.

هر پوشه یک پروژهٔ کامل و مستقل است. زبان خود را انتخاب کنید، یک متغیر محیطی تنظیم کنید و اجرا کنید.

```bash
export EBTEKAR_API_KEY="ebt_live_..."      # Windows PowerShell: $env:EBTEKAR_API_KEY="ebt_live_..."
```

| Stack | Folder | Run | Try it |
|---|---|---|---|
| **Node.js** (no deps) | [`nodejs/`](nodejs) | `node nodejs/server.mjs` | `curl -X POST localhost:3000/ask -d '{"question":"سلام"}'` |
| **Python · FastAPI** | [`python-fastapi/`](python-fastapi) | `pip install -r python-fastapi/requirements.txt`<br>`cd python-fastapi && uvicorn main:app` | `curl -X POST localhost:8000/ask -H "content-type: application/json" -d '{"question":"سلام"}'` |
| **PHP 8** | [`php/`](php) | `cd php && php -S localhost:8000` | open `http://localhost:8000` |

All three:
- keep the key on the server (read from `EBTEKAR_API_KEY`);
- use `ebtekar-smart` unless you set `EBTEKAR_MODEL`;
- send a unique `Idempotency-Key` with every request;
- pass gateway errors (401, 402, 429 …) back with their status code.

## Next steps / قدم بعدی

- 🍳 Recipes for streaming, tools, images and more → [**ebtekar-cookbook**](https://github.com/EbtekarAi/ebtekar-cookbook)
- 💬 A full web chat → [**chat-ui**](https://github.com/EbtekarAi/chat-ui)
- 📘 Full reference → [Developer guide](https://github.com/EbtekarAi/ebtekar-ai/blob/main/docs/developers.md) · [راهنمای فارسی](https://github.com/EbtekarAi/ebtekar-ai/blob/main/docs/developers.fa.md)

<sub>MIT © Ebtekar AI</sub>
