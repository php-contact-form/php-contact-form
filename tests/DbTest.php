<?php

require dirname(__FILE__) . '/../src/Db.php';
 
class DbTests extends PHPUnit_Framework_TestCase
{
    private $db;
 
    protected function setUp()
    {
        $this->db = new Db();
    }
 
    protected function tearDown()
    {
        $this->db = NULL;
    }
}
