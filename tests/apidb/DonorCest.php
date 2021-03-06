<?php
namespace Kronofoto\Test\APIDB;

require_once 'ControllerCest.php';

use ApiTester;

class DonorCest extends ControllerCest
{
    protected function getURLHelper()
    {
        return '/donors';
    }

    /* --------------- tests for one record ---------------- */

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 7;
        $I->wantTo("get one record by id");
        $id = 221;
        $I->sendGET($this->getURL() . "/$id"); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals($id, $data[0]);
        $I->assertEquals('Sergey', $data[1]);
        $I->assertEquals('Golitsynskiy', $data[2]);
        //$I->assertEquals(0, $data[3]); //use test db!
        //$I->assertEquals(0, $data[4]);
        $I->assertEquals('2015-05-28 16:47:40', $data[5]);
        $I->assertEquals('2015-05-28 16:47:40', $data[6]);
    }

    public function testReadInvalid(ApiTester $I)
    { 
        $this->runTestReadInvalid($I, 'donor', 'id');
    }

    /* --------------- tests for lists of records ---------------- */

    public function testGetAll(ApiTester $I)
    {
        $expected = 296;
        $I->wantTo("get all records");
        $I->sendGET("/alldonors");
        $this->checkNumberOfRecords($I, $expected);
    }


    public function testFilterByFirstName(ApiTester $I)
    {
        $first_name = 'sh';
        $expected = 7;
        $I->wantTo("get records with first name starting with $first_name");
        $I->sendGET($this->getURL() . "?filter[first_name]=$first_name"); 
        $this->checkNumberOfRecords($I, $expected);
    }

    public function testFilterByLastName(ApiTester $I)
    {
        $last_name = 'sch';
        $expected = 12;
        $I->wantTo("get records with last name starting with $last_name");
        $I->sendGET($this->getURL() . "?filter[last_name]=$last_name"); 
        $this->checkNumberOfRecords($I, $expected);
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
        $this->checkNumberOfRecords($I, $expected);
    }

    public function testPaging(ApiTester $I) 
    {
        $this->runTestPaging($I, 42, 10, 'userId', 326, 331);
    }

    public function runTestSortUserIdAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'userId', false);
    }

    public function runTestSortUserIdDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'userId', true);
    }

    public function runTestSortFirstNameAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'firstName', false);
    }

    public function runTestSortFirstNameDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'firstName', true);
    }

    public function runTestSortLastNameAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'lastName', false);
    }

    public function runTestSortLastNameDecs(ApiTester $I) 
    {
        $this->runTestSort($I, 'lastName', true);
    }

    public function runTestSortCollectionCountAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'collectionCount', false);
    }

    public function runTestSortCollectionCountDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'collectionCount', true);
    }

    public function runTestSortItemCountAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'itemCount', false);
    }

    public function runTestSortItemCountDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'itemCount', true);
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
