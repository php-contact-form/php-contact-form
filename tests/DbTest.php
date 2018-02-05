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

    private $testingData = [
            'column_1' => 'value_1',
            'column_2' => 'value_2',
            'column_3' => 'value_3'
    ];

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

        $this->assertObjectHasAttribute('field_count', $db->createDatabaseConnection());
    }

    public function testCanDetermineCorrectColumnNames()
    {
        $columnNames = $this->db->getColumnNames((object) $this->testingData);

        $this->assertEquals($columnNames[0], 'column_1');
        $this->assertEquals($columnNames[1], 'column_2');
        $this->assertEquals($columnNames[2], 'column_3');
    }

    public function testCanDetermineCorrectDataValues()
    {
        $values = $this->db->getValues((object) $this->testingData);

        $this->assertEquals($values[0], 'value_1');
        $this->assertEquals($values[1], 'value_2');
        $this->assertEquals($values[2], 'value_3');
    }

    public function testGetInsertQueryComposesCorrectString()
    {
        $query = $this->db->getInsertQuery('tablename', (object) $this->testingData);
        $expected = "INSERT INTO tablename (column_1, column_2, column_3) VALUES ('value_1', 'value_2', 'value_3');";

        $this->assertEquals($query, $expected);
    }

    public function testValidDataCanBeSuccessfullyInserted()
    {
        $validData = [
            'name'              => 'Georgie',
            'email'             => 'georgie@email.com',
            'message'           => 'This is a message!',
            'email_sent_to'     => 'guy-smiley@example.com',
            'email_sent_status' => 'sent'
        ];

        $result = $this->db->insertData('contacts', (object) $validData);

        $this->assertGreaterThan(0, $result);
    }
}
