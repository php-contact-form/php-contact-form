<?php

/**
 * Database file for Contact Form application
 *
 * PHP version 5.5
 *
 * @category ContactForm
 * @package  ContactForm
 * @author   Ceili Cornelison <ceili@ceilicornelison.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: GIT: <git_id>
 * @link     https://bitbucket.org/dealerinspire/php-contact-form/
 * @since    File available since Release 1.0.0
 */

/**
 * Database class for saving & sending form contacts
 *
 * @category ContactForm
 * @package  ContactForm
 * @author   Ceili Cornelison <ceili@ceilicornelison.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.0
 * @link     https://bitbucket.org/dealerinspire/php-contact-form/
 * @since    File available since Release 1.0.0
 */
class Db
{
    private $_connection = null;
    private $_credentials = [];

    /**
     * Instantiates Db class
     *
     * @param array $databaseCredentials DB Creds
     *
     * @return void
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function __construct($databaseCredentials = [])
    {
        $this->_credentials  = $databaseCredentials;
        $this->_connection   = $this->createDatabaseConnection();
    }

    /**
     * Creates mysqli connection to DB
     *
     * @return mysqli
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function createDatabaseConnection()
    {
        if (empty($this->_credentials)) {
            return false;
        }

        try {
            $connection = new mysqli(
                $this->_credentials['host'],
                $this->_credentials['username'],
                $this->_credentials['password'],
                $this->_credentials['database']
            );
        } catch (Exception $e) {
            // Could not create database connection with provided credentials
            return false;
        }

        return $connection;
    }

    /**
     * Returns results from querying DB for contact
     *
     * @param int $id ID of contact to query for
     *
     * @return null|array Null if not found, array if is
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function getContact($id)
    {
        $query = sprintf(
            'SELECT name, email, phone, message FROM contacts WHERE contact_id = %d',
            $id
        );

        return $this->_connection->query($query)->fetch_assoc();
    }

    /**
     * Updates contact in DB after attempted sending email with
     * status of email send -- save first in case email fails!
     *
     * @param int    $submittedContactId ID of contact to query for
     * @param string $status             status to save on contact
     *
     * @return null|int Null if failed, int if success
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function updateContactEmailStatus($submittedContactId, $status)
    {
        $query = sprintf(
            'UPDATE %s SET email_sent_status = \'%s\' WHERE contact_id = %d);',
            'contacts',
            $status,
            $submittedContactId
        );

        $result = $this->_connection->query($query);
    }

    /**
     * Creates contact entry in database
     *
     * @param array $formData data to save on contact
     *
     * @return null|int Null if failed, int if success
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function insertContact($formData)
    {
        $query = sprintf(
            'INSERT INTO contacts (%s) VALUES (\'%s\');',
            implode(', ', $this->getColumnNames($formData)),
            implode('\', \'', $this->getValues($formData))
        );

        $result = $this->_connection->query($query);

        return $this->_connection->insert_id;
    }

    /**
     * Determines column names for insert statement
     * based on data available -- useful for non-required
     * fields like Phone which may or may not exist
     *
     * @param array $data data available for save
     *
     * @return array Array of determined column names
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function getColumnNames($data)
    {
        $out = [];

        foreach ($data as $key => $value) {
            $out[] = $key;
        }

        return $out;
    }

    /**
     * Determines field values for insert statement
     * based on data available -- useful for non-required
     * fields like Phone which may or may not exist
     *
     * @param array $data data available for save
     *
     * @return array Array of determined field values
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function getValues($data)
    {
        foreach ($data as $key => $value) {
            $out[] = $value;
        }

        return $out;
    }

    /**
     * Interact with db connection to escape string
     *
     * @param array $value value to escape
     *
     * @return string Escaped string
     *
     * @access public
     * @since  Method available since Release 1.0
     */
    public function escapeString($value)
    {
        return $this->_connection->real_escape_string($value);
    }
}
