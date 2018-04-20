<?php
namespace Kronofoto\Test;

use ApiTester;

class PageCest
{
    protected $container; 
    protected $baseUrl = '/api/';

    public function _before(ApiTester $I)
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    public function testResponseIsJson(ApiTester $I)
    {
        $I->wantTo('get data in JSON format');
        $I->sendGET($this->getURL() . 'about');
        $this->checkResponseIsValid($I);
    }

    protected function checkResponseIsValid(ApiTester $I)
    {
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeHttpHeader('Content-type', 'application/json;charset=utf-8');
        $I->seeResponseIsJson();
    }
    protected function getURL()
    {
        return  $this->baseUrl . 'page/';
    }

    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 6;
        $I->wantTo("get one page by slug");
        $slug = 'about';
        $I->sendGET($this->getURL() . "$slug"); 
        $this->checkResponseIsValid($I);
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals(1, $data[0]);
        $I->assertEquals($slug, $data[1]);
        $I->assertEquals('About Fortepan Iowa', $data[2]);
       // $I->assertEquals('body here', $data[3]); //TODO no way to test. Need mocks!
       // $I->assertEquals('2018-04-19 13:36:15', $data[4]);
       // $I->assertEquals('2018-04-19 13:36:15', $data[5]);
    }
}
