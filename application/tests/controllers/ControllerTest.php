<?php
require_once(__DIR__.'/../CITest.php');

class ControllerTest extends CITestCase
{

    public function setUp()
    {
        //$this->CI =& get_instance();
    }

    public function testDummy()
    {
        $input = ['foo'=>'bar',2,3];
        $this->assertArrayHasKey('foo', $input);
        $this->assertEquals(3, count($input));
    }
}
