<?php
namespace Kronofoto\Test;

use Kronofoto\QueryStringHelper;

class QueryStringHelperTest extends \Codeception\Test\Unit
{
    protected $container; 

    protected function _before()
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    /* -------------------- test filtering ------------------ */
    /* TODO: implement for item, not collection.
    public function testHasFilterParam()
    {
        $params = array('a' => 'foo', 'filter' => array());
        $qs = new QueryStringHelper($params, $this->container);
        $this->assertTrue($qs->hasFilterParam());
    }

    public function testHasNoFilterParam()
    {
        $params = array('a' => 'foo');
        $qs = new QueryStringHelper($params, $this->container);
        $this->assertFalse($qs->hasFilterParam());
    }

    public function testFilterParamInvalid()
    {
        $this->expectException(\Exception::class);

        $filterArray = array('invalid' => '111');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $qs = new QueryStringHelper($params, $this->container);
        $qs->getFilterParams();
    }

    public function testFilterParamEmptyValue()
    {
        $this->expectException(\Exception::class);

        $filterArray = array('id' => '');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $qs = new QueryStringHelper($params, $this->container);
        $qs->getFilterParams();
    }

    public function testGetFilterParams()
    {
        $filterArray = array('id' => '111', 'donor_id' => '222');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $expected = array(
            ['field' => 'id', 'operator' => '=', 'value' => '111'],
            ['field' => 'donor_id', 'operator' => '=', 'value' => '222']
        );
        $qs = new QueryStringHelper($params, $this->container);
        $this->assertEquals($expected, $qs->getFilterParams());
    }

    public function testGetFilterMinMaxParams()
    {
        $filterArray = array('id' => '111', 'year_min' => '222', 'year_max' => '333');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $expected = array(
            ['field' => 'id', 'operator' => '=', 'value' => '111'],
            ['field' => 'year_min', 'operator' => '<=', 'value' => '222'],
            ['field' => 'year_max', 'operator' => '>=', 'value' => '333']
        );
        $qs = new QueryStringHelper($params, $this->container);
        $this->assertEquals($expected, $qs->getFilterParams());
    }
     */

    /* -------------------- test sorting -------------------- */
    public function testHasSortParam()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertTrue($qs->hasSortParam());
    }

    public function testHasNoSortParam()
    {
        $params = array('a' => 'foo', 'b' => 'bar');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertFalse($qs->hasSortParam());
    }

    public function testGetSortField()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => $sortField);
        $qs = new QueryStringHelper($params, array($sortField), $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetSortFieldWithoutDescendingOrder()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => '-' . $sortField);
        $qs = new QueryStringHelper($params, array($sortField), $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetAcsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals('ASC', $qs->getSortOrder());
    }

    public function testGetDecsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => '-id');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals('DESC', $qs->getSortOrder());
    }

    public function testSortFieldInvalid()
    {
        $this->expectException(\Exception::class);

        $params = array('a' => 'foo', 'sort' => 'invalid');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $qs->getSortField();
    }

    public function testSortFieldEmpty()
    {
        $this->expectException(\Exception::class);

        $params = array('a' => 'foo', 'sort' => '');
        $qs = new QueryStringHelper($params, array(), $this->container);
        $qs->getSortField();
    }

    /* -------------------- test paging -------------------- */
    
    public function testGetOffset()
    {
        $offset = 12;
        $params = array('offset' => $offset);
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals($offset, $qs->getOffset());
    }

    public function testGetDefaultOffset()
    {
        $params = array();
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals(0, $qs->getOffset());
    }

    public function testGetLimit()
    {
        $limit = 17;
        $params = array('limit' => $limit);
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals($limit, $qs->getLimit());
    }

    public function testGetDefaultLimit()
    {
        $defaultLimit = (int)$this->container['settings']['paging']['default_page_size'];
        $params = array();
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals($defaultLimit, $qs->getLimit());
    }

    public function testGetLimitCappedByMaxRecords()
    {
        $limit = 999999;
        $maxRecords = (int)$this->container['settings']['paging']['max_records'];
        $params = array('limit' => $limit);
        $qs = new QueryStringHelper($params, array(), $this->container);
        $this->assertEquals($maxRecords, $qs->getLimit());
    }
}
