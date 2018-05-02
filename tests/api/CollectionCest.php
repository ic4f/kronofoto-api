<?php
namespace Kronofoto\Test\API;

require_once 'BaseCest.php';

use ApiTester;

class CollectionCest extends BaseCest
{
    /* data-specific values (must be present in database */
    private $collectionId = 2;
    private $numberOfFields = 14;
    private $numberOfListFields = 11;

    public function testCollectionById($I)
    {
        $url = '/collections/' . $this->collectionId;
        $I->wantTo("get collection by id: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $this->numberOfFields);
        $this->testCollectionStructure($I);
    }

    public function testCollectionByItemIdentifier($I)
    {
        $url = '/items/' . $this->itemIdentifier . '/collection';
        $I->wantTo("get collection by item identifier: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $this->numberOfFields);
        $this->testCollectionStructure($I);
    }
 
    public function testCollections($I)
    {
        $url = '/collections';
        $I->wantTo("get collections: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $this->numberOfListFields, $this->firstRowSelector);
        $this->testCollectionListStructure($I);
    }

    private function testCollectionListStructure($I)
    {
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'name' => 'string|null',
            'yearMin' => 'integer:>' . $this->yearMin,
            'yearMax' => 'integer:<' . $this->yearMax,
            'itemCount' => 'integer:zeroOrGreater',
            'created' => 'string:' . $this->dateRegex,
            'modified' => 'string:' . $this->dateRegex,
            'featuredItemIdentifier' => 'string:' . $this->itemIdRegex . '|null',
            'donorId' => 'integer:>0',
            'donorFirstName' => 'string',
            'donorLastName' => 'string'
        ]);
    }
 
    private function testCollectionStructure($I)
    {
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'name' => 'string|null',
            'yearMin' => 'integer:>' . $this->yearMin,
            'yearMax' => 'integer:<' . $this->yearMax,
            'itemCount' => 'integer:zeroOrGreater',
            'isPublished' => 'integer:'. $this->boolRegex,
            'description' => 'string|null',
            'created' => 'string:' . $this->dateRegex,
            'modified' => 'string:' . $this->dateRegex,
            'featuredItemId' => 'integer:>0|null',
            'featuredItemIdentifier' => 'string:' . $this->itemIdRegex . '|null',
            'donorId' => 'integer:>0',
            'donorFirstName' => 'string',
            'donorLastName' => 'string'
        ]);
    }
} 
