<?php

include dirname(__FILE__) . '/../src/Db.php';

class Contact
{
    private $validationErrors = [];
    private $databaseConnection = NULL;

    public function __construct()
    {
        // PUT YOUR DB CREDS HERE
        $databaseCredentials = [
            'host'      => 'localhost',
            'username'  => 'dealerinspire',
            'password'  => 'dealerinspire',
            'database'  => 'dealerinspire'
        ];

        $this->databaseConnection = new Db($databaseCredentials);
    }

    public function saveContact($formData)
    {
        $out = [];

        $validatedSubmission = $this->parseFormSubmission($formData);

        if ($validatedSubmission) {
            // validated
            $formSubmissionID = $this->saveFormSubmission($validatedSubmission);

            if ($formSubmissionID) {
                // saved
                if ($this->sendContactFormEmail($formSubmissionID)) {
                    // sent
                    $out['status']  = 'success';
                    $out['message'] = 'Thank you for you contact!';

                    return $out;
                }
            }
        }

        $out['status']  = 'error';
        $out['message'] = 'We were unable to send your contact -- please try again soon!';
        return $out;
    }

    private function sendContactFormEmail($id)
    {
        $data = $this->getDatabase()->getContact($id);

        if ($this->sendEmail($data)) {
            $this->getDatabase()->updateContactEmailStatus($id, 'success');
            return true;
        }
        
        // Email could not be sent
        $this->getDatabase()->updateContactEmailStatus($submittedContactId, 'failed');
        return false;
    }

    private function saveFormSubmission($data)
    {
        return $this->getDatabase()->insertData('contacts', $data);
    }
    
    public function sendEmail($parsedSubmission)
    {
        // The Business
        $to = 'ceili@ceilicornelison.com';
        $subject = "New contact form submission!";
        $headers = sprintf(
            "From: %s <%s>\r\n",
            $parsedSubmission['name'],
            $parsedSubmission['email']
        );

        $messageBody = "New contact form submission!\r\n";

        foreach($parsedSubmission as $key => $value) {
            $messageBody .= sprintf("%s: %s\r\n", $key, $value);
        }

        // If email has been process for sending, display a success message
        return mail($to, $subject, $messageBody, $headers);
    }

    public function sanitizeFieldValue($value)
    {
        $value  = stripslashes($value);
        $value  = htmlentities($value);
        $value  = strip_tags($value);
        $value  = $this->databaseConnection->escapeString($value);

        return $value;
    }

    public function sanitizeEmailValue($value)
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    public function parseFormSubmission($data)
    {
        if (empty($data)) {
            return false;
        }

        $formData = json_decode($data);

        if (NULL === json_decode($data)) {
            return false;
        }

        foreach ($formData as $obj) {
            $this->validateField($obj->name, 'NotNull', $obj->value);
        }

        if (empty($this->getValidationErrors())) {
            $out = [];

            foreach ($formData as $obj) {
                $out[$obj->name] = $obj->value;
            }

            return $out;
        }

        return false;
    }

    public function getDatabase()
    {
        return $this->databaseConnection;
    }

    // Only current validation condition is not null
    // as per project requirements
    public function validateField($fieldName, $condition = 'NotNull', $fieldValue = '')
    {
        $validationMethod = 'validate' . $condition;

        if (true !== $this->$validationMethod($fieldValue)) {
            $this->setValidationErrorOnField($fieldName, $condition);
        }
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function setValidationErrorOnField($fieldName, $condition)
    {
        $this->validationErrors[$fieldName] = $condition;
    }

    private function validateNotNull($value)
    {
        return !empty($value);
    }
}
