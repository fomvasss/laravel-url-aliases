<?php

return [

    /*
     * Default url-alias model
     */
    'model' => \Fomvasss\UrlAliases\Models\UrlAlias::class,

    /*
     * If visit systep path.
     * [301|302|false]
     */
    'redirect_for_system_path' => 301,

    /*
     * If empty - available all methods.
     */
    'available_methods' => ['GET'],

    /*
     * Do not apply aliases for paths.
     */
    'ignore_paths' => [
        'admin/*',
        '*download*',
    ],
    
    /*
     * For scope UrlA() - if aliased path not exists.
     */
    'if_url_a_is_empty' => '/',
];
