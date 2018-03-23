<?php
namespace Kronofoto\Test;

use Kronofoto\QueryStringHelper;
use Kronofoto\Models\Model;


class QueryStringHelperTest extends \Codeception\Test\Unit
{
    protected $container; 

    protected function _before()
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    /* -------------------- test filtering ------------------ */
    public function testHasFilterParam()
    {
        $params = array('a' => 'foo', 'filter' => array());
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertTrue($qs->hasFilterParam());
    }

    public function testHasNoFilterParam()
    {
        $params = array('a' => 'foo');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertFalse($qs->hasFilterParam());
    }

    public function testFilterParamInvalid()
    {
        $this->expectException(\Exception::class);

        $filterArray = array('invalid' => '111');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $model = $this->getMockModel();
        $model->method('validateFilter')->will($this->throwException(new \Exception));
        $qs = new QueryStringHelper($params, $model, $this->container);
        $qs->getFilterParams();
    }

    public function testFilterParamEmptyValue()
    {
        $this->expectException(\Exception::class);

        $filterArray = array('f1' => '');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $qs->getFilterParams();
    }

    public function testGetFilterParams()
    {
        $filterArray = array('f1' => '111', 'f2' => '222');
        $params = array('a' => 'foo', 'filter' => $filterArray);
        $model = $this->getMockModel();
        $expected = array(
            ['key' => 'f1', 'operator' => '=', 'value' => '111'],
            ['key' => 'f2', 'operator' => '=', 'value' => '222']
        );
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($expected, $qs->getFilterParams());
    }

/* -------------------- test sorting -------------------- */
    
    public function testHasSortParam()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertTrue($qs->hasSortParam());
    }

    public function testHasNoSortParam()
    {
        $params = array('a' => 'foo', 'b' => 'bar');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertFalse($qs->hasSortParam());
    }

    public function testGetSortField()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => $sortField); 
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetSortFieldWithoutDescendingOperator()
    {
        $sortField = 'id';
        $params = array('a' => 'foo', 'sort' => '-' . $sortField);
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($sortField, $qs->getSortField());
    }

    public function testGetAcsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => 'id');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals('ASC', $qs->getSortOrder());
    }

    public function testGetDecsendingSortOrder()
    {
        $params = array('a' => 'foo', 'sort' => '-id');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals('DESC', $qs->getSortOrder());
    }
 
    public function testSortFieldInvalid()
    {
        $this->expectException(\Exception::class);

        $params = array('a' => 'foo', 'sort' => 'invalid');
        $model = $this->getMockModel();
        $model->method('validateSort')->will($this->throwException(new \Exception));
        $qs = new QueryStringHelper($params, $model, $this->container);
        $qs->getSortField();
    }

    public function testSortFieldEmpty()
    {
        $this->expectException(\Exception::class);

        $params = array('a' => 'foo', 'sort' => '');
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $qs->getSortField();
    }

/* -------------------- test paging -------------------- */
    public function testGetOffset()
    {
        $offset = 12;
        $params = array('offset' => $offset);
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($offset, $qs->getOffset());
    }

    public function testGetDefaultOffset()
    {
        $params = array();
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals(0, $qs->getOffset());
    }

    public function testGetLimit()
    {
        $limit = 17;
        $params = array('limit' => $limit);
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($limit, $qs->getLimit());
    }

    public function testGetDefaultLimit()
    {
        $defaultLimit = (int)$this->container['settings']['paging']['default_page_size'];
        $params = array();
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($defaultLimit, $qs->getLimit());
    }

    public function testGetLimitCappedByMaxRecords()
    {
        $limit = 999999;
        $maxRecords = (int)$this->container['settings']['paging']['max_records'];
        $params = array('limit' => $limit);
        $model = $this->getMockModel();
        $qs = new QueryStringHelper($params, $model, $this->container);
        $this->assertEquals($maxRecords, $qs->getLimit());
    }

    private function getMockModel()
    {
        return $this->createMock(Model::class);
    }
}
