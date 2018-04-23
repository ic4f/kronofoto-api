<?php
namespace Kronofoto\Test;

require_once 'ControllerCest.php';

use ApiTester;

class ItemCest extends ControllerCest
{
    /* ----------- required overrides ----------- */

    protected function getURLHelper()
    {
        return 'items';
    }

    protected function getListDataStructure()
    {
        return [
            'id' => 'integer',
            'identifier' => 'string',
            'collectionId' => 'integer',
            'latitude' => 'integer|float',
            'longitude' => 'integer|float',
            'yearMin' => 'integer|null',
            'yearMax' => 'integer|null',
            'isPublished' => 'integer',
            'created' => 'string',
            'modified' => 'string',
        ];
    }

    /* --------------- tests for one record ---------------- */

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 10;
        $I->wantTo("get one record by 'identifier' field");
        $identifier = 'FI000326';
        $I->sendGET($this->getURL() . "/$identifier"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals(10, $data[0]); 
        $I->assertEquals($identifier, $data[1]);
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
        $idIndex = 1; //not 0: se are using identifier field, not id
        $this->runTestReadAnother($I, 'FI000327', 10, $idIndex);
    }

    public function testReadInvalid(ApiTester $I)
    { 
        $this->runTestReadInvalid($I, 'item', 'id');
    }

    public function testGetRandomFeaturedItem(ApiTester $I)
    {    
        $I->wantTo("get a random featured item");
        $expectedFields = 2;
        $url = $this->getUrl() . "/random";

        $I->sendGET($url);
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
    }


    /* --------------- tests for metadata ---------------- */
    public function testGetMetadataForItem(ApiTester $I)
    {    
        $I->wantTo("get all metadata for one record");
        $identifier = 'FI001262';
        $expectedRecords = 6;
        $url = $this->getUrl() . "/$identifier/metadata";

        $I->sendGET($url);
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedRecords, count($data));
        $I->assertEquals('Durant', $data[1]['value']); 
        $I->assertEquals('IA', $data[2]['value']); 
    }




    /* --------------- tests for lists of records ---------------- */

    public function testFilterByCollection(ApiTester $I)
    {
        $collection = 2;
        $expected = 15;
        $I->wantTo("get items belonging to collection $collection" );
        $I->sendGET($this->getURL() . "?filter[collection]=$collection"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }
 
    public function testFilterByIdentifier(ApiTester $I)
    {
        $identifier = 'FI00429';
        $expected = 10;
        $I->wantTo("get records with identifier starting with $identifier" );
        $I->sendGET($this->getURL() . "?filter[identifier]=$identifier"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterGetItemsBeforeYear(ApiTester $I)
    {
        $year = 1870;
        $expected = 16;
        $I->wantTo("get items dated $year or earlier");
        $I->sendGET($this->getURL() . "?filter[before]=$year"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterGetItemsAfterYear(ApiTester $I)
    {
        $year = 1999;
        $expected = 28;
        $I->wantTo("get items dated $year or later");
        //add limit param to retrieve more than default
        $I->sendGET($this->getURL() . "?limit=100&filter[after]=$year"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterGetItemsBetweenYears(ApiTester $I)
    {
        $year1 = 1901;
        $year2 = 1903;
        $expected = 11;
        $I->wantTo("get items dated between $year1 and $year2");
        $I->sendGET($this->getURL() . "?filter[after]=$year1&filter[before]=$year2"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }

    public function testFilterGetItemsForYear(ApiTester $I)
    {
        $year = 1901;
        $expected = 3;
        $I->wantTo("get items dated $year");
        $I->sendGET($this->getURL() . "?filter[year]=$year"); 
        $this->checkValidAndNumberOfRecords($I, $expected);
    }
    
    public function testPaging(ApiTester $I) 
    {
        $this->runTestPaging($I, 42, 10, 'id', 2763, 2799);
    }

    public function runTestSortIdAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'id', false);
    }

    public function runTestSortIdDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'id', true);
    }
    public function runTestSortIdentifierAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'identifier', false);
    }

    public function runTestSortIdentifierDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'identifier', true);
    }

    public function runTestSortCollectionAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'collectionId', false);
    }

    public function runTestSortCollectionDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'collectionId', true);
    }

    public function runTestSortLatitudeAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'latitude', false);
    }

    public function runTestSortLatitudeDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'latitude', true);
    }

    public function runTestSortLongitudeAcs(ApiTester $I) 
    {
        $this->runTestSort($I, 'longitude', false);
    }

    public function runTestSortLongitudeDesc(ApiTester $I) 
    {
        $this->runTestSort($I, 'longitude', true);
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
