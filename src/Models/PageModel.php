<?php
namespace Kronofoto\Models;

class PageModel extends Model
{
    public function getName() { return 'page'; }
    
    protected function getSortCriteria() {}

    protected function getFilterCriteria() {}

    public function getDefaultSortField()
    {
        return Null;
    }

    public function getDefaultSortOrder()
    {
        return Null;
    }
}

