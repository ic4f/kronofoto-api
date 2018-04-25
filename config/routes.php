<?php
$base = $container['settings']['baseurl'];
//get random item 
$app->get($base . 
    '/items/random',
    'Kronofoto\Controllers\ItemController:getRandomFeaturedItem'
);


//get item by identifier
$app->get($base . 
    '/items/{identifier}',     
    'Kronofoto\Controllers\ItemController:read'
);

//get donor by id
$app->get($base . 
    '/donors/{id}',            
    'Kronofoto\Controllers\DonorController:read'
);

//get collection by id
$app->get($base . 
    '/collections/{id}',       
    'Kronofoto\Controllers\CollectionController:read'
);

//get collection by item identifier
$app->get($base . 
    '/items/{identifier}/collection',  
    'Kronofoto\Controllers\CollectionController:getItemCollection'
);

//get item metadata
$app->get($base . 
    '/items/{identifier}/metadata', 
    'Kronofoto\Controllers\ItemController:getItemMetadata'
);

//get page by address slug
$app->get($base . 
    '/page/{slug}',
    'Kronofoto\Controllers\PageController:read'
);

//all 'get records' queries accecpt optional querystrings

//get items
$app->get($base . 
    '/items',
    'Kronofoto\Controllers\ItemController:getItems'
);

//get donors
$app->get($base . 
    '/donors',
    'Kronofoto\Controllers\DonorController:getDonors'
);

//get all donors (no pagination)
$app->get($base . 
    '/alldonors',
    'Kronofoto\Controllers\DonorController:getAllDonors'
);

//get collections
$app->get($base . 
    '/collections',
    'Kronofoto\Controllers\CollectionController:getCollections'
);
