<?php

class db_user
{

    public $mysqli, $function;

    public function __construct()
    {
        require_once "config.php";
        $config = new config();
        $this->mysqli = new mysqli($config->getDBHOST(), $config->getDBUSER(), $config->getDBPASS(), $config->getDBDB());
        require_once "include_fns.php";
        $this->function = new include_fns();
    }

    public function add_api_data($user_id, $url, $data)
    {
        $api_data = mysqli_query($this->mysqli, "INSERT INTO api_data (user_id, url, api_data, `type`)
                                                        VALUES($user_id, '$url', '$data', 'user_web')");
        return $api_data ? $api_data : $this->mysqli->error;
    }

    public function getUserId($user_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM `user` WHERE uid = '$user_uid'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            return $user_id;
        }
        return FALSE;
    }

    public function isValidUserId($user_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM `user` WHERE uid = '$user_uid' AND status = 'active'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            return $user_id;
        }
        return FALSE;
    }

    public function isValidVendorId($vendor_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM vendor WHERE uid = '$vendor_uid' AND status = 'active'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $vendor_id = $row['id'];
            return $vendor_id;
        }
        return FALSE;
    }

    public function isValidProductId($product_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM `product` WHERE uid = '$product_uid'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $product_id = $row['id'];
            return $product_id;
        }
        return FALSE;
    }

    public function isValidOrderId($order_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM orders WHERE uid = '$order_uid'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $order_id = $row['id'];
            return $order_id;
        }
        return FALSE;
    }

    public function isValidActiveProductId($product_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM `product` WHERE uid = '$product_uid' AND status = 'active'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $product_id = $row['id'];
            return $product_id;
        }
        return FALSE;
    }

    public function isValidUserAddressId($user_id, $address_id)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM user_address 
                                                      WHERE user_id = '$user_id' 
                                                      AND id = '$address_id'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $address_id = $row['id'];
            return $address_id;
        }
        return FALSE;
    }
}
