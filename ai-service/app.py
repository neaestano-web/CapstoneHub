from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity
from flask import Flask, request, jsonify
import numpy as np

model = SentenceTransformer(
    "all-MiniLM-L6-v2"
)
app = Flask(__name__)


@app.route('/')
def home():
    return {
        "status": "AI Service Running"
    }


@app.route('/embedding', methods=['POST'])
def embedding():

    data = request.json

    text = data['text']

    vector = model.encode(text)

    return jsonify({
        "embedding": vector.tolist()
    })


@app.route('/compare-title', methods=['POST'])
def compare_title():

    data = request.json

    title1 = data['title1']
    title2 = data['title2']

    emb1 = model.encode(title1)
    emb2 = model.encode(title2)

    similarity = cosine_similarity(
        [emb1],
        [emb2]
    )[0][0]

    return jsonify({
        "similarity":
        round(float(similarity)*100, 2)
    })


@app.route('/compare-research', methods=['POST'])
def compare_research():

    data = request.json

    title1 = data['title1']
    objective1 = data['objective1']

    title2 = data['title2']
    objective2 = data['objective2']

    title_emb1 = model.encode(title1)
    title_emb2 = model.encode(title2)

    obj_emb1 = model.encode(objective1)
    obj_emb2 = model.encode(objective2)

    title_similarity = cosine_similarity(
        [title_emb1],
        [title_emb2]
    )[0][0]

    objective_similarity = cosine_similarity(
        [obj_emb1],
        [obj_emb2]
    )[0][0]

    overall = (
        title_similarity +
        objective_similarity
    ) / 2

    return jsonify({

        "title_similarity":
        round(float(title_similarity)*100, 2),

        "objective_similarity":
        round(float(objective_similarity)*100, 2),

        "overall_similarity":
        round(float(overall)*100, 2)
    })


@app.route('/compare', methods=['POST'])
def compare_embedding():

    emb1 = np.array(
        request.json['embedding1']
    )

    emb2 = np.array(
        request.json['embedding2']
    )

    similarity = cosine_similarity(
        [emb1],
        [emb2]
    )[0][0]

    return jsonify({

        "similarity":
        round(
            float(similarity)*100,
            2
        )
    })


@app.route('/classify', methods=['POST'])
def classify_category():
    """
    Classify research text into matching categories and project types.
    Expects: {
        "text": "...",
        "categories": ["cat1", "cat2", ...],
        "project_types": [{"name": "...", "category": "..."}, ...]  (optional)
    }
    Returns: {
        "categories": [{"name": "cat1", "confidence": 85.3}, ...],
        "project_types": [{"name": "pt1", "category": "cat1", "confidence": 72.1}, ...]
    }
    """
    data = request.json
    text = data.get('text', '')
    categories = data.get('categories', [])
    project_types = data.get('project_types', [])

    if not text:
        return jsonify({"categories": [], "project_types": []})

    text_emb = model.encode(text)
    # Keep this aligned with the Laravel auto-classification setting.
    threshold = float(data.get('minimum_confidence', 40.0))

    # Score categories
    cat_results = []
    for cat_name in categories:
        cat_emb = model.encode(cat_name)
        score = cosine_similarity([text_emb], [cat_emb])[0][0]
        confidence = round(float(score) * 100, 2)
        if confidence >= threshold:
            cat_results.append({
                "name": cat_name,
                "confidence": confidence
            })

    cat_results.sort(key=lambda x: x['confidence'], reverse=True)

    # Score project types
    pt_results = []
    for pt in project_types:
        pt_name = pt.get('name', '')
        pt_category = pt.get('category', '')
        pt_emb = model.encode(pt_name)
        score = cosine_similarity([text_emb], [pt_emb])[0][0]
        confidence = round(float(score) * 100, 2)
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


@app.route('/convert-to-pdf', methods=['POST'])
def convert_to_pdf():
    """
    Convert a DOCX file to PDF using Microsoft Word.
    Expects: { "input_path": "/absolute/path/to/file.docx", "output_path": "/absolute/path/to/output.pdf" }
    Returns: { "success": true, "output_path": "..." }
    """
    import os
    data = request.json
    input_path = data.get('input_path', '')
    output_path = data.get('output_path', '')

    if not input_path or not output_path:
        return jsonify({"success": False, "error": "input_path and output_path are required"}), 400

    if not os.path.exists(input_path):
        return jsonify({"success": False, "error": f"Input file not found: {input_path}"}), 404

    try:
        import pythoncom
        pythoncom.CoInitialize()
        try:
            from docx2pdf import convert
            # Ensure output directory exists
            os.makedirs(os.path.dirname(output_path), exist_ok=True)
            convert(input_path, output_path)
            return jsonify({"success": True, "output_path": output_path})
        finally:
            pythoncom.CoUninitialize()
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500


@app.route('/merge-documents', methods=['POST'])
def merge_documents():
    """
    Merge multiple PDF or DOCX files into a single combined PDF in the given sequence.
    Expects: { "input_paths": ["/path/file1.pdf", "/path/file2.docx", ...], "output_path": "/path/merged.pdf" }
    """
    import os
    import tempfile
    from pypdf import PdfWriter

    data = request.json or {}
    input_paths = data.get('input_paths', [])
    output_path = data.get('output_path', '')

    if not input_paths or not output_path:
        return jsonify({"success": False, "error": "input_paths and output_path are required"}), 400

    merger = PdfWriter()
    temp_pdfs = []

    try:
        import pythoncom
        pythoncom.CoInitialize()
        try:
            from docx2pdf import convert
            for p in input_paths:
                if not os.path.exists(p):
                    continue
                ext = os.path.splitext(p)[1].lower()
                if ext == '.pdf':
                    merger.append(p)
                elif ext in ['.docx', '.doc']:
                    tmp_pdf = tempfile.NamedTemporaryFile(suffix='.pdf', delete=False)
                    tmp_pdf.close()
                    temp_pdfs.append(tmp_pdf.name)
                    convert(p, tmp_pdf.name)
                    merger.append(tmp_pdf.name)

            os.makedirs(os.path.dirname(output_path), exist_ok=True)
            merger.write(output_path)
            merger.close()
            return jsonify({"success": True, "output_path": output_path})
        finally:
            pythoncom.CoUninitialize()
    except Exception as e:
        try:
            merger.close()
        except Exception:
            pass
        return jsonify({"success": False, "error": str(e)}), 500
    finally:
        for tmp in temp_pdfs:
            if os.path.exists(tmp):
                try:
                    os.remove(tmp)
                except Exception:
                    pass


if __name__ == '__main__':
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=True
    )
