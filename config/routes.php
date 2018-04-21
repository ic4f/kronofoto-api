<?php
$base = '/api';

$app->get($base . '/items/{id}/metadata',    'Kronofoto\Controllers\ItemController:getItemMetadata');
//get one record by id or identifier (item)
$app->get($base . '/items/{identifier}',     'Kronofoto\Controllers\ItemController:read');
$app->get($base . '/donors/{id}',            'Kronofoto\Controllers\DonorController:read');
$app->get($base . '/collections/{id}',       'Kronofoto\Controllers\CollectionController:read');

//get metadata 


////all 'get records' queries accecpt optional querystrings
//
//items
$app->get($base . '/items',                   'Kronofoto\Controllers\ItemController:getItems');
//$app->get($base . '/donors/{id}/items',       'Kronofoto\ItemController:getDonorItems');
//$app->get($base . '/collections/{id}/items',  'Kronofoto\ItemController:getCollectionItems');
//
//donors
$app->get($base . '/donors',                  'Kronofoto\Controllers\DonorController:getDonors');
$app->get($base . '/alldonors',               'Kronofoto\Controllers\DonorController:getAllDonors');

//collections
$app->get($base . '/collections',             'Kronofoto\Controllers\CollectionController:getCollections');
//$app->get($base . '/donors/{id}/collections', 'Kronofoto\CollectionController:getDonorCollections');
//
$app->get($base . '/page/{slug}',               'Kronofoto\Controllers\PageController:read');
