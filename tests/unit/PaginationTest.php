<?php
namespace Kronofoto\Test\Unit;

use Kronofoto\Pagination;
//TODO add all assertions to all tests: we are testing validity of state after all.

class PaginationTest extends \Codeception\Test\Unit
{
    /* default values: use when they don't matter for member under test */
    const TOTAL  = 1000;
    const OFFSET = 0;
    const LIMIT  = 10;

    /* method names: 
     * _eq_: equals
     * _ne_: not equal
     * _gt_: greater than
     * _lt_: less than
     */

    /*
     * We can assume that limit and offset are valid because they
     * are extracted from the query string and validated at that point.
     */

    /*
     * Inlude asserts for all Pagination members in each test:
     * although testing one member per test, this will validate entire state
     */

    //------------------ test totalRecords ------------------//
    public function testTotalRecords()
    {
        $total = 100;
        $offset = self::OFFSET;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords()); //under test
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(10, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    //------------------ test firstRecord ------------------//
    public function testFirstRecord_offset_eq_0()
    {
        $total = self::TOTAL;
        $offset = 0;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(self::TOTAL, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord()); //under test
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(100, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    public function testFirstRecord_offset_gt_0()
    {
        $total = self::TOTAL;
        $offset = 1;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(self::TOTAL, $p->totalRecords());
        $this->assertEquals(2, $p->firstRecord()); //under test
        $this->assertEquals(11, $p->lastRecord());
        $this->assertEquals(-1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(-1, $p->currentPageNumber());
    }

    public function testFirstRecord_offset_eq_total()
    {
        $total = 1000;
        $offset = $total;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);
        
        $this->assertEquals(self::TOTAL, $p->totalRecords());
        $this->assertEquals(0, $p->firstRecord()); //under test
        $this->assertEquals(0, $p->lastRecord());
        $this->assertEquals(1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(0, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }
 
    public function testFirstRecord_total_eq_0()
    {
        $total = 0;
        $offset = self::OFFSET;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);
        
        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(0, $p->firstRecord()); //under test
        $this->assertEquals(0, $p->lastRecord());
        $this->assertEquals(1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(0, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    //------------------ test lastRecord ------------------//
    public function testLastRecord()
    {
        $total = self::TOTAL;
        $limit = 20;
        $offset = self::OFFSET;
        $p = new Pagination($offset, $limit, $total);
        
        $this->assertEquals(self::TOTAL, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(20, $p->lastRecord()); //under test
        $this->assertEquals(50, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(20, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    public function testLastRecord_limit_gt_total()
    {
        $total = 100;
        $limit = 101;
        $offset = self::OFFSET;
        $p = new Pagination($offset, $limit, $total);
        
        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(100, $p->lastRecord()); //under test
        $this->assertEquals(1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(100, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    public function testLastRecord_lt_offsetPlusLimit()
    {
        $total = 100;
        $offset = 95;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);
        
        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(96, $p->firstRecord());
        $this->assertEquals(100, $p->lastRecord()); //under test
        $this->assertEquals(-1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(5, $p->currentPageSize());
        $this->assertEquals(-1, $p->currentPageNumber());
    }

    //------------------ test totalPages ------------------//
    public function testTotalPages()
    {
        $total = 100;
        $offset = self::OFFSET;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(10, $p->totalPages()); //under test
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    public function testTotalPages_lastPageSmaller()
    {
        $total = 99;
        $offset = self::OFFSET;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(10, $p->totalPages()); //under test
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }

    public function testTotalPages_0()
    {
        $total = 0;
        $offset = self::OFFSET;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(0, $p->firstRecord());
        $this->assertEquals(0, $p->lastRecord());
        $this->assertEquals(1, $p->totalPages()); //under test
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(0, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber());
    }
    
    //------------------ test pageSize ------------------//
    //TODO
    //
    //------------------ test currentPageSize ------------------//
    public function testPageSize_ne_currPageSize()
    {
        $total = 17;
        $offset = 10;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);
        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(11, $p->firstRecord());
        $this->assertEquals(17, $p->lastRecord());
        $this->assertEquals(2, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize()); //under test
        $this->assertEquals(7, $p->currentPageSize()); 
        $this->assertEquals(2, $p->currentPageNumber());
    }

    //------------------ test currentPageSize ------------------//
    public function testCurrentPageSize()
    {
        $total = 100;
        $offset = self::OFFSET;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(10, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize()); //under test
        $this->assertEquals(1, $p->currentPageNumber());
    }
  
    public function testCurrentPageSize_lastPage()
    {
        $total = 95;
        $offset = 90;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals($total, $p->totalRecords());
        $this->assertEquals(91, $p->firstRecord());
        $this->assertEquals(95, $p->lastRecord());
        $this->assertEquals(10, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(5, $p->currentPageSize()); //under test
        $this->assertEquals(10, $p->currentPageNumber());
    }
  
   
    //------------------ test currentPageNumber ------------------//
    public function testCurrentPageNumber()
    {
        $total = self::TOTAL;
        $offset = 0;
        $limit = self::LIMIT;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(1000, $p->totalRecords());
        $this->assertEquals(1, $p->firstRecord());
        $this->assertEquals(10, $p->lastRecord());
        $this->assertEquals(100, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(1, $p->currentPageNumber()); //under test
    }

    public function testCurrentPageNumber_offset_lt_limit()
    {
        $total = 100;
        $offset = 5;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(100, $p->totalRecords());
        $this->assertEquals(6, $p->firstRecord());
        $this->assertEquals(15, $p->lastRecord());
        $this->assertEquals(-1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(-1, $p->currentPageNumber()); //under test
    }
   
    public function testCurrentPageNumber_offset_eq_limit()
    {
        $total = self::TOTAL;
        $offset = 10;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(1000, $p->totalRecords());
        $this->assertEquals(11, $p->firstRecord());
        $this->assertEquals(20, $p->lastRecord());
        $this->assertEquals(100, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(2, $p->currentPageNumber()); //under test
    }
    
    public function testCurrentPageNumber_offset_gt_limit()
    {
        $total = self::TOTAL;
        $offset = 15;
        $limit = 10;
        $p = new Pagination($offset, $limit, $total);

        $this->assertEquals(1000, $p->totalRecords());
        $this->assertEquals(16, $p->firstRecord());
        $this->assertEquals(25, $p->lastRecord());
        $this->assertEquals(-1, $p->totalPages());
        $this->assertEquals($limit, $p->pageSize());
        $this->assertEquals(10, $p->currentPageSize());
        $this->assertEquals(-1, $p->currentPageNumber()); //under test
    }
}
