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
                                                        VALUES($user_id, '$url', '$data', 'user')");
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
   public function check(){
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $mobile = isset($this->_request['mobile']) ? mysqli_real_escape_string($this->mysqli, $this->_request['mobile']) : null;
        $pswd = isset($this->_request['password']) ? mysqli_real_escape_string($this->mysqli, $this->_request['password']) : null;
        $user = mysqli_query($this->mysqli, "SELECT *
        FROM `user`");
        $row = $user->fetch_assoc();
        $success = array('status' => "success", 'msg' =>$row);
            $this->response($this->json($success), 200);
 
    }
  public function sendOtp($mobile)
    {
        $otp = rand(1000, 9999);
        $api_key = '3df074be-e9dd-11ea-9fa5-0200cd936042';
        $curl = curl_init();
        // $postData = [
        //     'From' => "VZNAIZ",
        //     'To' => $mobile,
        //     'TemplateName' => "NAIZ-OTP",
        //     'VAR1' => $otp
        // ];
        $from="VZNAIZ";
        $templateName='NAIZ-OTP';
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://2factor.in/API/V1/8fd1c12c-f91a-11ea-9fa5-0200cd936042/SMS/" . $mobile . "/" . $otp . "/".$templateName,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS =>  "",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));
       
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $return['otp'] = $otp;
        if ($err) {
        return 'error';
        $response['Status'] = 'error';
        $response['Details'] = $err;
        $return['status'] =false;
        return $return;
        } else {
        $return['status'] =true;
        return $return;
        }
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

    public function isValidCategoryId($category_uid)
    {
        $result = mysqli_query($this->mysqli, "SELECT id FROM `category` WHERE uid = '$category_uid' AND status = 'active'");
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $category_id = $row['id'];
            return $category_id;
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
