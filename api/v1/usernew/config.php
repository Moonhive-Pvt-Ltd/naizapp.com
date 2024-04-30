<?php

class config
{
    private $DB_HOST;
    private $DB_USER;
    private $DB_PASS;
    private $DB_DB;

    public function __construct()
       {
       $this->DB_HOST = "localhost";
       $this->DB_USER = "root";
       $this->DB_PASS = "R7tf$MR&VsQoPxxi";
       $this->DB_DB = "livenaiz";
    }

    /**
     * @return string
     */
    public function getDBHOST()
    {
        return $this->DB_HOST;
    }

    /**
     * @param string $DB_HOST
     */
    public function setDBHOST($DB_HOST)
    {
        $this->DB_HOST = $DB_HOST;
    }

    /**
     * @return string
     */
    public function getDBUSER()
    {
        return $this->DB_USER;
    }

    /**
     * @param string $DB_USER
     */
    public function setDBUSER($DB_USER)
    {
        $this->DB_USER = $DB_USER;
    }

    /**
     * @return string
     */
    public function getDBPASS()
    {
        return $this->DB_PASS;
    }

    /**
     * @param string $DB_PASS
     */
    public function setDBPASS($DB_PASS)
    {
        $this->DB_PASS = $DB_PASS;
    }

    /**
     * @return string
     */
    public function getDBDB()
    {
        return $this->DB_DB;
    }

    /**
     * @param string $DB_DB
     */
    public function setDBDB($DB_DB)
    {
        $this->DB_DB = $DB_DB;
    }
}
