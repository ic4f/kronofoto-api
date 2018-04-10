<?php

namespace Kronofoto;

class PagingHelper
{
    private $records;
    private $pageSize;
    private $offset;

    //For performance sake: assume args are valid 
    //  (records >= 0, pageSize > 0)
    public function __construct($records, $pageSize, $offset=0)
    {
        $this->records = $records;
        $this->pageSize = $pageSize;
        $this->offset = $offset;
    }

    public function getPageCount()
    {
        return ceil($this->records / $this->pageSize); 
    }

    public function getCurrentPage()
    {
        if ($this->offset >= $this->records) {
            return 1; //if there's nothing to display, the current page is 1
        } else {
            return ceil($this->offset / $this->pageSize) + 1;
        }
    }

    public function getCurrentPageSize()
    {
        $tmp = min($this->pageSize, ($this->records - $this->offset));
        return max(0, $tmp); //can't be negative
    }

    public function getFirstRecord()
    {
        return $this->offset + 1;
    }

    public function getLastRecord()
    {
        return min($this->records, $this->getCurrentPage() * $this->pageSize);
    }
}

