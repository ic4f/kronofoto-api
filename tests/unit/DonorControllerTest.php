<?php
namespace Kronofoto\Test\Unit\Controllers;

require_once 'ControllerTest.php';

class DonorControllerTest extends ControllerTest
{
    public function testDonor()
    {
        $r = $this->getResponse('GET', '/donors/42');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testDonors()
    {
        $r = $this->getResponse('GET', '/donors');
        $this->assertEquals(200, $r->getStatusCode());
    }

    public function testAllDonors()
    {
        $r = $this->getResponse('GET', '/alldonors');
        $this->assertEquals(200, $r->getStatusCode());
    }
}
