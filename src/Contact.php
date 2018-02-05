<?php

include dirname(__FILE__) . '/../src/Db.php';

class Contact
{
    private $validationErrors = [];
    private $databaseConnection = NULL;

    public function __construct()
    {
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
        $parsedSubmission = $this->parseFormSubmission($formData);

        $out = [];

        if ("string" === gettype($parsedSubmission)) {
            $out['status']  = 'error';
            $out['message'] = $parsedSubmission;
        } else if ("array" === gettype($parsedSubmission)) {
            // valid
            $submittedContactId = $this->getDatabase()->insertData('contacts', $parsedSubmission);

            // success
            if ($this->sendEmail($parsedSubmission)) {
                $this->getDatabase()->updateContactEmailStatus($submittedContactId, 'success');
                $out['status']  = 'success';
                $out['message'] = 'Thank you for you contact!';
            } else {
                // Email could not be sent
                $this->getDatabase()->updateContactEmailStatus($submittedContactId, 'failed');
                $out['status']  = 'error';
                $out['message'] = $parsedSubmission;
            }
        }

        return $out;
    }

    public function render()
    {
        return file_get_contents('public/index.php');
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
            return "Invalid submission; please try again";
        }

        $formData = json_decode($data);

        if (NULL === json_decode($data)) {
            return "Invalid submission; please try again";
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

        return "Invalid submission; please try again";
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
