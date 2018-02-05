<?php

class Contact
{
    private $validationErrors = [];
    private $databaseConnection = NULL;

    public function __construct()
    {
        $databaseCredentials = [
            'host'      => '',
            'username'  => '',
            'password'  => '',
            'database'  => ''
        ];
    }

    public function render()
    {
        return file_get_contents('public/index.php');
    }

    // Only current validation condition is not null
    // as per project requirements
    public function validateField($condition = 'NotNull', $fieldName, $fieldValue = '')
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

    public function connectToDatabase()
    {

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
