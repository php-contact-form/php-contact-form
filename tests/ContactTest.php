<?php

/**
 * File contains testing class for Contact
 *
 * PHP version 5.5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category ContactForm
 * @package  ContactForm
 * @author   Ceili Cornelison <ceili@ceilicornelison.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  GIT: <git_id>
 * @link     http://dealerinspire.local:8080/
 * @since    File available since Release 1.0.0
 */

require dirname(__FILE__) . '/../src/Contact.php';

 /**
 * Contact Form Tests
 *
 * @category ContactForm
 * @package  ContactForm
 * @author   Ceili Cornelison <ceili@ceilicornelison.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://dealerinspire.local:8080/
 * @since    Class available since Release 1.0.0
 */

class ContactTests extends PHPUnit_Framework_TestCase
{
    private $_contact;

     /**
     * Populates private clas vars
     *
     * @return FALSE
     *
     * @access protected
     * @since  Method available since Release 1.0
     */
    protected function setUp()
    {
        $this->_contact = new Contact();
    }

      /**
     * Nulls out private clas vars
     *
     * @return FALSE
     *
     * @access protected
     * @since  Method available since Release 1.0
     */
    protected function tearDown()
    {
        $this->_contact = null;
    }
 
    public function testValidationErrorsInitedAsBoolFalse()
    {
        $validationError = $this->_contact->hasValidationError();

        $this->assertFalse($validationError);
    }

    public function testNullFieldValueIsProperlyValidated()
    {
        $this->_contact->validateField('name', 'NotNull', '');

        $validationError = $this->_contact->hasValidationError();

        $this->assertTrue($validationError);
    }

    public function testNotNullFieldValueIsProperlyValidated()
    {
        $this->_contact->validateField('name', 'NotNull', 'George');

        $validationError = $this->_contact->hasValidationError();

        $this->assertFalse($validationError);
    }

    public function testDatabaseConnectionIsInstantiated()
    {
        $this->assertInstanceOf('Db', $this->_contact->getDatabase());
    }

    public function testInvalidFormDataReturnsErrorMessage()
    {
        $emptyInput = '';
        $response = $this->_contact->parseFormSubmission($emptyInput);
        $this->assertFalse($response);

        $nonJsonInput = 'Not even JSON';
        $response = $this->_contact->parseFormSubmission($nonJsonInput);
        $this->assertFalse($response);
    }

    public function testMissingFormDataReturnsErrorMessage()
    {
        $validJsonButInvalidFieldValue = json_encode(
            [
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
            ]
        );

        $response = $this->_contact->parseFormSubmission(
            $validJsonButInvalidFieldValue
        );
        $this->assertFalse($response);
    }

    public function testGoodFormInputReturnsParsedJSON()
    {
        $validJsonSubmission = json_encode(
            [
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
            ]
        );
        $response = $this->_contact->parseFormSubmission($validJsonSubmission);
        $this->assertFalse($this->_contact->hasValidationError());
    }

    public function testFormDataProperlySanitized()
    {
        $emailValue = new stdClass;
        $emailValue->value  = 'georgie@example.com';
        $sanitizedEmail = $this->_contact->sanitizeFieldValue($emailValue->value);

        $this->assertEquals('georgie@example.com', $sanitizedEmail);

        $messageValue = new stdClass;
        $messageValue->value  = 'It\'s a "Message" contaning spècial çhars!';
        $sanitizedMessage = $this->_contact->sanitizeFieldValue(
            $messageValue->value
        );

        $this->assertEquals(
            "It\'s a &quot;Message&quot; contaning sp&egrave;cial &ccedil;hars!",
            $sanitizedMessage
        );
    }

    public function testEmailProperlySanitized()
    {
        $goodEmail  = 'georgie@example.com';
        $badEmail   = '(georgie)@example.com,';

        $this->assertEquals(
            $goodEmail,
            $this->_contact->sanitizeEmailValue($goodEmail)
        );

        $this->assertEquals(
            $goodEmail,
            $this->_contact->sanitizeEmailValue($badEmail)
        );
    }

    public function testEmailShouldReturnStatus()
    {
        $goodSubmission = json_encode(
            [
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
            ]
        );

        $parsedSubmission = $this->_contact->parseFormSubmission($goodSubmission);

        $this->assertTrue($this->_contact->sendEmail($parsedSubmission));
    }
} 
