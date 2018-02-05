<?php

class Db
{
    private $connection = NULL;
    private $credentials = [];

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

    public function __construct($databaseCredentials = [])
    {
        $this->credentials  = $databaseCredentials;
        $this->connection   = $this->createDatabaseConnection();
    }
}
