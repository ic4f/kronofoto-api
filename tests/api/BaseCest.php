<?php
namespace Kronofoto\Test\API;

//TODO make sure to test incorrect routes
use ApiTester;

\Codeception\Util\JsonType::addCustomFilter('zeroOrGreater', function($value) {
    return $value >= 0;
});

/* This tests the routes + the structure of the returned data */
abstract class BaseCest
{    
    protected $firstRowSelector = '$[0]*';
    protected $dateRegex = 'regex(~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~)';
    protected $boolRegex = 'regex(~^0|1$~)';
    protected $itemIdRegex;
    protected $yearMin;
    protected $yearMax;

    /* data-specific values (must be present in database */
    protected $itemIdentifier = 'FI000001';
//    protected $collectionId = 2;

    private $container; 

    public function _before(ApiTester $I)
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
        $this->itemIdRegex = 
            'regex(~' . $this->container['settings']['data']['item_id_regex'] . '~)';
        $this->yearMin = $this->container['settings']['data']['year_min'];
        $this->yearMax = $this->container['settings']['data']['year_max'];
    }

    protected function testResponseIsJson(ApiTester $I)
    {
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }

    protected function testNumberOfFields(ApiTester $I, $numberOfFields, $selector = '$*')
    {
        $data = $I->grabDataFromResponseByJsonPath($selector);
        $I->assertEquals($numberOfFields, count($data));
    }

}
