FROM python:3.10-slim

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Pre-download FastEmbed model during build so startup is instant
RUN python -c "from fastembed import TextEmbedding; list(TextEmbedding(model_name='sentence-transformers/all-MiniLM-L6-v2').embed(['warmup']))"

COPY . .

ENV PORT=5000
EXPOSE 5000

CMD ["sh", "-c", "gunicorn -w 1 -b 0.0.0.0:${PORT} --timeout 120 app:app"]
