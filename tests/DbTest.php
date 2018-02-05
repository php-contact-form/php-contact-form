<?php

require dirname(__FILE__) . '/../src/Db.php';
 
class DbTests extends PHPUnit_Framework_TestCase
{
    private $db;

    private $emptyCreds = [];

    private $invalidCreds = [
            'host'      => 'host',
            'username'  => 'username',
            'password'  => 'password',
            'database'  => 'database'
        ]
    ;

    private $validCreds = [
            'host'      => 'localhost',
            'username'  => 'dealerinspire',
            'password'  => 'dealerinspire',
            'database'  => 'dealerinspire'
        ]
    ;
 
    protected function setUp()
    {
        $this->db = new Db($this->validCreds);
    }
 
    protected function tearDown()
    {
        $this->db = NULL;
    }

    public function testCanNotCreateDbConnectionWithoutCredentials()
    {
        $db = new Db($this->emptyCreds);

        $this->assertFalse($db->createDatabaseConnection());
    }

    public function testCanNotCreateDbConnectionWithInvalidCredentials()
    {
        $db = new Db($this->invalidCreds);

        $this->assertFalse($db->createDatabaseConnection());
    }

    public function testCanCreateDbConnectionWithValidCredentials()
    {
        $db = new Db($this->validCreds);

        $this->assertTrue($db->createDatabaseConnection());
    }
}
