<?php
namespace Kronofoto\Models;

class CollectionModel extends Model
{
    protected function getSortCriteria() 
    {
        return [
            'id', 
            'name',
            'yearMin',
            'yearMax',
            'itemCount',
            'isPublished',
            'created',
            'modified',
            'donorId',
            'donorFirstName',
            'donorLastName'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [];
    }
}

