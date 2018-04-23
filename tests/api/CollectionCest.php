<?php
namespace Kronofoto\Test;

require_once 'ControllerCest.php';

use ApiTester;

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
            'yearMin' => 'integer|null',
            'yearMax' => 'integer|null',
            'itemCount' => 'integer',
            'created' => 'string',
            'modified' => 'string',
            'featuredItemIdentifier' => 'string|null',
            'donorId' => 'integer',
            'donorFirstName' => 'string',
            'donorLastName' => 'string'
        ];
    }

    public function testGetItemCollection(ApiTester $I)
    {    
        $I->wantTo("get a collecton by an item's identifier");
        $identifier = 'FI001262';
        $url = $this->baseUrl . "items/$identifier/collection";

        $I->sendGET($url);
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals(3, $data[0]);
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
        $this->runTestPaging($I, 42, 10, 'id', 97, 330);
    }

    public function testPagingHeaders(ApiTester $I) 
    {
        $offset = 270;
        $limit = 10;
        $expected_totalRecords   = 278;
        $expected_firstRecord    = 271;
        $expected_lastRecord     = 278;
        $expected_totalPages     = 28;
        $expected_pageSize       = 10;
        $expected_currPageSize   = 8;
        $expected_currPageNumber = 28;
        $this->runTestPagingHeaders(
            $I, 
            $offset, 
            $limit,
            $expected_totalRecords,
            $expected_firstRecord,
            $expected_lastRecord,
            $expected_totalPages, 
            $expected_pageSize,
            $expected_currPageSize,
            $expected_currPageNumber);
    }
    public function runTestSortYearMinAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'yearMin', false);
    }

    public function runTestSortYearMinDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'yearMin', true);
    }

    public function runTestSortYearMaxAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'yearMax', false);
    }

    public function runTestSortYearMaxDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'yearMax', true);
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

    public function runTestSortDonorIdAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'donorId', false);
    }

    public function runTestSortDonorIdDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'donorId', true);
    }
}
