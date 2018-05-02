<?php
namespace Kronofoto\Test\API;

require_once 'BaseCest.php';

use ApiTester;

class ItemCest extends BaseCest
{
    public function testRandomItem($I)
    {
        $url = '/items/random';
        $numberOfFields = 2;

        $I->wantTo("get random item: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'identifier' => 'string:' . $this->itemIdRegex,
        ]);
    }

    public function testItemByIdentifier($I)
    {
        $url = '/items/' . $this->itemIdentifier;
        $numberOfFields = 10;

        $I->wantTo("get item by item identifier: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields);
        $this->testItemStructure($I);
    }

    public function testItems($I)
    {
        $url = '/items';
        $numberOfFields = 10;

        $I->wantTo("get items: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields, $this->firstRowSelector);
        $this->testItemStructure($I);
    }

    public function testItemMetadata($I)
    {
        $url = '/items/' . $this->itemIdentifier . '/metadata';
        $numberOfFields = 3;

        $I->wantTo("get item metadata by item identifier: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields, $this->firstRowSelector);
        $I->seeResponseMatchesJsonType([
            'value' => 'string',
            'elementId' => 'integer:>0',
            'element' => 'string'
        ]);
    }

    private function testItemStructure($I)
    {
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'identifier' => 'string:' . $this->itemIdRegex,
            'collectionId' => 'integer:>0',
            'latitude' => 'integer|float',
            'longitude' => 'integer|float',
            'yearMin' => 'integer:>' . $this->yearMin,
            'yearMax' => 'integer:<' . $this->yearMax,
            'isPublished' => 'integer:'. $this->boolRegex,
            'created' => 'string:' . $this->dateRegex,
            'modified' => 'string:' . $this->dateRegex
        ]);
    }
}
