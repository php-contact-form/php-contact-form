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
 
    public function testValidationErrorsInitedAsBoolFalse()
    {
        $validationError = $this->contact->hasValidationError();

        $this->assertFalse($validationError);
    }

    public function testNullFieldValueIsProperlyValidated()
    {
        $this->contact->validateField('name', 'NotNull', '');

        $validationError = $this->contact->hasValidationError();

        $this->assertTrue($validationError);
    }

    public function testNotNullFieldValueIsProperlyValidated()
    {
        $this->contact->validateField('name', 'NotNull', 'George');

        $validationError = $this->contact->hasValidationError();

        $this->assertFalse($validationError);
    }

    public function testDatabaseConnectionIsInstantiated()
    {
        $this->assertInstanceOf('Db', $this->contact->getDatabase());
    }

    public function testInvalidFormDataReturnsErrorMessage()
    {
        $emptyInput = '';
        $response = $this->contact->parseFormSubmission($emptyInput);
        $this->assertFalse($response);

        $nonJsonInput = 'Not even JSON';
        $response = $this->contact->parseFormSubmission($nonJsonInput);
        $this->assertFalse($response);
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
        $this->assertFalse($response);
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
        $this->assertFalse($this->contact->hasValidationError());
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

        $this->assertTrue($this->contact->sendEmail($parsedSubmission));
    }
} 
