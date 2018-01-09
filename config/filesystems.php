<?php

return [
    'default' => 'local',
    'cloud' => 's3',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],
        's3' => [
            'driver' => 's3',
            'key' => 'AKIAJLXU6MQ62S62Q7TA',
            'secret' => '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',
            'region' => 'sa-east-1',
            'bucket' => 'gofeels-images',
        ],
    ],
];