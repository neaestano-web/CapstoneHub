<?php

return [
    // Studies below this semantic similarity score are not shown in results.
    // Score bands in view: 40-59% (Related Study Found), 60-74% (Moderate Match), 75%+ (High Potential Match)
    'minimum_score' => env('SIMILARITY_MIN_SCORE', 40),
];
