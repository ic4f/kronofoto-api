<?php
namespace Kronofoto\Test;

use ApiTester;

class ItemCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/items';

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
        $expectedFields = 10;
        $I->wantTo("get one record by id");
        $id = 10;
        $I->sendGET(self::URL . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
        $I->assertEquals('FI000326', $data[1]);
        $I->assertEquals(2, $data[2]);
        $I->assertEquals(0, $data[3]);
        $I->assertEquals(0, $data[4]);
        $I->assertEquals(1917, $data[5]);
        $I->assertEquals(1917, $data[6]);
        $I->assertEquals(1, $data[7]);
        $I->assertEquals('2015-05-21 20:19:57', $data[8]);
        $I->assertEquals('2015-05-21 20:19:57', $data[9]);
    }

    public function testReadAnother(ApiTester $I)
    {
        $expectedFields = 10;
        $I->wantTo("get another record by id");
        $id = 20;
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
        $I->assertEquals('Requested item not found', $data[0]['message']);
        $I->assertEquals('Invalid id', $data[0]['detail']);
    }

    public function testDataStructure(ApiTester $I)
    {
        $I->wantTo("check the structure of a record");
        $I->sendGET(self::URL); 
        $this->checkResponseIsValid($I);

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'identifier' => 'string',
            'collection_id' => 'integer',
            'latitude' => 'integer|float',
            'longitude' => 'integer|float',
            'year_min' => 'integer|null',
            'year_max' => 'integer|null',
            'is_published' => 'integer',
            'created' => 'string',
            'modified' => 'string',
        ], '$*');
    }

    public function testFilterByIdentifier(ApiTester $I)
    {
        $identifier = 'FI00429';
        $expected = 10;
        $I->wantTo("get records with identifier starting with $identifier" );
        $I->sendGET(self::URL . "?filter[identifier]=$identifier"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }
    public function testFilterGetItemsBeforeYear(ApiTester $I)
    {
        $year = 1870;
        $expected = 16;
        $I->wantTo("get items dated $year or earlier");
        $I->sendGET(self::URL . "?filter[before]=$year"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterGetItemsAfterYear(ApiTester $I)
    {
        $year = 1999;
        $expected = 28;
        $I->wantTo("get items dated $year or later");
        //add limit param to retrieve more than default
        $I->sendGET(self::URL . "?limit=100&filter[after]=$year"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterGetItemsBetweenYears(ApiTester $I)
    {
        $year1 = 1901;
        $year2 = 1903;
        $expected = 10;
        $I->wantTo("get items dated between $year1 and $year2");
        $I->sendGET(self::URL . "?filter[after]=$year1&filter[before]=$year2"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterGetItemsForYear(ApiTester $I)
    {
        $year = 1901;
        $expected = 3;
        $I->wantTo("get items dated $year");
        $I->sendGET(self::URL . "?filter[year]=$year"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }
  
    
    public function testPaging(ApiTester $I) 
    {
        $offset = 42;
        $limit = 10;
        $sort_by = 'id';
        $expected_first_id = 47;
        $expected_last_id = 56;
        $I->wantTo("get $limit records starting after record # $offset");
        $I->sendGET(self::URL . "?offset=$offset&limit=$limit"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($limit, count($data));
        $I->assertEquals($expected_first_id, $data[0]['id']);
        $I->assertEquals($expected_last_id, $data[$limit-1]['id']);
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

    public function testSortedIdAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'id', false);
    }

    public function testSortedIdDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'id', true);
    }
    public function testSortedIdentifierAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'identifier', false);
    }

    public function testSortedIdentifierDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'identifier', true);
    }

    public function testSortedCollectionAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'collection_id', false);
    }

    public function testSortedCollectionDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'collection_id', true);
    }

    public function testSortedLatitudeAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'latitude', false);
    }

    public function testSortedLatitudeDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'latitude', true);
    }

    public function testSortedLongitudeAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'longitude', false);
    }

    public function testSortedLongitudeDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'longitude', true);
    }

    public function testSortedYearMinAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'year_min', false);
    }

    public function testSortedYearMinDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'year_min', true);
    }

    public function testSortedYearMaxAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'year_max', false);
    }

    public function testSortedYearMaxDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'year_max', true);
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

