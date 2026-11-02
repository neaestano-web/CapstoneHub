<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Viewer — {{ $research->title ?? 'Research' }}</title>

    {{-- docx-preview requires JSZip --}}
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #e8ecef;
            min-height: 100vh;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #1e293b;
            color: #fff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .toolbar-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: rgba(34,197,94,0.15);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }
        .toolbar-title {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 500px;
        }
        .toolbar-badge {
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .toolbar-badge.docx { background: #1d4ed8; color: #fff; }
        .toolbar-badge.pdf  { background: #dc2626; color: #fff; }

        .toolbar-right { display: flex; align-items: center; gap: 8px; }
        .toolbar-btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .toolbar-btn-outline {
            background: transparent;
            color: rgba(255,255,255,0.7);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .toolbar-btn-outline:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .toolbar-btn-download {
            background: #22c55e;
            color: #fff;
        }
        .toolbar-btn-download:hover { background: #16a34a; }

        /* ===== DOCUMENT CONTAINER ===== */
        .doc-wrapper {
            max-width: 960px;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* docx-preview container */
        #docx-container {
            background: #525659;
            min-height: 80vh;
        }

        #docx-container .docx-wrapper {
            background: transparent !important;
            padding: 16px 0 !important;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #docx-container .docx-wrapper > section.docx {
            box-shadow: 0 4px 16px rgba(0,0,0,0.2) !important;
            margin-bottom: 20px !important;
            background: #fff !important;
        }

        /* PDF embed */
        .pdf-embed {
            width: 100%;
            min-height: 90vh;
            border: none;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        @if(request('embedded'))
        body {
            background: #525659 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .doc-wrapper {
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        #docx-container {
            box-shadow: none !important;
            border: none !important;
            min-height: 100vh;
        }
        @endif

        /* Loading state */
        .loading-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
            color: #64748b;
            text-align: center;
        }
        .loading-spinner {
            width: 40px; height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: #22c55e;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 16px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .error-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
            color: #dc2626;
            text-align: center;
        }
        .error-state h2 { font-size: 18px; margin-bottom: 8px; }
        .error-state p { font-size: 13px; color: #64748b; max-width: 400px; }

        @media (max-width: 768px) {
            .toolbar-title { max-width: 200px; }
            #docx-container .docx-wrapper > section.docx {
                padding: 40px 24px !important;
            }
        }
    </style>
</head>
<body>
@if(!request('embedded') && !request('hide_toolbar'))
    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <div class="toolbar-icon">📄</div>
            <span class="toolbar-title">{{ $research->title ?? 'Document' }}</span>
            <span class="toolbar-badge {{ $extension }}">{{ strtoupper($extension) }}</span>
        </div>
        <div class="toolbar-right">
            <a href="{{ url()->previous() }}" class="toolbar-btn toolbar-btn-outline">
                ← Back
            </a>
            <a href="{{ $fileUrl }}" class="toolbar-btn toolbar-btn-download" download>
                ⬇ Download
            </a>
        </div>
    </div>
@endif

    {{-- Document --}}
    <div class="doc-wrapper">
        @if($extension === 'pdf')
            <embed src="{{ $fileUrl }}" type="application/pdf" class="pdf-embed">
        @else
            <div id="docx-container">
                <div class="loading-state" id="loadingState">
                    <div class="loading-spinner"></div>
                    <p style="font-weight:600;margin-bottom:4px">Rendering document…</p>
                    <p style="font-size:12px">This may take a moment for large files.</p>
                </div>
            </div>
        @endif
    </div>

    @if($extension !== 'pdf')
    <script>
        (function() {
            const fileUrl = @json($fileUrl);
            const container = document.getElementById('docx-container');
            const loading = document.getElementById('loadingState');

            fetch(fileUrl)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch document (HTTP ' + response.status + ')');
                    return response.arrayBuffer();
                })
                .then(arrayBuffer => {
                    // Clear loading state
                    loading.remove();

                    // Render with docx-preview (preserves formatting much better than mammoth)
                    return docx.renderAsync(arrayBuffer, container, null, {
                        className: 'docx',
                        inWrapper: true,
                        ignoreWidth: false,
                        ignoreHeight: false,
                        ignoreFonts: false,
                        breakPages: true,
                        ignoreLastRenderedPageBreak: false,
                        experimental: true,
                        trimXmlDeclaration: true,
                        useBase64URL: true,
                        renderHeaders: true,
                        renderFooters: true,
                        renderFootnotes: true,
                        renderEndnotes: true,
                    });
                })
                .then(() => {
                    console.log('Document rendered successfully');
                })
                .catch(error => {
                    console.error('Document render error:', error);
                    container.innerHTML = `
                        <div class="error-state">
                            <div style="font-size:48px;margin-bottom:16px">⚠️</div>
                            <h2>Could not render document</h2>
                            <p>${error.message}</p>
                            <a href="${fileUrl}" download
                               style="margin-top:20px;padding:10px 24px;background:#22c55e;color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600">
                                Download Original File
                            </a>
                        </div>
                    `;
                });
        })();
    </script>
    @endif
</body>
</html>