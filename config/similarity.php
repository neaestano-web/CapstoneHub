<?php

return [
    // Studies below this combined similarity score are filtered out as non-matches (high originality).
    'minimum_score' => env('SIMILARITY_MIN_SCORE', 40),

    // Maximum number of ranked results to return
    'max_results' => env('SIMILARITY_MAX_RESULTS', 5),

    // Calibrated scoring weights for combining Semantic & Textual similarity
    'semantic_weight' => env('SIMILARITY_SEMANTIC_WEIGHT', 0.60),
    'textual_weight' => env('SIMILARITY_TEXTUAL_WEIGHT', 0.40),

    // Cross-field weights: primary matching field vs supportive fields
    'primary_field_weight' => env('SIMILARITY_PRIMARY_FIELD_WEIGHT', 0.85),
    'supportive_field_weight' => env('SIMILARITY_SUPPORTIVE_FIELD_WEIGHT', 0.15),
];
