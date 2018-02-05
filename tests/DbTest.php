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

    public function testCanDetermineCorrectColumnNames()
    {
        $testingData = json_decode(json_encode([
            'column_1' => 'value_1',
            'column_2' => 'value_2',
            'column_3' => 'value_3'
        ]));

        $columnNames = $this->db->getColumnNames($testingData);

        $this->assertEquals($columnNames[0], 'column_1');
        $this->assertEquals($columnNames[1], 'column_2');
        $this->assertEquals($columnNames[2], 'column_3');
    }
}
