<?php

/**
 * Main file for Contact Form application
 *
 * PHP version 5.5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

/**
 * This is a "Docblock Comment," also known as a "docblock."  The class'
 * docblock, below, contains a complete description of how to write these.
 */

require dirname(__FILE__) . '/../src/Db.php';

/**
 * Contact Form class for saving & sending form contacts
 *
 * @category   ContactForm
 * @package    ContactForm
 * @author     Ceili Cornelison <ceili@ceilicornelison.com>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      Class available since Release 1.2.0
 * @deprecated Class deprecated in Release 2.0.0
 */

class Contact
{
    private $_validationError = false;
    private $_databaseConnection = null;


    /**
     * Instantiates Contact Form
     *
     * @return FALSE
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function __construct()
    {
        // PUT YOUR DB CREDS HERE
        $databaseCredentials = [
            'host'      => 'localhost',
            'username'  => 'dealerinspire',
            'password'  => 'dealerinspire',
            'database'  => 'dealerinspire'
        ];

        $this->_databaseConnection = new Db($databaseCredentials);
    }

    /**
     * Primary method for saving & sending contact
     *
     * @param array $formData JSON data from submitted form
     *
     * @return array array of the result for the front end
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function saveContact($formData)
    {
        $out = [];

        $validatedSubmission = $this->parseFormSubmission($formData);

        if ($validatedSubmission) {
            // validated
            $formSubmissionID = $this->_saveFormSubmission($validatedSubmission);

            if ($formSubmissionID) {
                // saved
                if ($this->_sendContactFormEmail($formSubmissionID)) {
                    // sent
                    $out['status']  = 'success';
                    $out['message'] = 'Thank you for your contact!';

                    return $out;
                }
            }
        }

        $out['status']  = 'error';
        $out['message']
            = 'We were unable to send your contact -- please try again soon!';
        return $out;
    }

    /**
     * Internal helper function to validate form submission and, if
     * valid, return normalized array
     *
     * @param array $data JSON data from submitted form
     *
     * @return false|array False if failed, array if success
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function parseFormSubmission($data)
    {
        if (empty($data)) {
            return false;
        }

        $formData = json_decode($data);

        if (null === json_decode($data)) {
            return false;
        }

        foreach ($formData as $obj) {
            $this->validateField($obj->name, 'NotNull', $obj->value);
        }

        if (!$this->hasValidationError()) {
            $out = [];

            foreach ($formData as $obj) {
                $out[$obj->name] = $obj->value;
            }

            return $out;
        }

        return false;
    }

    /**
     * Internal helper function to save validated form submission
     *
     * @param array $data Array of data to save
     *
     * @return false|int False if faild, int ID if success
     *
     * @access private
     * @since  Method available since Release 1.0
     */
    private function _saveFormSubmission($data)
    {
        return $this->getDatabase()->insertContact($data);
    }

    /**
     * Internal helper function to send validated contact form
     *
     * @param int $id ID of contact form to email
     *
     * @return bool true if success, false if failed
     *
     * @access private
     * @since  Method available since Release 1.0
     */
    private function _sendContactFormEmail($id)
    {
        $data = $this->getDatabase()->getContact($id);

        if ($this->sendEmail($data)) {
            $this->getDatabase()->updateContactEmailStatus($id, 'success');
            return true;
        }
        
        // Email could not be sent
        $this->getDatabase()->updateContactEmailStatus(
            $submittedContactId,
            'failed'
        );

        return false;
    }

    /**
     * Function to interact with PHP mail()
     *
     * @param array $parsedSubmission submission content array
     *
     * @return bool true if success, false if failed
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function sendEmail($parsedSubmission)
    {
        // The Business
        $to = 'guy-smiley@example.com';
        $subject = "New contact form submission!";
        $headers = sprintf(
            "From: %s <%s>\r\n",
            $parsedSubmission['name'],
            $parsedSubmission['email']
        );

        $messageBody = "New contact form submission!\r\n";

        foreach ($parsedSubmission as $key => $value) {
            $messageBody .= sprintf("%s: %s\r\n", $key, $value);
        }

        // If email has been process for sending, display a success message
        return mail($to, $subject, $messageBody, $headers);
    }

    /**
     * Function to sanitize field value for db saftey
     *
     * @param string $value value to sanitize
     *
     * @return string sanitized value
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function sanitizeFieldValue($value)
    {
        $value  = stripslashes($value);
        $value  = htmlentities($value);
        $value  = strip_tags($value);
        $value  = $this->_databaseConnection->escapeString($value);

        return $value;
    }

    /**
     * Special email sanitize function to take advantage of filter_var()
     *
     * @param string $value email to sanitize
     *
     * @return string sanitized email
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function sanitizeEmailValue($value)
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Accessor for DB connection
     *
     * @return Db DB connection class
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function getDatabase()
    {
        return $this->_databaseConnection;
    }

    /**
     * Validation function -- Only current validation condition is
     * 'not null', as per project requirements
     *
     * @param string $fieldName  Name of Field
     * @param string $condition  Validation condition
     * @param string $fieldValue Value of Field
     *
     * @return void
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function validateField(
        $fieldName,
        $condition = 'NotNull',
        $fieldValue = ''
    ) {
        $validationMethod = '_validate' . $condition;

        if (true !== $this->$validationMethod($fieldValue)) {
            $this->addValidationError();
            return;
        }
    }

    /**
     * Add validation status to internal private var
     *
     * @return void
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function addValidationError()
    {
        $this->_validationError = true;
    }

    /**
     * Get validation status from internal private var
     *
     * @return bool
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function hasValidationError()
    {
        return $this->_validationError;
    }

    /**
     * Not Null validation function called by ValidateField
     *
     * @param string $value value to validate
     *
     * @return bool
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    private function _validateNotNull($value)
    {
        return !empty($value);
    }
}
