@extends('layouts.public')
@section('title', 'Semantic Search - JHCSC Capstone Repository')
@section('content')
<style>
    :root{--primary:#166534;--primary-dark:#14532d;--primary-light:#22c55e;--accent:#15803d}
    .sim-hero{background:linear-gradient(135deg,#0f2e0f 0%,#166534 50%,#15803d 100%);padding:56px 0 64px;position:relative;overflow:hidden}
    .sim-hero::before{content:'';position:absolute;top:-50%;right:-15%;width:400px;height:400px;background:rgba(255,255,255,.04);border-radius:50%}
    .sim-hero::after{content:'';position:absolute;bottom:-40%;left:-10%;width:350px;height:350px;background:rgba(255,255,255,.04);border-radius:50%}
    .sim-hero *{position:relative;z-index:1}
    .hero-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 16px;border-radius:20px;font-size:11px;font-weight:600;background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.12)}
    .form-card{border-radius:20px;background:white;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.04)}
    .form-card .accent-bar{height:5px;background:linear-gradient(90deg,#166534,#22c55e)}
    .form-input{border:1.5px solid #e5e7eb;border-radius:12px;padding:14px 18px;font-size:14px;transition:all .2s;width:100%;outline:none;background:#f9fafb}
    .form-input:focus{border-color:#166534;box-shadow:0 0 0 4px rgba(22,101,52,.1);background:white}
    .form-input::placeholder{color:#9ca3af}
    .btn-search{display:inline-flex;align-items:center;gap:8px;padding:14px 36px;background:linear-gradient(135deg,#166534,#22c55e);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s}
    .btn-search:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(22,101,52,.3)}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .animate-in{animation:fadeInUp .5s ease forwards;opacity:0}
</style>

{{-- Hero Section --}}
<div class="sim-hero">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="hero-chip">🔍 Search</span>
                <span class="hero-chip">🧠 Semantic Understanding</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                Semantic <span style="color:#86efac">Search</span>
            </h1>
            <p class="text-lg" style="color:rgba(255,255,255,.75);max-width:560px;margin:0 auto;line-height:1.7">
                Search using natural language. It goes beyond keyword matching—the system understands the meaning of your query and retrieves relevant research papers.
            </p>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="py-10" style="background:#f8fafc">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Search Form --}}
        <div class="form-card animate-in">
            <div class="accent-bar"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span style="width:40px;height:40px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:20px">🔍</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">What are you looking for?</h2>
                        <p class="text-sm text-gray-500">Describe your research topic in natural language</p>
                    </div>
                </div>

                <form action="{{ route('semantic-search.search') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="query" class="block text-sm font-semibold text-gray-700 mb-2">
                            Search Query <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="query"
                            id="query"
                            rows="4"
                            class="form-input"
                            placeholder="e.g. student performance monitoring system using machine learning"
                            required>{{ old('query') }}</textarea>
                    </div>

                    <button type="submit" class="btn-search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                </form>
            </div>
        </div>

        {{-- How It Works --}}
        <div class="form-card animate-in" style="animation-delay:.2s">
            <div class="accent-bar"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span style="width:40px;height:40px;border-radius:12px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;font-size:20px">💡</span>
                    <h2 class="text-lg font-bold text-gray-900">How It Works</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div style="width:48px;height:48px;border-radius:14px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 12px">📝</div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">1. Type Your Query</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">Use natural language to describe what you're looking for</p>
                    </div>
                    <div class="text-center">
                        <div style="width:48px;height:48px;border-radius:14px;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 12px">🧠</div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">2. Meaning-Based Understanding</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">The system analyzes meaning, not just keywords</p>
                    </div>
                    <div class="text-center">
                        <div style="width:48px;height:48px;border-radius:14px;background:#dcfce7;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 12px">🎯</div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">3. Get Results</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">View semantically relevant research papers ranked by similarity</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
