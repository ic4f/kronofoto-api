<?php
//private settings stored here
$local = require ROOT_PATH . '/config/settings-local.php';

$main = [
    //TODO: change to false for production
    'addContentLengthHeader' => false,
    'paging' => [
        'max_records' => 100,
        'default_page_size' => 20
    ]
];

return [ 'settings' => array_merge($main, $local) ];
