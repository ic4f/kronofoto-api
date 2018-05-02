<?php
namespace Kronofoto\Test\APIDB;

use ApiTester;

class PageCest
{
    public function testReadOne(ApiTester $I)
    {
        $expectedFields = 6;
        $I->wantTo("get one page by name");
        $name= 'about';
        $I->sendGET("/page/$name"); 
        $data = $I->grabDataFromResponseByJsonPath('$*');
        $I->assertEquals($expectedFields, count($data));
        $I->assertEquals(1, $data[0]);
        $I->assertEquals($name, $data[1]);
        $I->assertEquals('About Fortepan Iowa', $data[2]);
        $I->assertEquals('2018-04-19 14:27:46', $data[4]);
        $I->assertEquals('2018-04-19 14:27:46', $data[5]);
    }
}
