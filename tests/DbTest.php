<?php

class DbTests extends PHPUnit_Framework_TestCase
{
    private $_db;

    private $_emptyCreds = [];

    private $_invalidCreds = [
            'host'      => 'host',
            'username'  => 'username',
            'password'  => 'password',
            'database'  => 'database'
        ]
    ;

    private $_validCreds = [
            'host'      => 'localhost',
            'username'  => 'dealerinspire',
            'password'  => 'dealerinspire',
            'database'  => 'dealerinspire'
        ]
    ;

    private $_testingData = [
            'column_1' => 'value_1',
            'column_2' => 'value_2',
            'column_3' => 'value_3'
    ];

    protected function setUp()
    {
        $this->_db = new Db($this->_validCreds);
    }
 
    protected function tearDown()
    {
        $this->_db = null;
    }

    public function testCanNotCreateDbConnectionWithoutCredentials()
    {
        $db = new Db($this->_emptyCreds);

        $this->assertFalse($db->createDatabaseConnection());
    }

    public function testCanNotCreateDbConnectionWithInvalidCredentials()
    {
        $db = new Db($this->_invalidCreds);

        $this->assertFalse($db->createDatabaseConnection());
    }

    public function testCanCreateDbConnectionWithValidCredentials()
    {
        $db = new Db($this->_validCreds);

        $this->assertObjectHasAttribute(
            'field_count',
            $db->createDatabaseConnection()
        );
    }

    public function testCanDetermineCorrectColumnNames()
    {
        $columnNames = $this->_db->getColumnNames((object) $this->_testingData);

        $this->assertEquals($columnNames[0], 'column_1');
        $this->assertEquals($columnNames[1], 'column_2');
        $this->assertEquals($columnNames[2], 'column_3');
    }

    public function testCanDetermineCorrectDataValues()
    {
        $values = $this->_db->getValues((object) $this->_testingData);

        $this->assertEquals($values[0], 'value_1');
        $this->assertEquals($values[1], 'value_2');
        $this->assertEquals($values[2], 'value_3');
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

        $result = $this->_db->insertContact((object) $validData);

        $this->assertGreaterThan(0, $result);
    }

    public function testContactCanBeGotten()
    {
        $this->markTestIncomplete(
            'Implement tests on DB mocking'
        );
    }
}
