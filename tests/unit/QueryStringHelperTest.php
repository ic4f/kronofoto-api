<?php

class QueryStringHelperTest extends \Codeception\Test\Unit
{
    protected $container; 

    protected function _before()
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }


    /* -------------------- test paging -------------------- */
    public function testGetOffset()
    {
        $offset = 12;
        $params = array('offset' => $offset);
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($offset, $qs->getOffset());
    }

    public function testGetDefaultOffset()
    {
        $params = array();
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals(0, $qs->getOffset());
    }

    public function testGetLimit()
    {
        $limit = 17;
        $params = array('limit' => $limit);
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($limit, $qs->getLimit());
    }

    public function testGetDefaultLimit()
    {
        $defaultLimit = (int)$this->container['settings']['paging']['default_page_size'];
        $params = array();
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($defaultLimit, $qs->getLimit());
    }

    public function testGetLimitCappedByMaxRecords()
    {
        $limit = 999999;
        $maxRecords = (int)$this->container['settings']['paging']['max_records'];
        $params = array('limit' => $limit);
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($maxRecords, $qs->getLimit());
    }



    /* -------------------- test sorting -------------------- */
    public function testHasSortParam()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertTrue($qs->hasSortParam());
    }

    public function testHasNoSortParam()
    {
        $params = array('a' => 'foo', 'b' => 'bar');
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertFalse($qs->hasSortParam());
    }

    public function testGetSortField()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => $sortField);
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetSortFieldWithoutDescendingOrder()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => '-' . $sortField);
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetAcsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals('ASC', $qs->getSortOrder());
    }

    public function testGetDecsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => '-id');
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $this->assertEquals('DESC', $qs->getSortOrder());
    }

    public function testSortFieldInvalid()
    {
        $this->expectException(\Exception::class);

        $params = array('a' => 'foo', 'sort' => 'invalid');
        $qs = new Kronofoto\QueryStringHelper($params, $this->container);
        $qs->getSortField();
    }
}
