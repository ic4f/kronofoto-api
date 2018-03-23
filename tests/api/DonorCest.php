<?php
namespace Kronofoto\Test;

use ApiTester;

class DonorCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/donors';

    private $container; 

    public function _before(ApiTester $I)
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    //all records are published (is_published = 1), unless noted otherwise
    public function testResponseIsJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET(self::URL); 
        $this->checkResponseIsValid($I);
    }

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 7;
        $I->wantTo("get one record by id");
        $id = 221;
        $I->sendGET(self::URL . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
        $I->assertEquals('Sergey', $data[1]);
        $I->assertEquals('Golitsynskiy', $data[2]);
        $I->assertEquals(0, $data[3]);
        $I->assertEquals(0, $data[4]);
        $I->assertEquals('2015-05-28 16:47:40', $data[5]);
        $I->assertEquals('2015-05-28 16:47:40', $data[6]);
    }

    public function testReadAnother(ApiTester $I)
    {
        $expectedFields = 7;
        $I->wantTo("get another record by id");
        $id = 223;
        $I->sendGET(self::URL . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
    }

    public function testReadInvalid(ApiTester $I)
    {
        $I->wantTo("see 404 status code and error data");
        $nonexistantId = 'invalid';
        $I->sendGET(self::URL . "/$nonexistantId"); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); //404
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals('404', $data[0]['status']);
        $I->assertEquals('Requested donor not found', $data[0]['message']);
        $I->assertEquals('Invalid id', $data[0]['detail']);
    }

    public function testDataStructure(ApiTester $I)
    {
        $I->wantTo("check the structure of a record");
        $I->sendGET(self::URL); 
        $this->checkResponseIsValid($I);

        $I->seeResponseMatchesJsonType([
            'user_id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'collection_count' => 'integer',
            'item_count' => 'integer',
            'created' => 'string',
            'modified' => 'string'
        ], '$*');
    }

    public function testFilterByFirstName(ApiTester $I)
    {
        $first_name = 'sh';
        $expected = 7;
        $I->wantTo("get records with first name starting with $first_name");
        $I->sendGET(self::URL . "?filter[first_name]=$first_name"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterByLastName(ApiTester $I)
    {
        $last_name = 'sch';
        $expected = 12;
        $I->wantTo("get records with last name starting with $last_name");
        $I->sendGET(self::URL . "?filter[last_name]=$last_name"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterByFirstAndLastName(ApiTester $I)
    {
        $first_name = 'm';
        $last_name = 's';
        $expected = 5;
        $I->wantTo("get records with last name starting with $last_name " .  
            "and first name starting with $first_name");
        $I->sendGET(self::URL . "?filter[last_name]=$last_name&filter[first_name]=$first_name"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }
  
    public function testPaging(ApiTester $I) 
    {
        $offset = 42;
        $limit = 10;
        $sort_by = 'user_id';
        $expected_first_id = 86;
        $expected_last_id = 101;
        $I->wantTo("get $limit records starting after record # $offset");
        $I->sendGET(self::URL . "?offset=$offset&limit=$limit"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($limit, count($data));
        $I->assertEquals($expected_first_id, $data[0]['user_id']);
        $I->assertEquals($expected_last_id, $data[$limit-1]['user_id']);
    }

    public function testMaxRecordCount(ApiTester $I)
    {
        $count = (int)$this->container['settings']['paging']['max_records'];
        $I->wantTo("get not more than $count records");
        $I->sendGET(self::URL . "?limit=999999"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($count, count($data));
    }

    public function testSortedUserIdAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'user_id', false);
    }

    public function testSortedUserIdDecs(ApiTester $I) 
    {
        $this->testSorted($I, 'user_id', true);
    }

    public function testSortedFirstNameAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'first_name', false);
    }

    public function testSortedFirstNameDecs(ApiTester $I) 
    {
        $this->testSorted($I, 'first_name', true);
    }

    public function testSortedLastNameAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'last_name', false);
    }

    public function testSortedLastNameDecs(ApiTester $I) 
    {
        $this->testSorted($I, 'last_name', true);
    }

    public function testSortedCollectionCountAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'collection_count', false);
    }

    public function testSortedCollectionCountDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'collection_count', true);
    }

    public function testSortedItemCountAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'item_count', false);
    }

    public function testSortedItemCountDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'item_count', true);
    }

    public function testSortedCreatedAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'created', false);
    }

    public function testSortedCreatedDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'created', true);
    }

    public function testSortedModifiedAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'modified', false);
    }

    public function testSortedModifiedDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'modified', true);
    }



    /* ------------------- private ----------------------- */

    private function checkResponseIsValid(ApiTester $I)
    {
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    private function testSorted(ApiTester $I, $col, $isDesc)
    {
        $order = $isDesc ? 'descending' : 'ascending';
        $I->wantTo("get records sorted by $col in $order order");

        $desc = $isDesc ? '-' : '';
        $I->sendGET(self::URL . "?sort=$desc$col"); 
        $this->checkResponseIsValid($I);

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSorted($I, $data, $col, $isDesc);
    }

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

