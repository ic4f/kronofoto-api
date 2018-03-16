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

        //TODO: these lines should be included in all test
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    public function testRecordCount(ApiTester $I)
    {
        $count = self::COLLECTIONS_ALL;
        $I->wantTo("get $count collections");
        $I->sendGET(self::URL); 

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();

        //now check the data
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($count, count($data));
    }

    public function testDataFormat(ApiTester $I)
    {
        $I->wantTo('get data in correct format');
        $I->sendGET(self::URL); 

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();

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
}
