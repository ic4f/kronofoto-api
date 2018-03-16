<?php

class ItemControllerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $foo;
    
    protected function _before()
    {
        $this->foo = 42;
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $this->assertSame($this->foo, 42);

    }
}
