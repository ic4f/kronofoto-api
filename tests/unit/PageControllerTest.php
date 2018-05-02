<?php
namespace Kronofoto\Test\Unit\Controllers;

require_once 'ControllerTest.php';

class PageControllerTest extends ControllerTest
{
    public function testPage()
    {
        $r = $this->getResponse('GET', '/page/any-page-name');
        $this->assertEquals(200, $r->getStatusCode());
    }
}
