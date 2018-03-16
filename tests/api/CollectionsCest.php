<?php

class CollectionsCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/collections';
    const COLLECTIONS_ALL = 296;

    public function testResponseIsJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET(self::URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    public function testRecordCount(ApiTester $I)
    {
        $count = self::COLLECTIONS_ALL;
        $I->wantTo("get $count records");
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
            'description' => 'string',
            'created' => 'string',
            'modified' => 'string',
            'donor_id' => 'integer',
            'featured_item_id' => 'integer|null'
        ], '$*');
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

        //TODO: datetime fields may require special processing
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



/*
    public function testSortingYearMaxAcs(ApiTester $I) 
    {
        $I->wantTo('get records sorted by year_max in acsending order');
        $I->sendGET(self::URL . '?sort=year_max'); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSortedAcs($I, $data, 'year_max');
    }

    public function testSortingYearMaxDesc(ApiTester $I) 
    {
        $I->wantTo('get records sorted by year_max in descending order');
        $I->sendGET(self::URL . '?sort=-year_max'); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSortedDesc($I, $data, 'year_max');
    }

    public function testSortingItemCountAcs(ApiTester $I) 
    {
        $I->wantTo('get records sorted by item_count in acsending order');
        $I->sendGET(self::URL . '?sort=item_count'); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSortedAcs($I, $data, 'item_count');
    }

    public function testSortingItemCountDesc(ApiTester $I) 
    {
        $I->wantTo('get records sorted by item_count in descending order');
        $I->sendGET(self::URL . '?sort=-item_count'); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $this->checkIsSortedDesc($I, $data, 'item_count');
    }

 */
    private function bcheckIsSorted(ApiTester $I, $recordset, $col, $isDesc)
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
