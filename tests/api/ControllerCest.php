<?php namespace Kronofoto\Test;

use ApiTester;

use Kronofoto\HttpHelper;

abstract class ControllerCest
{
    protected $container; 
    protected $baseUrl = '/api/';

    /* default base URL */
    protected abstract function getURLHelper();

    /* structure of a record which is part of a list 
     * return format: ['field1' => 'type', 'field2' => 'type', ... ]
     */
    protected abstract function getListDataStructure();

    public function _before(ApiTester $I)
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    public function testResponseIsJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET($this->getURL());
        $this->checkResponseIsValid($I);
    }

    /* --------------- tests for lists of records ---------------- */

    public function testListDataStructure(ApiTester $I)
    {
        $I->wantTo("check the structure of a record which is part of a list");
        $I->sendGET($this->getURL()); 
        $this->checkResponseIsValid($I);

        $ds = $this->getListDataStructure();
        $data = $I->grabDataFromResponseByJsonPath('$[0]');
        // check number of fields
        $I->assertEquals(count($ds), count($data[0]));
        // check field names and types
        $I->seeResponseMatchesJsonType($ds, '$[0]'); 
    }

    public function testMaxRecordCount(ApiTester $I)
    {
        //this will work ONLY if there is a limit param:
        //max_records is NOT the default page size - it's a guard against
        //a page that is too large.
        $count = (int)$this->container['settings']['paging']['max_records'];
        $I->wantTo("get not more than $count records");
        $I->sendGET($this->getURL() . "?limit=999999"); 
        $this->checkValidAndNumberOfRecords($I, $count);
    }

    /* --------------- helpers for subclasses ---------------- */

    protected function runTestReadAnother(ApiTester $I, $id, $expectedFields, $idIndex=0)
    {
        $I->wantTo("get another record by id");
        $I->sendGET($this->getURL() . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[$idIndex]);
    }

    protected function runTestReadInvalid(ApiTester $I, $record, $field)
    {
        $I->wantTo("see 404 status code and error data");
        $nonexistantId = 'invalid';
        $I->sendGET($this->getURL() . "/$nonexistantId"); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); //404
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals('404', $data[0]['status']);
        $I->assertEquals("Requested $record not found", $data[0]['message']);
        $I->assertEquals("Invalid $field", $data[0]['detail']);
    }

    protected function runTestPagingHeaders(
        ApiTester $I, 
        $offset, 
        $limit,
        $expected_totalRecords,
        $expected_firstRecord,
        $expected_lastRecord,
        $expected_totalPages, 
        $expected_pageSize,
        $expected_currPageSize,
        $expected_currPageNumber)
    {
        $I->wantTo("get pagination data via http headers");
        $I->sendGET($this->getURL() . "?offset=$offset&limit=$limit"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');

        $I->seeHttpHeader(HttpHelper::PAGINATION_TOTAL_RECORDS, $expected_totalRecords);
        $I->seeHttpHeader(HttpHelper::PAGINATION_PAGE_SIZE, $expected_pageSize);
        $I->seeHttpHeader(HttpHelper::PAGINATION_TOTAL_PAGES, $expected_totalPages);
        $I->seeHttpHeader(HttpHelper::PAGINATION_FIRST_RECORD, $expected_firstRecord);
        $I->seeHttpHeader(HttpHelper::PAGINATION_LAST_RECORD, $expected_lastRecord);
        $I->seeHttpHeader(HttpHelper::PAGINATION_CURRENT_PAGE_NUMBER, $expected_currPageNumber);
        $I->seeHttpHeader(HttpHelper::PAGINATION_CURRENT_PAGE_SIZE, $expected_currPageSize);
    }

    protected function runTestPaging(
        ApiTester $I, 
        $offset, 
        $limit, 
        $id_column, 
        $expected_first_id, 
        $expected_last_id)
    {
        $I->wantTo("get $limit records starting after record # $offset");
        $I->sendGET($this->getURL() . "?offset=$offset&limit=$limit"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($limit, count($data));
        $I->assertEquals($expected_first_id, $data[0][$id_column]);
        $I->assertEquals($expected_last_id, $data[$limit-1][$id_column]);
    }

    protected function runTestSort(ApiTester $I, $col, $isDesc)
    {
        $order = $isDesc ? 'descending' : 'ascending';
        $I->wantTo("get records sorted by $col in $order order");

        $desc = $isDesc ? '-' : '';
        $I->sendGET($this->getURL() . "?sort=$desc$col"); 
        $this->checkResponseIsValid($I);

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSorted($I, $data, $col, $isDesc);
    }

    protected function checkResponseIsValid(ApiTester $I)
    {
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    protected function checkValidAndNumberOfRecords(ApiTester $I, $expected)
    {
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    protected function getURL()
    {
        return  $this->baseUrl . $this->getUrlHelper();
    }

    /* --------------- private ---------------- */

    private function checkIsSorted(ApiTester $I, $recordset, $col, $isDesc)
    {
        $previous = $isDesc ? 9999 : null;

        foreach ($recordset as $row) {
            $current= $row[$col];
            if ($isDesc) {
                $I->assertGreaterThanOrEqual($current, $previous);
            } else {
                $I->assertLessThanOrEqual($current, $previous);
            }
            $previous = $current;
        }
    }
}
