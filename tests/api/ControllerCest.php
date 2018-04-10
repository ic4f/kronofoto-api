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

    protected function runTestReadAnother(ApiTester $I, $id, $expectedFields)
    {
        $I->wantTo("get another record by id");
        $I->sendGET($this->getURL() . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
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
        $expected_total_records)
    {

        //TODO
        $expected_pages = 28;
        $expected_pagesize = 7;
        $expected_page = 28;
        $expected_first = 271;
        $expected_last = 277;

        $I->wantTo("get paging info via http headers");
        $I->sendGET($this->getURL() . "?offset=$offset&limit=$limit"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');

        $I->seeHttpHeader(HttpHelper::PAGING_RECORDS, $expected_total_records);
        $I->seeHttpHeader(HttpHelper::PAGING_PAGES, $expected_pages);
        $I->seeHttpHeader(HttpHelper::PAGING_PAGESIZE, $expected_pagesize);
        $I->seeHttpHeader(HttpHelper::PAGING_PAGE, $expected_page);
        $I->seeHttpHeader(HttpHelper::PAGING_FIRST, $expected_first);
        $I->seeHttpHeader(HttpHelper::PAGING_LAST, $expected_last);

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
