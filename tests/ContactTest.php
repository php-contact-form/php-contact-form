<?php

require dirname(__FILE__) . '/../src/Contact.php';
 
class ContactTests extends PHPUnit_Framework_TestCase
{
    private $contact;
 
    protected function setUp()
    {
        $this->contact = new Contact();
    }
 
    protected function tearDown()
    {
        $this->contact = NULL;
    }
 
    public function testRender()
    {
        $result = $this->contact->render();
        $this->assertContains('Contact Guy Smiley', $result);
    }

    public function testValidationErrorsInitedAsEmptyArray()
    {
        $validationErrors = $this->contact->getValidationErrors();

        $this->assertEquals($validationErrors, []);
        $this->assertEmpty($validationErrors);
    }

    public function testNullFieldValueIsProperlyValidated()
    {
        $this->contact->validateField('NotNull', 'name', '');

        $validationErrors = $this->contact->getValidationErrors();

        $this->assertNotEmpty($validationErrors);
        $this->assertCount(1, $validationErrors);
        $this->assertArrayHasKey('name', $validationErrors);
        $this->assertEquals($validationErrors['name'], 'NotNull');
    }

    public function testNotNullFieldValueIsProperlyValidated()
    {
        $this->contact->validateField('NotNull', 'name', 'George');

        $validationErrors = $this->contact->getValidationErrors();

        $this->assertEmpty($validationErrors);
    }

    public function testDatabaseConnectionIsInstantiated()
    {
        $this->assertInstanceOf('Db', $this->contact->getDatabase());
    }
} 
