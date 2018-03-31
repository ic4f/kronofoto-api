<?php
$base = '/api';

//get one record by id
$app->get($base . '/items/{id}',             'Kronofoto\Controllers\ItemController:read');
$app->get($base . '/donors/{id}',            'Kronofoto\Controllers\DonorController:read');
$app->get($base . '/collections/{id}',       'Kronofoto\Controllers\CollectionController:read');
//
////all 'get records' queries accecpt optional querystrings
//
//items
$app->get($base . '/items',                   'Kronofoto\Controllers\ItemController:getItems');
//$app->get($base . '/donors/{id}/items',       'Kronofoto\ItemController:getDonorItems');
//$app->get($base . '/collections/{id}/items',  'Kronofoto\ItemController:getCollectionItems');
//
//donors
$app->get($base . '/donors',                  'Kronofoto\Controllers\DonorController:getDonors');

//collections
$app->get($base . '/collections',             'Kronofoto\Controllers\CollectionController:getCollections');
//$app->get($base . '/donors/{id}/collections', 'Kronofoto\CollectionController:getDonorCollections');
