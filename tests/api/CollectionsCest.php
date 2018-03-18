<?php

class CollectionsCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/collections';
    //TODO: how do I pull this from the app's config?
    const MAX_RECORDS = 100;

    //all records are published (is_published = 1), unless noted otherwise
    public function testResponseIsJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET(self::URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    public function testMaxRecordCount(ApiTester $I)
    {
        $count = self::MAX_RECORDS; //otherwise would be 101
        $I->wantTo("get not more than $count records");
        $I->sendGET(self::URL); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($count, count($data));
    }

    public function testDataStructure(ApiTester $I)
    {
        $I->wantTo("check the structure of a record");
        $I->sendGET(self::URL); 

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'year_min' => 'integer|null',
            'year_max' => 'integer|null',
            'item_count' => 'integer',
            'is_published' => 'integer',
            'created' => 'string',
            'modified' => 'string',
            'featured_item_id' => 'integer|null',
            'donor_id' => 'integer',
            'donor_first_name' => 'string',
            'donor_last_name' => 'string'
        ], '$*');
    }

    public function testPaging(ApiTester $I) 
    {
        $offset = 42;
        $limit = 10;
        $sort_by = 'id';
        $expected_first_id = 51;
        $expected_last_id = 60;
        $I->wantTo("get $limit records starting after record # $offset");
        $I->sendGET(self::URL . "?offset=$offset&limit=$limit"); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($limit, count($data));
        $I->assertEquals($expected_first_id, $data[0]['id']);
        $I->assertEquals($expected_last_id, $data[$limit-1]['id']);
    }

    public function testFilterByYearMin(ApiTester $I) 
    {
        $yearMin = 1950;
        $expected = 19;
        $I->wantTo("filter records by criteria: year_min = $yearMin");
        $I->sendGET(self::URL . "?filter[year_min]=$yearMin");

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterByYearMax(ApiTester $I) 
    {
        $yearMax = 1950;
        $expected = 8;
        $I->wantTo("filter records by criteria: year_max = $yearMax");
        $I->sendGET(self::URL . "?filter[year_max]=$yearMax");

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
    }

    public function testFilterByYearMinAndMax(ApiTester $I) 
    {
        $yearMin = 1950;
        $yearMax = 1980;
        $expected = 6;
        $I->wantTo("filter records by criteria: year_min = $yearMin, year_max = $yearMax");;
        $I->sendGET(self::URL . "?filter[year_min]=$yearMin&filter[year_max]=$yearMax");

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expected, count($data));
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

    public function testSortedItemCountAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'item_count', false);
    }

    public function testSortedItemCountDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'item_count', true);
    }

    public function testSortedIsPublishedAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'is_published', false);
    }

    public function testSortedIsPublishedDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'is_published', true);
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
 
    public function testSortedDonorIdAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'donor_id', false);
    }

    public function testSortedDonorIdDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'donor_id', true);
    }
 
    public function testSortedFeaturedItemIdAcs(ApiTester $I) 
    {
        $this->testSorted($I, 'featured_item_id', false);
    }
 
    public function testSortedFeaturedItemIdDesc(ApiTester $I) 
    {
        $this->testSorted($I, 'featured_item_id', true);
    }
 

    /* ------------------- private ----------------------- */

    private function testSorted(ApiTester $I, $col, $isDesc)
    {
        $order = $isDesc ? 'descending' : 'ascending';
        $I->wantTo("get records sorted by $col in $order order");

        $desc = $isDesc ? '-' : '';
        $I->sendGET(self::URL . "?sort=$desc$col"); 

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
