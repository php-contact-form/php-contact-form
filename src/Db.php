<?php

class Db
{
    private $connection = NULL;
    private $credentials = [];

    public function __construct($databaseCredentials = [])
    {
        $this->credentials  = $databaseCredentials;
        $this->connection   = $this->createDatabaseConnection();
    }

    public function createDatabaseConnection()
    {
        if (empty($this->credentials)) {
            return false;
        }

        try {
            $this->connection = new mysqli(
                $this->credentials['host'],
                $this->credentials['username'],
                $this->credentials['password'],
                $this->credentials['database']
            );
        } catch (Exception $e) {
            // Could not create database connection with provided credentials
            return false;
        }

        return true;
    }

    public function insertData($tableName, $formData)
    {
        $result = $this->connection->query(
            $this->getInsertQuery($tableName, $formData)
        );

        return $result;
    }

    public function getInsertQuery($tableName, $formData)
    {
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (\'%s\');',
            $tableName,
            implode(', ', $this->getColumnNames($formData)),
            implode('\', \'', $this->getValues($formData))
        );

        return $query;
    }

    public function getColumnNames($data)
    {
        $out = [];

        foreach ($data as $key => $value) {
            $out[] = $key;
        }

        return $out;
    }

    public function getValues($data)
    {
        foreach ($data as $key => $value) {
            $out[] = $value;
        }

        return $out;
    }
}
