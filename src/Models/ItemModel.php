<?php
namespace Kronofoto\Models;

class ItemModel extends Model
{
    public function getName() { return 'item'; }

    protected function getSortCriteria() 
    {
        return [
            'id',
            'identifier',
            'collectionId',
            'latitude',
            'longitude',
            'yearMin',
            'yearMax',
            'created',
            'modified'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [
            'identifier',
            'year',
            'before',
            'after',
            'collection'
        ];
    }
}

