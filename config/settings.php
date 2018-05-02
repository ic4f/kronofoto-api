<?php
//private settings stored here
$local = require ROOT_PATH . '/config/settings-local.php';

$main = [
    //TODO: change to false for production
    'addContentLengthHeader' => false,
    'paging' => [
        'max_records' => 100,
        'default_page_size' => 20
    ],
    'data' => [
        'year_min' => 1800,
        'year_max' => 2000,
        'item_id_regex' => '^FI\d{6}$'
    ]
];

return [ 'settings' => array_merge($main, $local) ];
