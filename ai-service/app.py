import os
from flask import Flask, request, jsonify
import numpy as np
from fastembed import TextEmbedding

# Ultra-lightweight ONNX-powered embedding (~65MB RAM total, fits easily in Render 512MB free tier!)
# Uses the exact same sentence-transformers/all-MiniLM-L6-v2 (384 dimensions)
model = TextEmbedding(model_name="sentence-transformers/all-MiniLM-L6-v2")
app = Flask(__name__)


def cosine_similarity(v1, v2):
    if not v1 or not v2:
        return 0.0
    a = np.array(v1, dtype=np.float32)
    b = np.array(v2, dtype=np.float32)
    norm_a = np.linalg.norm(a)
    norm_b = np.linalg.norm(b)
    if norm_a == 0 or norm_b == 0:
        return 0.0
    return float(np.dot(a, b) / (norm_a * norm_b))


def get_embedding(text):
    text = str(text or "").strip()
    if not text:
        return []
    embeddings = list(model.embed([text]))
    if not embeddings:
        return []
    return embeddings[0].tolist()


@app.route('/')
def home():
    return jsonify({
        "status": "AI Service Running",
        "engine": "FastEmbed ONNX (Ultra-lightweight)",
        "model": "all-MiniLM-L6-v2"
    })


@app.route('/embedding', methods=['POST'])
def embedding():
    data = request.json or {}
    text = data.get('text', '')
    vector = get_embedding(text)
    return jsonify({
        "embedding": vector
    })


@app.route('/compare-title', methods=['POST'])
def compare_title():
    data = request.json or {}
    title1 = data.get('title1', '')
    title2 = data.get('title2', '')

    emb1 = get_embedding(title1)
    emb2 = get_embedding(title2)
    sim = cosine_similarity(emb1, emb2) * 100.0

    return jsonify({
        "similarity": round(float(sim), 2)
    })


@app.route('/compare-research', methods=['POST'])
def compare_research():
    data = request.json or {}
    title_emb1 = get_embedding(data.get('title1', ''))
    title_emb2 = get_embedding(data.get('title2', ''))
    obj_emb1 = get_embedding(data.get('objective1', ''))
    obj_emb2 = get_embedding(data.get('objective2', ''))

    title_sim = cosine_similarity(title_emb1, title_emb2) * 100.0
    obj_sim = cosine_similarity(obj_emb1, obj_emb2) * 100.0
    overall = (title_sim + obj_sim) / 2.0

    return jsonify({
        "title_similarity": round(float(title_sim), 2),
        "objective_similarity": round(float(obj_sim), 2),
        "overall_similarity": round(float(overall), 2)
    })


@app.route('/compare', methods=['POST'])
def compare_embedding():
    data = request.json or {}
    emb1 = data.get('embedding1', [])
    emb2 = data.get('embedding2', [])
    sim = cosine_similarity(emb1, emb2) * 100.0

    return jsonify({
        "similarity": round(float(sim), 2)
    })


@app.route('/classify', methods=['POST'])
def classify_category():
    data = request.json or {}
    text = data.get('text', '')
    categories = data.get('categories', [])
    project_types = data.get('project_types', [])
    threshold = float(data.get('minimum_confidence', 40.0))

    if not text:
        return jsonify({"categories": [], "project_types": []})

    text_emb = get_embedding(text)

    cat_results = []
    for cat_name in categories:
        cat_emb = get_embedding(cat_name)
        score = cosine_similarity(text_emb, cat_emb) * 100.0
        confidence = round(float(score), 2)
        if confidence >= threshold:
            cat_results.append({
                "name": cat_name,
                "confidence": confidence
            })

    cat_results.sort(key=lambda x: x['confidence'], reverse=True)

    pt_results = []
    for pt in project_types:
        pt_name = pt.get('name', '')
        pt_category = pt.get('category', '')
        pt_emb = get_embedding(pt_name)
        score = cosine_similarity(text_emb, pt_emb) * 100.0
        confidence = round(float(score), 2)
        if confidence >= threshold:
            pt_results.append({
                "name": pt_name,
                "category": pt_category,
                "confidence": confidence
            })

    pt_results.sort(key=lambda x: x['confidence'], reverse=True)

    return jsonify({
        "categories": cat_results,
        "project_types": pt_results
    })


if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
