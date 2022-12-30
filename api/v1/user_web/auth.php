<?php

require_once 'include_fns.php';
require_once 'config.php';
require_once 'db_user.php';

class auth
{
    public $functions, $db_user, $config;

    public function __construct()
    {
        defined('API_TEST') ? NULL : define('API_TEST', TRUE);

        $this->functions = new include_fns();

        $this->config = new config();

        $this->db_user = new db_user();
    }
}