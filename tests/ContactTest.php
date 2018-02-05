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
        $this->contact->validateField('name', 'NotNull', '');

        $validationErrors = $this->contact->getValidationErrors();

        $this->assertNotEmpty($validationErrors);
        $this->assertCount(1, $validationErrors);
        $this->assertArrayHasKey('name', $validationErrors);
        $this->assertEquals($validationErrors['name'], 'NotNull');
    }

    public function testNotNullFieldValueIsProperlyValidated()
    {
        $this->contact->validateField('name', 'NotNull', 'George');

        $validationErrors = $this->contact->getValidationErrors();

        $this->assertEmpty($validationErrors);
    }

    public function testDatabaseConnectionIsInstantiated()
    {
        $this->assertInstanceOf('Db', $this->contact->getDatabase());
    }

    public function testInvalidFormDataReturnsErrorMessage()
    {
        $emptyInput = '';
        $response = $this->contact->parseFormSubmission($emptyInput);
        $this->assertEquals($response, "Invalid submission; please try again");

        $nonJsonInput = 'Not even JSON';
        $response = $this->contact->parseFormSubmission($nonJsonInput);
        $this->assertEquals($response, "Invalid submission; please try again");
    }

    public function testMissingFormDataReturnsErrorMessage()
    {
        $validJsonButInvalidFieldValue = json_encode([
            'name'      => 'Georgie',
            'email'     => 'georgie@example.com',
            'message'   => ''
        ]);
        $response = $this->contact->parseFormSubmission($validJsonButInvalidFieldValue);
        $this->assertEquals($response, "Invalid submission; please try again");
    }

    public function testGoodFormInputReturnsParsedJSON()
    {
        $validJsonSubmission = json_encode([
            'name'      => 'Georgie',
            'email'     => 'georgie@example.com',
            'message'   => 'Hey there!'
        ]);
        $response = $this->contact->parseFormSubmission($validJsonSubmission);
        $this->assertEmpty($this->contact->getValidationErrors());
    }
} 
