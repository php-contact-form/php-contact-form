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

    public function parseFormSubmission($data)
    {
        if (empty($data)) {
            return "Invalid submission; please try again";
        }

        $formData = json_decode($data);

        if (NULL === json_decode($data)) {
            return "Invalid submission; please try again";
        }

        foreach ($formData as $key => $value) {
            $this->validateField($key, 'NotNull', $value);
        }

        if (empty($this->getValidationErrors())) {
            return $formData;
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
