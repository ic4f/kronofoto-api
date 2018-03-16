<?php

class CollectionsCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/collections';
    const COLLECTIONS_ALL = 296;

    public function testJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET(self::URL); 
        $this->checkJson($I);
    }

    public function testRecordCount(ApiTester $I)
    {
        $count = self::COLLECTIONS_ALL;
        $I->wantTo("get $count collections");
        $I->sendGET(self::URL); 
        $this->checkJson($I);

        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($count, count($data));
    }

    public function testDataFormat(ApiTester $I)
    {
        $I->wantTo('get data in correct format');
        $I->sendGET(self::URL); 
        $this->checkJson($I);

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

    public function testSortingYearMin(ApiTester $I) 
    {
        $I->wantTo('get sorted by year_min');
        $I->sendGET(self::URL . '?sort=year_min'); 
        $this->checkJson($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');

        $this->checkIsSorted($I, $data, 'year_min');
    }


    //public function testSortingYearMinAcs(ApiTester $I) 
    //{
    //    $I->wantTo('get sorted by year_min; ascending order');
    //    $I->sendGET(self::URL . '?sort=+year_min'); 
    //    $this->checkJson($I);
    //    $data = $I->grabDataFromResponseByJsonPath('$*');

    //    $this->checkIsSorted($I, $data, 'year_min');
    //}


    //public function testSortingYearMinDesc(ApiTester $I) 
    //{
    //    $I->wantTo('get sorted by year_min; descending order');
    //    $I->sendGET(self::URL . '?sort=-year_min'); 
    //    $this->checkJson($I);
    //    $data = $I->grabDataFromResponseByJsonPath('$*');

    //    $this->checkIsSorted($I, $data, 'year_min');
    //}

    private function checkIsSorted(ApiTester $I, $recordset, $col)
    {
        $current = $recordset[0][$col];
        foreach ($recordset as $row) {
            $next = $row[$col];
            $I->assertLessThanOrEqual($current, $next);
            $current = $next;
        }
    } 

    private function checkJson(ApiTester $I) 
    {
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }
}
