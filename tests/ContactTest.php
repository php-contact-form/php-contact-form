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
            [
                'name'  => 'name',
                'value' => 'Georgie'
            ],
            [
                'name'  => 'email',
                'value' => 'georgie@example.com'
            ],
            [   'name'  => 'message',
                'value' => ''
            ]
        ]);

        $response = $this->contact->parseFormSubmission($validJsonButInvalidFieldValue);
        $this->assertEquals($response, "Invalid submission; please try again");
    }

    public function testGoodFormInputReturnsParsedJSON()
    {
        $validJsonSubmission = json_encode([
            [
                'name'  => 'name',
                'value' => 'Georgie'
            ],
            [
                'name'  => 'email',
                'value' => 'georgie@example.com'
            ],
            [   'name'  => 'message',
                'value' => 'Hey there!'
            ]
        ]);
        $response = $this->contact->parseFormSubmission($validJsonSubmission);
        $this->assertEmpty($this->contact->getValidationErrors());
    }

    public function testFormDataProperlySanitized()
    {
        $emailValue = new stdClass;
        $emailValue->value  = 'georgie@example.com';
        $sanitizedEmail = $this->contact->sanitizeFieldValue($emailValue->value);

        $this->assertEquals('georgie@example.com', $sanitizedEmail);

        $messageValue = new stdClass;
        $messageValue->value  = 'It\'s a "Message" contaning spècial çhars!';
        $sanitizedMessage = $this->contact->sanitizeFieldValue($messageValue->value);

        $this->assertEquals(
            "It\'s a &quot;Message&quot; contaning sp&egrave;cial &ccedil;hars!",
            $sanitizedMessage
        );
    }

    public function testEmailProperlySanitized()
    {
        $goodEmail  = 'georgie@example.com';
        $badEmail   = '(georgie)@example.com,';

        $this->assertEquals($goodEmail, $this->contact->sanitizeEmailValue($goodEmail));
        $this->assertEquals($goodEmail, $this->contact->sanitizeEmailValue($badEmail));
    }

    public function testEmailShouldReturnStatus()
    {
        $goodSubmission = json_encode([
            [
                'name'  => 'name',
                'value' => 'Georgie'
            ],
            [
                'name'  => 'email',
                'value' => 'georgie@example.com'
            ],
            [   'name'  => 'message',
                'value' => 'Hey there!'
            ]
        ]);

        $parsedSubmission = $this->contact->parseFormSubmission($goodSubmission);

        $this->assertTrue($this->contact->sendEmail(1, $parsedSubmission));
    }
} 
