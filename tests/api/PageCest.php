<?php
namespace Kronofoto\Test;

require_once 'BaseCest.php';

use ApiTester;

class PageCest extends BaseCest 
{
    private $pageName = 'about';

    public function testPage(ApiTester $I)
    {
        $url = '/page/' . $this->pageName;
        $numberOfFields = 6;

        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields);
        $this->testPageStructure($I);
    }

    private function testPageStructure($I)
    {
        $I->seeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'slug' => 'string',
            'title' => 'string',
            'body' => 'string|null',
            'created' => 'string:' . $this->dateRegex,
            'modified' => 'string:' . $this->dateRegex
        ]);
    }
}
