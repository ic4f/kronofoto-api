<?php
namespace Kronofoto\Test;

use ApiTester;

class CollectionsCest
{
    //TODO: move these out into a helper class or a config location
    const URL = '/collections';

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
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
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

    public function testMaxRecordCount(ApiTester $I)
    {
        //this will work ONLY if there is a limit param:
        //max_records is NOT the default page size - it's a guard against
        //a page that is too large.
        $count = (int)$this->container['settings']['paging']['max_records'];
        $I->wantTo("get not more than $count records");
        $I->sendGET(self::URL . "?limit=999999"); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($count, count($data));
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
