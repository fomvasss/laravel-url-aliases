<?php

return [

    'model' => \Fomvasss\UrlAliases\Models\UrlAlias::class,

    'redirect_for_system_path' => 301, // 301 | 302 | false

    'available_mathods' => ['GET'], // if empty - available all methods

    'ignore_paths' => [ // do not apply aliases for paths
        'admin/',
        '*download*',
    ]
];
