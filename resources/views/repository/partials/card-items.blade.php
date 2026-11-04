@foreach($researches as $index => $research)
    <div class="capstone-card project-card-item" 
         data-title="{{ strtolower($research->title) }}" 
         data-authors="{{ strtolower($research->authors ?? '') }}"
         data-abstract="{{ strtolower($research->abstract ?? '') }}"
         data-category="{{ strtolower($research->category->name ?? '') }}">
        
        <div class="card-top-content">
            <!-- Title -->
            <a href="{{ route('repository.show', $research->id) }}" class="card-title-link">
                {{ $research->title }}
            </a>

            <!-- Author & Year Row -->
            <div class="card-meta-row">
                <span class="card-author-name" title="{{ $research->authors ?? 'Student Researcher' }}">
                    {{ $research->authors ?? 'SCS Researchers' }}
                </span>
                <span class="card-year-badge">
                    {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                </span>
            </div>

            <!-- Abstract Snippet -->
            <p class="card-abstract-text">
                {{ $research->abstract ?? $research->objectives ?? 'This capstone focuses on developing modern software solutions and technological architectures for institutional automation.' }}
            </p>
        </div>

        <!-- Footer Row: Stats + Solid Green View Project Button -->
        <div class="card-footer-row">
            <div class="card-stats-info">
                <span class="card-stat-item" title="Views">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{ number_format($research->views_count ?? 0) }}
                </span>
                <span class="card-stat-item" title="Downloads">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    {{ number_format($research->approved_downloads_count ?? 0) }}
                </span>
            </div>
            <a href="{{ route('repository.show', $research->id) }}" class="btn-view-project">
                View Project
            </a>
        </div>

    </div>
@endforeach
