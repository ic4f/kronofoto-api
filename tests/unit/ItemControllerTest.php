<?php
namespace Kronofoto\Test\Unit\Controllers;

require_once 'ControllerTest.php';

class ItemControllerTest extends ControllerTest
{
    public function testItemByIdentifier()
    {
        $r = $this->getResponse('GET', '/items/FI000001');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testRandomFeaturedItem()
    {
        $r = $this->getResponse('GET', '/items/random');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testItems()
    {
        $r = $this->getResponse('GET', '/items');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testItemMetadata()
    {
        $r = $this->getResponse('GET', '/items/FI000001/metadata');
        $this->assertEquals(200, $r->getStatusCode());
    }
}
