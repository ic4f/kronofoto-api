<?php
////get one record by id
//$app->get('/items/{id}',             'Kronofoto\ItemController:read');
//$app->get('/donors/{id}',            'Kronofoto\DonorController:read');
//$app->get('/collections/{id}',       'Kronofoto\CollectionController:read');
//
////all 'get records' queries accecpt optional querystrings
//
////items
//$app->get('/items',                   'Kronofoto\ItemController:getItems');
//$app->get('/donors/{id}/items',       'Kronofoto\ItemController:getDonorItems');
//$app->get('/collections/{id}/items',  'Kronofoto\ItemController:getCollectionItems');
//
////donors
//$app->get('/donors',                  'Kronofoto\DonorController:getDonors');
//
////collections
$app->get('/collections',             'Kronofoto\CollectionController:getCollections');
//$app->get('/donors/{id}/collections', 'Kronofoto\CollectionController:getDonorCollections');
