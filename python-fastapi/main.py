"""Minimal FastAPI backend: POST /ask {"question": "..."} → {"answer": "..."}

Run:  pip install -r requirements.txt
      uvicorn main:app --reload
"""
import os
import uuid

import anthropic
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field

client = anthropic.AsyncAnthropic(
    api_key=os.environ["EBTEKAR_API_KEY"],
    base_url=os.environ.get("EBTEKAR_BASE_URL", "https://ebtekarai.ir/api/developer-gateway"),
    default_headers={"User-Agent": "Ebtekar-Client/1.0"},
)
MODEL = os.environ.get("EBTEKAR_MODEL", "ebtekar-smart")

app = FastAPI(title="Ebtekar · getting started")


class Ask(BaseModel):
    question: str = Field(min_length=1, max_length=8000)


@app.post("/ask")
async def ask(body: Ask) -> dict:
    try:
        msg = await client.messages.create(
            model=MODEL,
            max_tokens=1024,
            messages=[{"role": "user", "content": body.question}],
            extra_headers={"Idempotency-Key": str(uuid.uuid4())},
        )
    except anthropic.APIStatusError as e:
        raise HTTPException(status_code=e.status_code, detail=e.message) from e
    return {"answer": "".join(b.text for b in msg.content if b.type == "text"), "usage": msg.usage.model_dump()}


@app.get("/health")
async def health() -> dict:
    return {"ok": True, "model": MODEL}
