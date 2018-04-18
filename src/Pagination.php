<?php
namespace Kronofoto;

/* 
    Takes offset, limit, and totalRecords as input >>
    and calculates pagination values to be sent to the client.
    Offset and limit have been validated (but not against totalRecords).
 */
class Pagination
{
    private $totalRecords;  //overall
    private $pageSize;      //overall (not current page: current could be smaller if it's last)
    private $totalPages;    //overall (includes last page which may be smaller)

    private $firstRecord;    //on current page
    private $lastRecord;     //on current page
    private $currPageNumber; //current page
    private $currPageSize;   //current page

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
        $this->pageSize = $limit; //this has been validated
        $this->loadData($offset, $limit);
    }

    public function totalRecords() { return $this->totalRecords; }

    public function pageSize() { return $this->pageSize; }

    public function totalPages() { return $this->totalPages; }

    public function firstRecord() { return $this->firstRecord; }

    public function lastRecord() { return $this->lastRecord; }

    public function currentPageNumber() { return $this->currPageNumber; }

    public function currentPageSize() { return $this->currPageSize; }

    private function loadData($offset, $limit) 
    {
        //check if there's anything to display
        if ($offset >= $this->totalRecords) {
            $this->firstRecord = 0;
            $this->lastRecord = 0;
            $this->totalPages = 1; //1 page with 0 records
            $this->currPageNumber = 1;
            $this->currPageSize = 0;
        } else {
            $this->firstRecord = $offset + 1;
            $this->lastRecord = min($offset + $limit, $this->totalRecords);
            $this->currPageSize = $this->lastRecord - $this->firstRecord + 1;

            // see top of file: note on arbitrary offsets 
            if ($offset % $limit == 0) {
                $this->totalPages = ceil($this->totalRecords / $limit);
                $this->currPageNumber = ceil($offset / $limit) + 1;
            } else {
                $this->totalPages = -1;
                $this->currPageNumber = -1;
            }
        }
    }
}
