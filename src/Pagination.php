<?php
namespace Kronofoto;

class Pagination
{
    private $totalRecords;
    private $firstRecord;
    private $lastRecord;
    private $totalPages;
    private $pageNumber;

    /* can assume:
     * offset >= 0 
     * limit > 0 
     * limit <= max allowed page size 
     * totalRecords >= 0
     */

    /* 
     * ON ARBITRARY OFFSETS AND COMMON SENSE: 
     * currentPageNumber and totalPages makes sense only if the limit divides the offset evenly.
     * For example, if offset=20, limit=10, we are on page 3 (items 21-30). 
     * However, if offset=19, it is not clear that we are on page 2 (items 20-29). Because if we
     * try to go go the previous page, we'll get a smaller page (size=9: items 1-9) - and that makes
     * little sense, as we expect that only the last page can be smaller since we move (in pages) 
     * from left to right. 
     * Therefore, if offset % limit != 0, consider the records NOT paged (just a subset), 
     * and return -1 for both currentPageNumber and totalPages.
     */ 
    public function __construct($offset, $limit, $totalRecords)
    {
        $this->totalRecords = $totalRecords;
        $this->data = array();
        $this->loadData($offset, $limit);
    }

    public function totalRecords() { return $this->totalRecords; }

    public function firstRecord() { return $this->firstRecord; }

    public function lastRecord() { return $this->lastRecord; }

    public function totalPages() { return $this->totalPages; }

    public function currentPageSize() { return $this->pageSize; }

    public function currentPageNumber() { return $this->pageNumber; }

    //TODO: refactor this logic
    private function loadData($offset, $limit) 
    {
        if ($offset >= $this->totalRecords) {
            $this->firstRecord = 0;
            $this->lastRecord = 0;
            $this->totalPages = 1; //1 page with 0 records
            $this->pageSize = 0;
            $this->pageNumber = 1;
        } else {
            $this->firstRecord = $offset + 1;
            $this->lastRecord = min($offset + $limit, $this->totalRecords);
            $this->pageSize = $this->lastRecord - $this->firstRecord + 1;

            // see top of file: note on arbitrary offsets 
            if ($offset % $limit == 0) {
                $this->totalPages = ceil($this->totalRecords / $limit);
                $this->pageNumber = ceil($offset / $limit) + 1;
            } else {
                $this->totalPages = -1;
                $this->pageNumber = -1;
            }
        }
    }
}
