<?php
namespace Kronofoto\Test;

require_once 'BaseCest.php';

use ApiTester;

class DonorCest extends BaseCest
{
    /* data-specific values (must be present in database */
    private $donorId = 5;

    public function testDonorById($I)
    {
        $url = '/donors/' . $this->donorId;
        $this->donorHelper($I, $url, '$*');
    }

    public function testDonors($I)
    {
        $url = '/donors';
        $this->donorHelper($I, $url, $this->firstRowSelector);
    }

    public function testAllDonors($I)
    {
        $url = '/alldonors';
        $this->donorHelper($I, $url, $this->firstRowSelector);
    }

    private function donorHelper($I, $url, $selector) 
    {
        $numberOfFields = 7;

        $I->wantTo("get donors: $url");
        $I->sendGET($url);
        $I->seeResponseCodeIs(200);
        $this->testResponseIsJSON($I);
        $this->testNumberOfFields($I, $numberOfFields, $selector);
        $this->testDonorStructure($I);
    }

    private function testDonorStructure($I)
    {
        $I->seeResponseMatchesJsonType([
            'userId' => 'integer:>0',
            'firstName' => 'string',
            'lastName' => 'string',
            'collectionCount' => 'integer:zeroOrGreater',
            'itemCount' => 'integer:zeroOrGreater',
            'created' => 'string:' . $this->dateRegex,
            'modified' => 'string:' . $this->dateRegex
        ]);
    }
}
