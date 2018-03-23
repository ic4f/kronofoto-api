<?php
namespace Kronofoto\Test;

require_once 'ControllerCest.php';

use ApiTester;
use Kronofoto\Controllers\DonorController;

class DonorCest extends ControllerCest
{
    /* ----------- required overrides ----------- */

    protected function getURL()
    {
        return '/donors';
    }

    protected function getListDataStructure()
    {
        return [
            'user_id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'collection_count' => 'integer',
            'item_count' => 'integer',
            'created' => 'string',
            'modified' => 'string'
        ];
    }

    /* --------------- tests for one record ---------------- */

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 7;
        $I->wantTo("get one record by id");
        $id = 221;
        $I->sendGET($this->getURL() . "/$id"); 
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
        $this->runTestReadAnother($I, 223, 7);
    }

    public function testReadInvalid(ApiTester $I)
    { 
        $this->runTestReadInvalid($I, 'donor', 'id');
    }


    /* --------------- tests for lists of records ---------------- */

    public function testFilterByFirstName(ApiTester $I)
    {
        $first_name = 'sh';
        $expected = 7;
        $I->wantTo("get records with first name starting with $first_name");
        $I->sendGET($this->getURL() . "?filter[first_name]=$first_name"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterByLastName(ApiTester $I)
    {
        $last_name = 'sch';
        $expected = 12;
        $I->wantTo("get records with last name starting with $last_name");
        $I->sendGET($this->getURL() . "?filter[last_name]=$last_name"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterByFirstAndLastName(ApiTester $I)
    {
        $first_name = 'm';
        $last_name = 's';
        $expected = 5;
        $I->wantTo("get records with last name starting with $last_name " .  
            "and first name starting with $first_name");
        $I->sendGET($this->getURL() . 
            "?filter[last_name]=$last_name&filter[first_name]=$first_name"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testPaging(ApiTester $I) 
    {
        $this->runTestPaging($I, 42, 10, 'user_id', 86, 101);
    }

    public function runTestSortUserIdAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'user_id', false);
    }

    public function runTestSortUserIdDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'user_id', true);
    }

    public function runTestSortFirstNameAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'first_name', false);
    }

    public function runTestSortFirstNameDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'first_name', true);
    }

    public function runTestSortLastNameAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'last_name', false);
    }

    public function runTestSortLastNameDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'last_name', true);
    }

    public function runTestSortCollectionCountAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'collection_count', false);
    }

    public function runTestSortCollectionCountDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'collection_count', true);
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
}
