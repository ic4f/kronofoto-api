<?php
namespace Kronofoto\Test;

use Kronofoto\PagingHelper;

class PagingHelperTest extends \Codeception\Test\Unit
{
    /* test totalPages */
    public function testTotalPages_DivEven()
    {
        $records = 100;
        $pageSize = 20;
        $expected = 5;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getPageCount());
    }

    public function testTotalPages_DivNotEven1()
    {
        $records = 99;
        $pageSize = 20;
        $expected = 5;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getPageCount());
    }

    public function testTotalPages_DivNotEven2()
    {
        $records = 101;
        $pageSize = 20;
        $expected = 6;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getPageCount());
    }

    public function testTotalPages_PageSizeLargerThanRecords()
    {
        $records = 19;
        $pageSize = 20;
        $expected = 1;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getPageCount());
    }

    public function testTotalPages_noRecords()
    {
        $records = 0;
        $pageSize = 20;
        $expected = 0;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getPageCount());
    }

    /* test currentPage */
    public function testCurrentPage_offset0()
    {
        $records = 100;
        $pageSize = 20;
        $offset = 0;
        $expected = 1;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPage());
    }

    public function testCurrentPage_offsetLessThanPage()
    {
        $records = 100;
        $pageSize = 20;
        $offset = 19;
        $expected = 2;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPage());
    }

    public function testCurrentPage_offsetEqualsPage()
    {
        $records = 100;
        $pageSize = 20;
        $offset = 20;
        $expected = 2;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPage());
    }

    public function testCurrentPage_offsetGreaterThanPage()
    {
        $records = 100;
        $pageSize = 20;
        $offset = 21;
        $expected = 3;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPage());
    }

    public function testCurrentPage_offsetGreaterThanRecords()
    {
        $records = 100;
        $pageSize = 20;
        $offset = 100; 
        $expected = 1; //if offset is >= records, page number is 1
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPage());
    }

    public function testCurrentPageSize_pageSameAsPageSize()
    {
        $records = 20;
        $pageSize = 10;
        $expected = $pageSize;
        $paging = new PagingHelper($records, $pageSize);
        //     $this->assertEquals($expected, $paging->getCurrentPageSize());
    }

    public function testCurrentPageSize_pageLessThanPageSize()
    {
        $records = 12;
        $pageSize = 10;
        $offset = 10; 
        $expected = 2;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPageSize());
    }

    public function testCurrentPageSize_0()
    {
        $records = 0;
        $pageSize = 10;
        $offset = 10;
        $expected = 0;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getCurrentPageSize());
    }

    public function testFirst_noOffset()
    {
        $records = 100;
        $pageSize = 10;
        $expected = 1;
        $paging = new PagingHelper($records, $pageSize);
        $this->assertEquals($expected, $paging->getFirstRecord());
    }

    public function testFirst_offset()
    {
        $records = 100;
        $pageSize = 10;
        $offset = 30; 
        $expected = 31;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getFirstRecord());
    }

    public function testLast_addPageSize()
    {
        $records = 100;
        $pageSize = 10;
        $offset = 30; 
        $expected = 40;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getLastRecord());
    }

    public function testLast_addLessThanPageSize()
    {
        $records = 95;
        $pageSize = 10;
        $offset = 90; 
        $expected = 95;
        $paging = new PagingHelper($records, $pageSize, $offset);
        $this->assertEquals($expected, $paging->getLastRecord());
    }
}
