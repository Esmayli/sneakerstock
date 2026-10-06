<?php

return [
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif', 'max:51200'],
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma', 'avif',
        ],
    ],
];
