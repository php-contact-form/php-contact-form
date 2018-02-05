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

    public function render()
    {
        return file_get_contents('public/index.php');
    }

    public function sendEmail($contactID, $parsedSubmission)
    {
        // sanitize form values
        $out = [];

        foreach ($parsedSubmission as $field) {
            if ('email' !== $field->name) {
                // email is the only field with special chars
            } else {
                $out[] = $this->sanitizeField($field);
            }
        }
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
        filter_var($c, FILTER_SANITIZE_EMAIL);
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
