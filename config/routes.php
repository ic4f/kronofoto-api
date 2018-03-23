<?php
//get one record by id
$app->get('/items/{id}',             'Kronofoto\Controllers\ItemController:read');
$app->get('/donors/{id}',            'Kronofoto\Controllers\DonorController:read');
$app->get('/collections/{id}',       'Kronofoto\Controllers\CollectionController:read');
//
////all 'get records' queries accecpt optional querystrings
//
//items
$app->get('/items',                   'Kronofoto\Controllers\ItemController:getItems');
//$app->get('/donors/{id}/items',       'Kronofoto\ItemController:getDonorItems');
//$app->get('/collections/{id}/items',  'Kronofoto\ItemController:getCollectionItems');
//
//donors
$app->get('/donors',                  'Kronofoto\Controllers\DonorController:getDonors');

//collections
$app->get('/collections',             'Kronofoto\Controllers\CollectionController:getCollections');
//$app->get('/donors/{id}/collections', 'Kronofoto\CollectionController:getDonorCollections');
