@extends('layouts.public')
@section('title', 'Search Results - JHCSC Capstone Repository')
@section('content')
<style>
    :root{--primary:#166534;--primary-dark:#14532d;--primary-light:#22c55e;--accent:#15803d}
    .sim-hero{background:linear-gradient(135deg,#0f2e0f 0%,#166534 50%,#15803d 100%);padding:48px 0 56px;position:relative;overflow:hidden}
    .sim-hero::before{content:'';position:absolute;top:-50%;right:-15%;width:400px;height:400px;background:rgba(255,255,255,.04);border-radius:50%}
    .sim-hero::after{content:'';position:absolute;bottom:-40%;left:-10%;width:350px;height:350px;background:rgba(255,255,255,.04);border-radius:50%}
    .sim-hero *{position:relative;z-index:1}
    .result-card{background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;transition:all .3s}
    .result-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.04);border-color:#22c55e}
    .result-card .accent-bar{height:4px;background:linear-gradient(90deg,#166534,#22c55e)}
    .view-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 20px;background:#f0fdf4;color:#166534;border-radius:10px;font-size:13px;font-weight:600;transition:all .3s;text-decoration:none}
    .view-btn:hover{background:#166534;color:white;transform:translateX(5px);box-shadow:0 4px 12px rgba(22,101,52,.2)}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .animate-in{animation:fadeInUp .5s ease forwards;opacity:0}
</style>

{{-- Hero Section --}}
<div class="sim-hero">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold" 
                  style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.15);margin-bottom:18px;backdrop-filter:blur(4px)">
                🔍 Search Results
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2 leading-tight">
                Results for: <span style="color:#86efac">"{{ $query }}"</span>
            </h1>
            <p class="text-base" style="color:rgba(255,255,255,.7)">
                {{ $results->count() }} result(s) found
            </p>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="py-10" style="background:#f8fafc">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Back Link --}}
        <div class="animate-in">
            <a href="{{ route('semantic-search') }}" 
               style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;background:white;border:1.5px solid #e5e7eb;border-radius:12px;font-size:13px;font-weight:500;color:#374151;text-decoration:none;transition:all .3s"
               onmouseover="this.style.borderColor='#166534';this.style.color='#166534';this.style.background='#f0fdf4'"
               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='white'">
                <span style="font-size:16px">←</span> New Search
            </a>
        </div>

        {{-- Results --}}
        @forelse($results as $item)
            <div class="result-card animate-in" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div class="accent-bar"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('repository.show', $item['research']->id) }}"
                               class="text-lg font-bold text-gray-900 hover:text-green-700 transition-colors no-underline"
                               style="line-height:1.4;display:block;margin-bottom:8px">
                                {{ $item['research']->title }}
                            </a>
                            <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                {{ Str::limit($item['research']->abstract ?? $item['research']->objectives, 200) }}
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <span style="width:20px;height:20px;border-radius:5px;background:#dcfce7;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#166534">
                                        {{ substr($item['research']->authors, 0, 1) }}
                                    </span>
                                    {{ $item['research']->authors }}
                                </span>
                                <span>•</span>
                                <span>{{ $item['research']->year }}</span>
                                <span>•</span>
                                <span>{{ $item['research']->category->name ?? 'Uncategorized' }}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <a href="{{ route('repository.show', $item['research']->id) }}" class="view-btn mt-3">
                                View Details
                                <span style="font-size:16px;line-height:1">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 animate-in">
                <div style="width:80px;height:80px;border-radius:20px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 20px">🔍</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Relevant Results Found</h3>
                <p class="text-gray-500 text-sm max-w-md mx-auto" style="line-height:1.7">
                    Try a different search term or broaden your query to find more relevant research papers.
                </p>
                <a href="{{ route('semantic-search') }}" 
                   style="display:inline-flex;align-items:center;gap:8px;margin-top:20px;padding:12px 28px;background:#166534;color:white;border-radius:12px;font-size:14px;font-weight:600;text-decoration:none;transition:all .3s"
                   onmouseover="this.style.background='#14532d'" 
                   onmouseout="this.style.background='#166534'">
                    Try New Search
                </a>
            </div>
        @endforelse

    </div>
</div>
@endsection
