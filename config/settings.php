<?php
//private settings stored here
$local = require ROOT_PATH . '/config/settings-local.php';

$main = [
    //TODO: change to false for production
    'addContentLengthHeader' => false
];

return [ 'settings' => array_merge($main, $local) ];
