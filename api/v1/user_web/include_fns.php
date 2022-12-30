<?php

class include_fns
{
    public function __construct()
    {
        defined('CRYPTKEY') ? NULL : define('CRYPTKEY', 'DhesaGtdfd8podf5');
    }

    public function Ncrypto($data)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, CRYPTKEY, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    public function Dcrypto($data)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, CRYPTKEY, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    public function create_random_string($length)
    {
        // The characters we want in the output
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $count = strlen($chars);

        // Generate 12 random bytes
        $bytes = random_bytes($length);

        // Construct the output string
        $result = '';
        // Split the string of random bytes into individual characters
        foreach (str_split($bytes) as $byte) {
            // ord($byte) converts the character into an integer between 0 and 255
            // ord($byte) % $count wrap it around $chars
            $result .= $chars[ord($byte) % $count];
        }

        return $result;
    }

    public function get($get_data)
    {
        return isset($_GET[$get_data]) ? $_GET[$get_data] : '';
    }

    public function post($post_data)
    {
        return isset($_POST[$post_data]) ? $_POST[$post_data] : '';
    }

    public function request($request_data)
    {
        return isset($_REQUEST[$request_data]) ? $_REQUEST[$request_data] : '';
    }

    function clean($i_data)
    {
        return strip_tags($this->my_real_escape_string($i_data));
    }

    function my_real_escape_string($value)
    {
        $search = array("%", "\x00", "\n", "\r", '\\', '\'', '"', "'", '"', "\x1a");
        $replace = array('', '\x00', '\n', '\r', '\\\\', '', "\'", '\"', '\x1a');

        return str_replace($search, $replace, $value);
    }

    function clean_inputs($data)
    {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->clean_inputs($v);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            $clean_input = trim($data);
        }
        return $clean_input;
    }
}