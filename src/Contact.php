<?php

class Contact
{
    private $validationErrors = [];

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

    private function setValidationErrorOnField($fieldName, $condition)
    {
        $this->validationErrors[$fieldName] = $condition;
    }

    private function validateNotNull($value)
    {
        return !empty($value);
    }
}
