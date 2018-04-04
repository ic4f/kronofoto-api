<?php
namespace Kronofoto\Test;

require_once 'ControllerCest.php';

use ApiTester;
use Kronofoto\Controllers\CollectionController;

class CollectionCest extends ControllerCest
{
    /* ----------- required overrides ----------- */

    protected function getURLHelper()
    {
        return 'collections';
    }

    protected function getListDataStructure()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'year_min' => 'integer|null',
            'year_max' => 'integer|null',
            'item_count' => 'integer',
            'created' => 'string',
            'modified' => 'string',
            'featured_item_identifier' => 'string|null',
            'donor_id' => 'integer',
            'donor_first_name' => 'string',
            'donor_last_name' => 'string'
        ];
    }

    /* --------------- tests for one record ---------------- */

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 14;
        $I->wantTo("get one record by id");
        $id = 10;
        $I->sendGET($this->getURL() . "/$id"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
        $I->assertEquals('Bull and Schnell Family', $data[1]);
        $I->assertEquals(1919, $data[2]);
        $I->assertEquals(1965, $data[3]);
        $I->assertEquals(39, $data[4]);
        $I->assertEquals(1, $data[5]);
        $I->assertEquals('Ardith Bull and Theresa Ecklund', $data[6]);
        $I->assertEquals('2015-05-19 13:31:01', $data[7]);
        $I->assertEquals('2015-05-19 13:31:01', $data[8]);
        $I->assertEquals(135, $data[9]);
        $I->assertEquals('FI000005', $data[10]);
        $I->assertEquals(17, $data[11]);
        $I->assertEquals('Ardith', $data[12]);
        $I->assertEquals('Bull', $data[13]);
    }

    public function testReadAnother(ApiTester $I)
    {
        $this->runTestReadAnother($I, 20, 14);
    }

    public function testReadInvalid(ApiTester $I)
    { 
        $this->runTestReadInvalid($I, 'collection', 'id');
    }

    /* --------------- tests for lists of records ---------------- */

    public function testPaging(ApiTester $I) 
    {
        $this->runTestPaging($I, 42, 10, 'id', 45, 54);
    }

    public function runTestSortYearMinAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'year_min', false);
    }

    public function runTestSortYearMinDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'year_min', true);
    }

    public function runTestSortYearMaxAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'year_max', false);
    }

    public function runTestSortYearMaxDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'year_max', true);
    }

    public function runTestSortItemCountAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'item_count', false);
    }

    public function runTestSortItemCountDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'item_count', true);
    }

    public function runTestSortCreatedAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'created', false);
    }

    public function runTestSortCreatedDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'created', true);
    }

    public function runTestSortModifiedAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'modified', false);
    }

    public function runTestSortModifiedDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'modified', true);
    }

    public function runTestSortDonorIdAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'donor_id', false);
    }

    public function runTestSortDonorIdDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'donor_id', true);
    }
}
