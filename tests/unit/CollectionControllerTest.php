<?php
namespace Kronofoto\Test\Unit\Controllers;

require_once 'ControllerTest.php';

class CollectionControllerTest extends ControllerTest
{
    public function testCollection()
    {
        $r = $this->getResponse('GET', '/collections/42');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testCollections()
    {
        $r = $this->getResponse('GET', '/collections');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testItemCollection()
    {
        $r = $this->getResponse('GET', '/items/FI000001/collection');
        $this->assertEquals(200, $r->getStatusCode());
    }
}
