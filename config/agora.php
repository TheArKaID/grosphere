<?php

return [
    'backend_url' => env('AGORA_BACKEND_URL', 'https://backmeet.grosphere.app'),
    'frontend_url' => env('AGORA_FRONTEND_URL', 'https://meet.grosphere.app'),
    'key' => env('AGORA_KEY', ''),
    'enable_pstn' => env('AGORA_ENABLE_PSTN', false),
];
