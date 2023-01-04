<?php

require_once("Rest.inc.php");

require_once 'db_user.php';
require_once 'auth.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;

require '../../../vendor/autoload.php';

class API extends REST
{
    const ENV = "PROD";
    const URL = self::ENV == 'DEV' ? 'http://localhost/naiz_web/' : 'https://admin.naizapp.com/';
    const WEB_URL = self::ENV == 'DEV' ? 'http://localhost/naiz_webapp/' : 'https://naizapp.com/';

    const EMAIL = 'naiztrading2021@gmail.com';
    const EMAIL_PASSWORD = 'xktiublkkpriftmm';
    const RAZOR_KEY_ID = 'rzp_live_ZJ78Jg5QfGVwb8';
    const RAZOR_KEY_SECRET = '61z64ax77EuwdgEwkTWPV02n';

    private $mysqli = NULL;
    public $auth, $functions, $db_user, $config;

    public function __construct()
    {
        parent::__construct();                // Init parent contructor
        $this->auth = new auth();
        $this->functions = new include_fns();
        $this->config = $config = new config();
        $this->db_user = new db_user();
        $this->mysqli = new mysqli($config->getDBHOST(), $config->getDBUSER(), $config->getDBPASS(), $config->getDBDB());

    }

    public function processApi()
    {
        $data = '';
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
        foreach ($this->_request as $param_name => $param_val) {
            $data .= $param_name . '-' . $param_val . ',';
        }
        if ((int)method_exists($this, $func) > 0) {
            $user_uid = isset($this->_request['uid']) ? $this->_request['uid'] : null;
            if ($user_uid) {
                $user_id = $this->db_user->getUserId($user_uid);
            } else {
                $user_id = 'null';
            }
            $this->db_user->add_api_data($user_id, $func, $data);

            $this->$func();
        }
    }

    public function user_register()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = $this->create_random_string(16);
        $full_name = isset($this->_request['full_name']) ? mysqli_real_escape_string($this->mysqli, $this->_request['full_name']) : null;
        $email = isset($this->_request['email']) ? mysqli_real_escape_string($this->mysqli, $this->_request['email']) : null;
        $mobile = isset($this->_request['mobile']) ? mysqli_real_escape_string($this->mysqli, $this->_request['mobile']) : null;
        $pswd = isset($this->_request['password']) ? mysqli_real_escape_string($this->mysqli, $this->_request['password']) : null;
        $ps = hash('sha512', $pswd);
        $salt = hash('sha512', $ps);
        $password = hash('sha512', $ps . $salt);

        if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
            $success = array('status' => "Failed", 'msg' => "Invalid Email");
            $this->response($this->json($success), 200);
        }

        if (!preg_match('/^[0-9]{10}+$/', $mobile)) {
            $success = array('status' => "Failed", 'msg' => "Invalid Mobile");
            $this->response($this->json($success), 200);
        }

        $user = mysqli_query($this->mysqli, "SELECT id 
                                                    FROM `user`
                                                    WHERE (email = '$email'
                                                    OR mobile = '$mobile')");
        if (!mysqli_num_rows($user)) {
            $user_rlt = mysqli_query($this->mysqli, "INSERT INTO `user` (uid, full_name, email, mobile, password, salt)
                                                            VALUES('$uid', '$full_name', '$email', '$mobile', '$password', '$salt')");
            if ($user_rlt) {
                $success = array('status' => "Success", 'msg' => "Registration Success", 'uid' => $uid);
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Registration Failed");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "Email/Mobile Already Exists");
            $this->response($this->json($success), 200);
        }
    }

    public function user_login()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $email = isset($this->_request['email']) ? mysqli_real_escape_string($this->mysqli, $this->_request['email']) : null;
        $pswd = isset($this->_request['password']) ? mysqli_real_escape_string($this->mysqli, $this->_request['password']) : null;
        $ps = hash('sha512', $pswd);
        $salt = hash('sha512', $ps);
        $password = hash('sha512', $ps . $salt);

        if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
            $success = array('status' => "Failed", 'msg' => "Invalid Email");
            $this->response($this->json($success), 200);
        }

        $user = mysqli_query($this->mysqli, "SELECT *
                                                    FROM `user`
                                                    WHERE email = '$email'
                                                    AND password = '$password'
                                                    AND status = 'active'");
        if (mysqli_num_rows($user)) {
            $row = $user->fetch_assoc();
            $user_data['uid'] = $row['uid'];
            $user_data['full_name'] = $row['full_name'];
            $user_data['mobile'] = $row['mobile'];
            $success = array('status' => "Success", 'msg' => "Login Success", 'user' => $user_data);
            $this->response($this->json($success), 200);
        } else {
            $success = array('status' => "Failed", 'msg' => "Login Failed");
            $this->response($this->json($success), 200);
        }
    }

    public function change_password()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $current_password = isset($this->_request['current_password']) ? mysqli_real_escape_string($this->mysqli, $this->_request['current_password']) : null;
        $new_password = isset($this->_request['new_password']) ? mysqli_real_escape_string($this->mysqli, $this->_request['new_password']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $current_ps = hash('sha512', $current_password);
            $salt = hash('sha512', $current_ps);
            $current_pswd = hash('sha512', $current_ps . $salt);
            $result = mysqli_query($this->mysqli, "SELECT id FROM `user` WHERE id = '$user_id' AND password = '$current_pswd'");
            if (mysqli_num_rows($result)) {
                $ps = hash('sha512', $new_password);
                $salt = hash('sha512', $ps);
                $password = hash('sha512', $ps . $salt);
                $rlt = mysqli_query($this->mysqli, "UPDATE `user` SET password ='$password', salt ='$salt' WHERE id = '$user_id'");
                if ($rlt) {
                    $success = array('status' => "Success", 'msg' => "Password Updated Successfully");
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Failed", 'msg' => "Update Failed");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Wrong Current Password");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_home_page()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;

        $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
        if ($vendor_id) {
            //get new launches list
            $new_launches = mysqli_query($this->mysqli, "SELECT product.*, 
                                                                       MIN(product_size.price) AS price,
                                                                       MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                                                                       MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                                                                       product_image.image, 
                                                                       top_product.type,
                                                                       GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category 
                                                                FROM product 
                                                                INNER JOIN product_size 
                                                                ON product_size.product_id = product.id
                                                                INNER JOIN product_image 
                                                                ON product_image.product_id = product.id 
                                                                INNER JOIN top_product
                                                                ON top_product.product_id = product.id
                                                                INNER JOIN vendor_stock
                                                                ON vendor_stock.product_size_id = product_size.id
                                                                LEFT JOIN product_category
                                                                ON product_category.product_id = product.id
                                                                LEFT JOIN category
                                                                ON category.id = product_category.category_id
                                                                AND category.status = 'active'
                                                                WHERE product.status = 'active'
                                                                AND product_size.status = 'active'
                                                                AND top_product.type = 'launches'
                                                                AND vendor_stock.vendor_id = '$vendor_id'
                                                                GROUP BY product.id 
                                                                ORDER BY top_product.`timestamp` ASC
                                                                LIMIT 8");

            $new_launches_data = array();
            if (mysqli_num_rows($new_launches)) {
                while ($row = $new_launches->fetch_assoc()) {
                    if ($row['category']) {
                        $new_launches_rlt['uid'] = $row['uid'];
                        $new_launches_rlt['name'] = $row['name'];
                        $new_launches_rlt['type'] = $row['type'];
                        if ($row['offer_price'] > 0) {
                            if ($row['offer_price'] < $row['price']) {
                                $new_launches_rlt['old_price'] = $row['old_price'];
                                $new_launches_rlt['display_price'] = $row['offer_price'];
                            } else {
                                $new_launches_rlt['old_price'] = '';
                                $new_launches_rlt['display_price'] = $row['price'];
                            }
                        } else {
                            $new_launches_rlt['old_price'] = '';
                            $new_launches_rlt['display_price'] = $row['price'];
                        }
                        $new_launches_rlt['image'] = self::URL . 'vendor_data/product/' . $row['image'];
                        array_push($new_launches_data, $new_launches_rlt);
                    }
                }
            }
            $home_page_data['new_launches'] = $new_launches_data;

            //get top picks list
            $top_picks = mysqli_query($this->mysqli, "SELECT product.*, 
                                                                    MIN(product_size.price) AS price,
                                                                    MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                                                                    MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                                                                    product_image.image,
                                                                    top_product.type,
                                                                    GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category  
                                                             FROM product 
                                                             INNER JOIN product_size 
                                                             ON product_size.product_id = product.id 
                                                             INNER JOIN product_image 
                                                             ON product_image.product_id = product.id
                                                             INNER JOIN top_product
                                                             ON top_product.product_id = product.id
                                                             INNER JOIN vendor_stock
                                                             ON vendor_stock.product_size_id = product_size.id
                                                             LEFT JOIN product_category
                                                             ON product_category.product_id = product.id
                                                             LEFT JOIN category
                                                             ON category.id = product_category.category_id
                                                             AND category.status = 'active'
                                                             WHERE product.status = 'active'
                                                             AND product_size.status = 'active'
                                                             AND top_product.type = 'picks'
                                                             AND vendor_stock.vendor_id = '$vendor_id'
                                                             GROUP BY product.id 
                                                             ORDER BY top_product.`timestamp` ASC
                                                             LIMIT 8");
            $top_picks_data = array();
            if (mysqli_num_rows($top_picks)) {
                while ($row1 = $top_picks->fetch_assoc()) {
                    if ($row1['category']) {
                        $top_picks_rlt['uid'] = $row1['uid'];
                        $top_picks_rlt['name'] = $row1['name'];
                        $top_picks_rlt['type'] = $row1['type'];
                        if ($row1['offer_price'] > 0) {
                            if ($row1['offer_price'] < $row1['price']) {
                                $top_picks_rlt['old_price'] = $row1['old_price'];
                                $top_picks_rlt['display_price'] = $row1['offer_price'];
                            } else {
                                $top_picks_rlt['old_price'] = '';
                                $top_picks_rlt['display_price'] = $row1['price'];
                            }
                        } else {
                            $top_picks_rlt['old_price'] = '';
                            $top_picks_rlt['display_price'] = $row1['price'];
                        }
                        $top_picks_rlt['image'] = self::URL . 'vendor_data/product/' . $row1['image'];
                        array_push($top_picks_data, $top_picks_rlt);
                    }
                }
            }
            $home_page_data['top_picks'] = $top_picks_data;

            //get most viewed list
            $most_viewed = mysqli_query($this->mysqli, "SELECT product.*, 
                                                                      MIN(product_size.price) AS price,
                                                                      MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                                                                      MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                                                                      product_image.image,
                                                                      top_product.type,
                                                                      GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category  
                                                               FROM product 
                                                               INNER JOIN product_size 
                                                               ON product_size.product_id = product.id 
                                                               INNER JOIN product_image 
                                                               ON product_image.product_id = product.id
                                                               INNER JOIN top_product
                                                               ON top_product.product_id = product.id
                                                               INNER JOIN vendor_stock
                                                               ON vendor_stock.product_size_id = product_size.id
                                                               LEFT JOIN product_category
                                                               ON product_category.product_id = product.id
                                                               LEFT JOIN category
                                                               ON category.id = product_category.category_id
                                                               AND category.status = 'active'
                                                               WHERE product.status = 'active'
                                                               AND product_size.status = 'active'
                                                               AND top_product.type = 'viewed'
                                                               AND vendor_stock.vendor_id = '$vendor_id'
                                                               GROUP BY product.id 
                                                               ORDER BY top_product.`timestamp` ASC
                                                               LIMIT 8");
            $most_viewed_data = array();
            if (mysqli_num_rows($most_viewed)) {
                while ($row2 = $most_viewed->fetch_assoc()) {
                    if ($row2['category']) {
                        $most_viewed_rlt['uid'] = $row2['uid'];
                        $most_viewed_rlt['name'] = $row2['name'];
                        $most_viewed_rlt['type'] = $row2['type'];
                        if ($row2['offer_price'] > 0) {
                            if ($row2['offer_price'] < $row2['price']) {
                                $most_viewed_rlt['old_price'] = $row2['old_price'];
                                $most_viewed_rlt['display_price'] = $row2['offer_price'];
                            } else {
                                $most_viewed_rlt['old_price'] = '';
                                $most_viewed_rlt['display_price'] = $row2['price'];
                            }
                        } else {
                            $most_viewed_rlt['old_price'] = '';
                            $most_viewed_rlt['display_price'] = $row2['price'];
                        }
                        $most_viewed_rlt['image'] = self::URL . 'vendor_data/product/' . $row2['image'];
                        array_push($most_viewed_data, $most_viewed_rlt);
                    }
                }
            }
            $home_page_data['most_viewed'] = $most_viewed_data;

            //get most popular list
            $most_popular = mysqli_query($this->mysqli, "SELECT product.*, 
                                                                       MIN(product_size.price) AS price,
                                                                       MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                                                                       MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                                                                       product_image.image,
                                                                       top_product.type,
                                                                       GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category  
                                                                FROM product 
                                                                INNER JOIN product_size 
                                                                ON product_size.product_id = product.id 
                                                                INNER JOIN product_image 
                                                                ON product_image.product_id = product.id
                                                                INNER JOIN top_product
                                                                ON top_product.product_id = product.id
                                                                INNER JOIN vendor_stock
                                                                ON vendor_stock.product_size_id = product_size.id
                                                                LEFT JOIN product_category
                                                                ON product_category.product_id = product.id
                                                                LEFT JOIN category
                                                                ON category.id = product_category.category_id
                                                                AND category.status = 'active'
                                                                WHERE product.status = 'active'
                                                                AND product_size.status = 'active'
                                                                AND top_product.type = 'popular'
                                                                AND vendor_stock.vendor_id = '$vendor_id'
                                                                GROUP BY product.id 
                                                                ORDER BY product.`timestamp` ASC
                                                                LIMIT 8");
            $most_popular_data = array();
            if (mysqli_num_rows($most_popular)) {
                while ($row3 = $most_popular->fetch_assoc()) {
                    if ($row3['category']) {
                        $most_popular_rlt['uid'] = $row3['uid'];
                        $most_popular_rlt['name'] = $row3['name'];
                        $most_popular_rlt['type'] = $row3['type'];
                        if ($row3['offer_price'] > 0) {
                            if ($row3['offer_price'] < $row3['price']) {
                                $most_popular_rlt['old_price'] = $row3['old_price'];
                                $most_popular_rlt['display_price'] = $row3['offer_price'];
                            } else {
                                $most_popular_rlt['old_price'] = '';
                                $most_popular_rlt['display_price'] = $row3['price'];
                            }
                        } else {
                            $most_popular_rlt['old_price'] = '';
                            $most_popular_rlt['display_price'] = $row3['price'];
                        }
                        $most_popular_rlt['image'] = self::URL . 'vendor_data/product/' . $row3['image'];
                        array_push($most_popular_data, $most_popular_rlt);
                    }
                }
            }
            $home_page_data['most_popular'] = $most_popular_data;

            //get most rated products
            $most_rated = mysqli_query($this->mysqli, "SELECT AVG(product_review.rating) AS rating,
                                                                    product_image.image,
                                                                    MIN(product_size.price) AS price,
                                                                    MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                                                                    MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                                                                    product.*,
                                                                    GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category 
                                                                FROM product 
                                                                INNER JOIN product_review
                                                                ON product_review.product_id = product.id
                                                                INNER JOIN product_image
                                                                ON product_image.product_id = product.id
                                                                INNER JOIN product_size 
                                                                ON product_size.product_id = product.id
                                                                INNER JOIN vendor_stock
                                                                ON vendor_stock.product_size_id = product_size.id
                                                                LEFT JOIN product_category
                                                                ON product_category.product_id = product.id
                                                                LEFT JOIN category
                                                                ON category.id = product_category.category_id
                                                                AND category.status = 'active' 
                                                                WHERE product.status = 'active' 
                                                                AND vendor_stock.vendor_id = '$vendor_id'
                                                                GROUP BY product.id 
                                                                ORDER BY rating DESC
                                                                LIMIT 4");
            $most_rated_data = array();
            if (mysqli_num_rows($most_rated)) {
                while ($row5 = $most_rated->fetch_assoc()) {
                    if ($row5['category']) {
                        $most_rated_rlt['uid'] = $row5['uid'];
                        $most_rated_rlt['name'] = $row5['name'];
                        if ($row5['offer_price'] > 0) {
                            if ($row5['offer_price'] < $row5['price']) {
                                $most_rated_rlt['old_price'] = $row5['old_price'];
                                $most_rated_rlt['display_price'] = $row5['offer_price'];
                            } else {
                                $most_rated_rlt['old_price'] = '';
                                $most_rated_rlt['display_price'] = $row5['price'];
                            }
                        } else {
                            $most_rated_rlt['old_price'] = '';
                            $most_rated_rlt['display_price'] = $row5['price'];
                        }
                        $most_rated_rlt['image'] = self::URL . 'vendor_data/product/' . $row5['image'];
                        array_push($most_rated_data, $most_rated_rlt);
                    }
                }
            }
            $home_page_data['most_rated'] = $most_rated_data;

            $category = mysqli_query($this->mysqli, "SELECT * FROM category
                                                        WHERE status = 'active'
                                                        ORDER BY `timestamp` ASC");
            $category_data = array();
            if (mysqli_num_rows($category)) {
                while ($row7 = $category->fetch_assoc()) {
                    $category_rlt['uid'] = $row7['uid'];
                    $category_rlt['name'] = $row7['name'];
                    $category_rlt['image'] = self::URL . 'vendor_data/category/' . $row7['image'];
                    array_push($category_data, $category_rlt);
                }
            }
            $home_page_data['category'] = $category_data;

            $category_product = mysqli_query($this->mysqli, "SELECT count(cat_table.category_id) AS category_count,
                                                                          cat_table.name, 
                                                                          cat_table.uid, 
                                                                          cat_table.image
                                                                    FROM (SELECT category.id AS category_id,
                                                                                 category.name,
                                                                                 category.uid,
                                                                                 category.image
                                                                           FROM product
                                                                           INNER JOIN product_category
                                                                           ON product_category.product_id = product.id
                                                                           INNER JOIN category
                                                                           ON category.id = product_category.category_id
                                                                           INNER JOIN product_size 
                                                                           ON product_size.product_id = product.id
                                                                           INNER JOIN vendor_stock
                                                                           ON vendor_stock.product_size_id = product_size.id 
                                                                           WHERE product.status = 'active' 
                                                                           AND vendor_stock.vendor_id = '$vendor_id'
                                                                           AND category.status = 'active'
                                                                           GROUP BY product.id) AS cat_table
                                                                     GROUP BY cat_table.category_id
                                                                     ORDER BY category_count DESC
                                                                     LIMIT 2");
            $category_product_data = array();
            if (mysqli_num_rows($category_product)) {
                while ($row8 = $category_product->fetch_assoc()) {
                    $category_product_rlt['uid'] = $row8['uid'];
                    $category_product_rlt['name'] = $row8['name'];
                    $category_product_rlt['image'] = self::URL . 'vendor_data/category/' . $row8['image'];
                    array_push($category_product_data, $category_product_rlt);
                }
            }
            $home_page_data['most_category_product'] = $category_product_data;

            $slider_rlt = mysqli_query($this->mysqli, "SELECT * FROM web_slider WHERE status = 'active'");
            $main_slider_rlt_data = array();

            if (mysqli_num_rows($slider_rlt)) {
                while ($row9 = $slider_rlt->fetch_assoc()) {
                    $home_slider_rlt['main_text'] = $row9['main_text'];
                    $home_slider_rlt['sub_text'] = $row9['sub_text'];
                    $home_slider_rlt['button_name'] = $row9['button_name'];
                    $home_slider_rlt['button_url'] = $row9['button_url'];
                    $home_slider_rlt['image'] = self::URL . 'vendor_data/slider/' . $row9['image'];
                    if ($row9['type'] == 'main') {
                        array_push($main_slider_rlt_data, $home_slider_rlt);
                    } else {
                        $home_page_data['home_center_slider'] = $home_slider_rlt;
                    }
                }
            }
            $home_page_data['home_main_slider'] = $main_slider_rlt_data;

            $success = array('status' => "Success", 'msg' => "Home Page Fetched", 'home_page' => $home_page_data);
            $this->response($this->json($success), 200);
        } else {
            $success = array('status' => "Failed", 'msg' => "Vendor is not available");
            $this->response($this->json($success), 200);
        }
    }

    public function update_user_profile()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $full_name = isset($this->_request['full_name']) ? mysqli_real_escape_string($this->mysqli, $this->_request['full_name']) : null;
        $email = isset($this->_request['email']) ? mysqli_real_escape_string($this->mysqli, $this->_request['email']) : null;
        $mobile = isset($this->_request['mobile']) ? mysqli_real_escape_string($this->mysqli, $this->_request['mobile']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
                $success = array('status' => "Failed", 'msg' => "Invalid Email");
                $this->response($this->json($success), 200);
            }

            if (!preg_match('/^[0-9]{10}+$/', $mobile)) {
                $success = array('status' => "Failed", 'msg' => "Invalid Mobile");
                $this->response($this->json($success), 200);
            }

            $user_rlt = mysqli_query($this->mysqli, "SELECT id 
                                                            FROM `user`
                                                            WHERE (email = '$email'
                                                            OR mobile = '$mobile')
                                                            AND id != '$user_id'");
            if (!mysqli_num_rows($user_rlt)) {
                $update = mysqli_query($this->mysqli, "UPDATE `user` 
                                                              SET full_name = '$full_name',
                                                                  email = '$email',
                                                                  mobile = '$mobile' 
                                                              WHERE id = '$user_id'");
                if ($update) {
                    $success = array('status' => "Success", 'msg' => "Updated Successfully");
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Failed", 'msg' => "Failed");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Email/Mobile Already Exists");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_user_address()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : 1;
        $limit = isset($this->_request['limit']) ? mysqli_real_escape_string($this->mysqli, $this->_request['limit']) : 15;
        $user_id = $this->db_user->isValidUserId($uid);

        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $lower_limit = ($page - 1) * $limit;
                $user_address = mysqli_query($this->mysqli, "SELECT SQL_CALC_FOUND_ROWS user_address.*,
                                                                          vendor_pincode.shipping_fee 
                                                                   FROM user_address
                                                                   LEFT JOIN vendor_pincode
                                                                   ON vendor_pincode.pincode = user_address.zip
                                                                   AND vendor_pincode.vendor_id = '$vendor_id'
                                                                   AND vendor_pincode.status = 'active'
                                                                   WHERE user_address.status = 'active'
                                                                   AND user_address.user_id = '$user_id'
                                                                   ORDER BY user_address.`timestamp` DESC
                                                                   LIMIT $lower_limit, $limit");

                $count_rlt = mysqli_query($this->mysqli, "SELECT FOUND_ROWS() AS data_count");
                $count_rlt = mysqli_fetch_assoc($count_rlt);
                $data_count = $count_rlt['data_count'];
                $total_pages = ceil($data_count / $limit);

                $user_address_data = array();
                if (mysqli_num_rows($user_address)) {
                    while ($row = $user_address->fetch_assoc()) {
                        $user_address_rlt['id'] = $row['id'];
                        $user_address_rlt['full_name'] = $row['full_name'];
                        $user_address_rlt['address'] = $row['address'];
                        $user_address_rlt['city_district'] = $row['city_district'];
                        $user_address_rlt['type'] = $row['type'];
                        $user_address_rlt['phone_number'] = $row['phone_number'];
                        $user_address_rlt['pincode'] = $row['zip'];
                        $user_address_rlt['shipping_fee'] = $row['shipping_fee'] ? $row['shipping_fee'] : '';
                        array_push($user_address_data, $user_address_rlt);
                    }
                }

                $success = array('status' => "Success", 'msg' => "Address Fetched", 'user_address_data' => $user_address_data, 'total_pages' => $total_pages);
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function add_user_address()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $full_name = isset($this->_request['full_name']) ? mysqli_real_escape_string($this->mysqli, $this->_request['full_name']) : null;
        $phone_number = isset($this->_request['phone_number']) ? mysqli_real_escape_string($this->mysqli, $this->_request['phone_number']) : null;
        $city_district = isset($this->_request['city_district']) ? mysqli_real_escape_string($this->mysqli, $this->_request['city_district']) : null;
        $zip = isset($this->_request['zip']) ? mysqli_real_escape_string($this->mysqli, $this->_request['zip']) : null;
        $address = isset($this->_request['address']) ? mysqli_real_escape_string($this->mysqli, $this->_request['address']) : null;
        $type = isset($this->_request['type']) ? mysqli_real_escape_string($this->mysqli, $this->_request['type']) : null;

        if (!preg_match('/^[0-9]{10}+$/', $phone_number)) {
            $success = array('status' => "Failed", 'msg' => "Invalid Phone Number");
            $this->response($this->json($success), 200);
        }

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $user_address_rlt = mysqli_query($this->mysqli, "INSERT INTO user_address (user_id, full_name, phone_number, 
                                                                                             city_district, zip, address, `type`)
                                                                    VALUES('$user_id', '$full_name', '$phone_number', 
                                                                           '$city_district', '$zip', '$address', '$type')");
            if ($user_address_rlt) {
                $success = array('status' => "Success", 'msg' => "Address Added Successfully");
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Failed to Add Address");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_vendor_pincode()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $user_id = $this->db_user->isValidUserId($uid);

        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $pincode_rlt = mysqli_query($this->mysqli, "SELECT * FROM vendor_pincode
                                                                  WHERE status = 'active'
                                                                  AND vendor_id = '$vendor_id'
                                                                  ORDER BY `timestamp` DESC");
                $pincode = array();
                if (mysqli_num_rows($pincode_rlt)) {
                    while ($row = $pincode_rlt->fetch_assoc()) {
                        array_push($pincode, $row['pincode']);
                    }
                }

                $success = array('status' => "Success", 'msg' => "Pincode Fetched", 'pincode' => $pincode);
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_cart_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $type = isset($this->_request['type']) ? mysqli_real_escape_string($this->mysqli, $this->_request['type']) : null;
        $user_id = $this->db_user->isValidUserId($uid);

        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);

            if ($vendor_id) {
                //get cart list

                $cart = mysqli_query($this->mysqli, "SELECT cart.id AS cart_id,
                                                               cart.count,
                                                               cart.color_id,
                                                               cart.warranty_id,
                                                               color.code AS color_code,
                                                               product.uid,
                                                               product.name,
                                                               product.status AS product_status,
                                                               product_image.image,
                                                               product_size.*,
                                                               vendor_stock.stock,
                                                               vendor.place,
                                                               tax.tax,
                                                               warranty.warranty,
                                                               warranty.status AS warranty_status,
                                                               product_size_color_warranty.price AS color_price,
                                                               product_size_color_warranty.offer_price AS color_offer_price,
                                                               product_color_image.image AS product_color_image_name,
                                                               GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category
                                                        FROM cart
                                                        INNER JOIN vendor
                                                        ON vendor.id = cart.vendor_id
                                                        LEFT JOIN product_size
                                                        ON product_size.id = cart.product_size_id
                                                        LEFT JOIN vendor_stock
                                                        ON vendor_stock.product_size_id = cart.product_size_id
                                                        AND vendor_stock.vendor_id = cart.vendor_id
                                                        AND (vendor_stock.color_id = cart.color_id
                                                        OR cart.color_id IS NULL)
                                                        LEFT JOIN color
                                                        ON color.id = cart.color_id
                                                        INNER JOIN product
                                                        ON product.id = product_size.product_id
                                                        INNER JOIN product_image
                                                        ON product_image.product_id = product.id
                                                        LEFT JOIN product_color_image 
                                                        ON product_color_image.product_id = product.id
                                                        AND product_color_image.color_id = color.id
                                                        LEFT JOIN product_size_color
                                                        ON product_size_color.product_size_id = cart.product_size_id
                                                        AND product_size_color.color_id = cart.color_id
                                                        LEFT JOIN warranty
                                                        ON warranty.id = cart.warranty_id
                                                        LEFT JOIN product_size_color_warranty
                                                        ON product_size_color_warranty.product_size_color_id = product_size_color.id
                                                        AND product_size_color_warranty.warranty_id = cart.warranty_id
                                                        LEFT JOIN tax
                                                        ON tax.id = product.tax_id
                                                        LEFT JOIN product_category
                                                        ON product_category.product_id = product.id
                                                        LEFT JOIN category
                                                        ON category.id = product_category.category_id
                                                        AND category.status = 'active'
                                                        WHERE cart.user_id = '$user_id'
                                                        AND cart.vendor_id = '$vendor_id'
                                                        GROUP BY cart_id
                                                        ORDER BY cart.timestamp DESC");
                $cart_data = array();
                $error_count = 0;
                $total_cost = 0;
                $total_tax = 0;
                if (mysqli_num_rows($cart)) {
                    while ($row = $cart->fetch_assoc()) {
                        $not_available_count = 0;
                        $cart_rlt['product_uid'] = $row['uid'];
                        $cart_rlt['name'] = $row['name'];
                        $cart_rlt['color_code'] = $row['color_code'];
                        $cart_rlt['image'] = $row['product_color_image_name'] ? (self::URL . 'vendor_data/product/' . $row['product_color_image_name']) : (self::URL . 'vendor_data/product/' . $row['image']);
                        $cart_rlt['stock'] = $row['stock'];
                        $cart_rlt['color_id'] = $row['color_id'];
                        $cart_rlt['is_warranty'] = $row['warranty_id'] ? 1 : 0;
                        $cart_rlt['warranty'] = [];
                        $cart_rlt['product_size_id'] = $row['id'];
                        $cart_rlt['size'] = $row['size'] . $row['size_unit'];
                        if ($row['category']) {
                            $cart_rlt['error'] = '';
                        } else {
                            $error_count++;
                            $not_available_count++;
                            $cart_rlt['error'] = 'Not Available';
                        }
                        if ($row['warranty_id']) {
                            $wrnty['cart_id'] = $row['cart_id'];
                            $wrnty['warranty_id'] = $row['warranty_id'];
                            $wrnty['warranty'] = $row['warranty'];
                            $wrnty['count'] = $row['count'];
                            if ($row['color_price'] == null) {
                                $wrnty['price'] = 0;
                                $wrnty['offer_price'] = 0;
                                $display_price = 0;
                                $wrnty['display_price'] = 0;
                                $wrnty['total_display_price'] = 0;
                                $wrnty['error'] = 'Not Available';
                                $error_count++;
                            } else {
                                $wrnty['price'] = $row['color_price'];
                                $wrnty['offer_price'] = $row['color_offer_price'];
                                $display_price = $row['color_offer_price'] > 0 ? $row['color_offer_price'] : $row['color_price'];
                                $wrnty['display_price'] = $display_price;
                                $wrnty['total_display_price'] = ($row['count'] * $display_price);
                                $wrnty['error'] = '';
                            }
                            array_push($cart_rlt['warranty'], $wrnty);
                        } else {
                            $cart_rlt['warranty_id'] = '';
                            $cart_rlt['count'] = $row['count'];
//                            $cart_rlt['total_count'] = $row['count'];
                            $cart_rlt['price'] = $row['price'];
                            $cart_rlt['offer_price'] = $row['offer_price'];
                            $display_price = $row['offer_price'] > 0 ? $row['offer_price'] : $row['price'];
                            $cart_rlt['display_price'] = $display_price;
                            $cart_rlt['total_display_price'] = ($row['count'] * $display_price);
                        }
                        $cart_rlt['product_status'] = $row['product_status'];
                        $cart_rlt['product_size_status'] = $row['status'];
                        $cart_rlt['vendor'] = $row['place'];
                        $total_cost = $total_cost + ($row['count'] * $display_price);
                        if ($row['tax']) {
                            $total_tax = $total_tax + (($row['count'] * $display_price) * ($row['tax'] / 100));
                        }
                        $stock = $row['stock'];
                        $count = $row['count'];

                        $color_id = $row['color_id'];
                        $product_size_id = $row['id'];

                        if ($not_available_count == 0) {
                            if ($row['product_status'] == 'active') {
                                if ($row['status'] == 'active') {
                                    if ($stock != 'unlimited') {
                                        if ($row['warranty_id']) {
                                            $warranty_query = mysqli_query($this->mysqli, "SELECT SUM(cart.count) AS total_cart_count
                                                                                              FROM cart
                                                                                              WHERE cart.user_id = '$user_id'
                                                                                              AND cart.vendor_id = '$vendor_id'
                                                                                              AND cart.product_size_id = '$product_size_id'
                                                                                              AND cart.color_id = '$color_id'
                                                                                              AND cart.warranty_id IS NOT NULL");
                                            if (mysqli_num_rows($warranty_query)) {
                                                $row_warranty = mysqli_fetch_array($warranty_query);
                                                $count = $row_warranty['total_cart_count'];
//                                            $cart_rlt['total_count'] = $count;
                                            }
                                        }

                                        if ($stock == 0) {
                                            $error_count++;
                                            $cart_rlt['error'] = 'Out of Stock';
                                        } else if ($count > $stock) {
                                            $error_count++;
                                            if ($stock) {
                                                if ($row['warranty_id']) {
                                                    $cart_rlt['error'] = 'Total ' . $stock . ' is Available';
                                                } else {
                                                    $cart_rlt['error'] = 'Only ' . $stock . ' is Available';
                                                }
                                            } else {
                                                $cart_rlt['error'] = 'Not Available';
                                            }
                                        } else {
                                            $cart_rlt['error'] = '';
                                        }
                                    } else {
                                        $cart_rlt['error'] = '';
                                    }

                                } else {
                                    $error_count++;
                                    $cart_rlt['error'] = 'Not Available';
                                }
                            } else {
                                $error_count++;
                                $cart_rlt['error'] = 'Not Available';
                            }
                        }

                        $is_exist = array_filter($cart_data, function ($var) use ($color_id, $product_size_id) {
                            return ($var['color_id'] == $color_id && $var['product_size_id'] == $product_size_id);
                        });

                        if (count($is_exist) > 0) {
                            foreach ($is_exist as $key => $new_warranty) {
                                if ($row['warranty_id']) {
                                    array_push($cart_data[$key]['warranty'], $cart_rlt['warranty'][0]);
                                }
                            }
                        } else {
                            array_push($cart_data, $cart_rlt);
                        }
                    }

                    if ($type == 'checkout') {
                        if ($error_count == 0) {
                            if ($total_cost < 500) {
                                $success = array('status' => "Failed", 'msg' => 'Minimum order value must be Rs. 500');
                                $this->response($this->json($success), 200);
                            }
                        }
                    }

                    $cart_detail['total_tax'] = round($total_tax, 2);
                    $cart_detail['cart'] = $cart_data;
                    $cart_detail['total_cost'] = $total_cost;
                    $success = array('status' => "Success", 'msg' => "Cart Details Fetched", 'cart_detail' => $cart_detail, 'error_count' => $error_count);
                    $this->response($this->json($success), 200);
                } else {
                    $cart_detail['total_tax'] = 0;
                    $cart_detail['cart'] = $cart_data;
                    $cart_detail['total_cost'] = $total_cost;

                    $success = array('status' => "Success", 'msg' => "Cart Empty", 'cart_detail' => $cart_detail, 'error_count' => $error_count);
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function add_to_cart()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $product_uid = isset($this->_request['product_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['product_uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $product_size_id = isset($this->_request['product_size_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['product_size_id']) : null;
        $count = isset($this->_request['count']) ? mysqli_real_escape_string($this->mysqli, $this->_request['count']) : null;
        $color_id = isset($this->_request['color_id']) && mysqli_real_escape_string($this->mysqli, $this->_request['color_id']) != '' ? "'" . mysqli_real_escape_string($this->mysqli, $this->_request['color_id']) . "'" : 'null';
        $warranty_id = isset($this->_request['warranty_id']) && mysqli_real_escape_string($this->mysqli, $this->_request['warranty_id']) != '' ? "'" . mysqli_real_escape_string($this->mysqli, $this->_request['warranty_id']) . "'" : 'null';
        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : null;

        if ($warranty_id == 0) {
            $success = array('status' => "Failed", 'msg' => "Warranty not found");
            $this->response($this->json($success), 200);
        };

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $product_id = $this->db_user->isValidProductId($product_uid);
                if ($product_id) {
                    if ($color_id == 'null' && $warranty_id == 'null') {
                        $cart_rlt = mysqli_query($this->mysqli, "SELECT id FROM cart 
                                                                    WHERE product_size_id = '$product_size_id'
                                                                    AND color_id IS NULL
                                                                    AND warranty_id IS NULL
                                                                    AND vendor_id = '$vendor_id'
                                                                    AND user_id = '$user_id'");
                    } else if ($color_id == 'null' && $warranty_id != 'null') {
                        $cart_rlt = mysqli_query($this->mysqli, "SELECT id FROM cart 
                                                                    WHERE product_size_id = '$product_size_id'
                                                                    AND color_id IS NULL
                                                                    AND warranty_id = $warranty_id
                                                                    AND vendor_id = '$vendor_id'
                                                                    AND user_id = '$user_id'");
                    } else if ($color_id != 'null' && $warranty_id == 'null') {
                        $cart_rlt = mysqli_query($this->mysqli, "SELECT id FROM cart 
                                                                    WHERE product_size_id = '$product_size_id'
                                                                    AND color_id = $color_id
                                                                    AND warranty_id IS NULL
                                                                    AND vendor_id = '$vendor_id'
                                                                    AND user_id = '$user_id'");
                    } else {
                        $cart_rlt = mysqli_query($this->mysqli, "SELECT id FROM cart 
                                                                    WHERE product_size_id = '$product_size_id'
                                                                    AND color_id = $color_id
                                                                    AND warranty_id = $warranty_id
                                                                    AND vendor_id = '$vendor_id'
                                                                    AND user_id = '$user_id'");
                    }


                    if (mysqli_num_rows($cart_rlt)) {
                        $row = mysqli_fetch_array($cart_rlt);
                        $cart_id = $row['id'];
                        if ($count == 0) {
                            if ($page == 'cart') {
                                $delete_cart_rlt = mysqli_query($this->mysqli, "DELETE FROM cart
                                                                                  WHERE id = '$cart_id'");
                                if ($delete_cart_rlt) {
                                    $success = array('status' => "Success", 'msg' => "Product Removed from Cart");
                                    $this->response($this->json($success), 200);
                                } else {
                                    $success = array('status' => "Failed", 'msg' => "Failed");
                                    $this->response($this->json($success), 200);
                                }
                            } else {
                                $success = array('status' => "Failed", 'msg' => "Failed");
                                $this->response($this->json($success), 200);
                            }
                        } else {
                            $product_rlt = mysqli_query($this->mysqli, "UPDATE cart
                                                                          SET `count` = '$count'
                                                                          WHERE id = '$cart_id'");
                        }
                    } else {
                        $product_rlt = mysqli_query($this->mysqli, "INSERT INTO cart (user_id, vendor_id, product_size_id, color_id, warranty_id,  `count`)
                                                                       VALUES('$user_id', '$vendor_id', '$product_size_id', $color_id, $warranty_id, '$count')");
                    }
                    if ($product_rlt) {
                        $success = array('status' => "Success", 'msg' => "Product Added to Cart");
                        $this->response($this->json($success), 200);
                    } else {
                        $success = array('status' => "Failed", 'msg' => "Failed");
                        $this->response($this->json($success), 200);
                    }
                } else {
                    $success = array('status' => "Failed", 'msg' => "Product not found");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function clear_cart()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $delete_cart_rlt = mysqli_query($this->mysqli, "DELETE FROM cart
                                                                       WHERE vendor_id = '$vendor_id'");
                if ($delete_cart_rlt) {
                    $success = array('status' => "Success", 'msg' => "Cart Cleared Successfully");
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Failed", 'msg' => "Failed");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function applied_promo_code()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $promo_code = isset($this->_request['promo_code']) ? mysqli_real_escape_string($this->mysqli, $this->_request['promo_code']) : null;
        $total_cost = isset($this->_request['total_cost']) ? mysqli_real_escape_string($this->mysqli, $this->_request['total_cost']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $current_date = date('Y-m-d');
                $promo_code_check = mysqli_query($this->mysqli, "SELECT SUM(CASE WHEN (table1.warranty_price > 0
                                                                               AND table1.warranty_price IS NOT NULL) 
                                                                               THEN (table1.warranty_price  * table1.count)
                                                                               ELSE (table1.price  * table1.count) END) AS display_price_val,
                                                                               table1.id, 
                                                                               table1.flat_rate, 
                                                                               table1.flat_rate_percent, 
                                                                               table1.category 
                                                                               FROM (SELECT promo_code.id, 
                                                                                            promo_code.flat_rate, 
                                                                                            promo_code.flat_rate_percent, 
                                                                                            GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category,
                                                                                            product_size.offer_price  AS offer_price_val, 
                                                                                            product_size.price  AS price_val,
                                                                                            product_size_color_warranty.offer_price AS warranty_offer_price_val, 
                                                                                            product_size_color_warranty.price AS warranty_price_val,
                                                                                            (CASE WHEN (product_size_color_warranty.offer_price > 0
                                                                                            AND product_size_color_warranty.offer_price IS NOT NULL) 
                                                                                            THEN product_size_color_warranty.offer_price
                                                                                            ELSE product_size_color_warranty.price END) AS warranty_price,
                                                                                            (CASE WHEN product_size.offer_price > 0 
                                                                                            THEN product_size.offer_price
                                                                                            ELSE product_size.price END) AS price,
                                                                                            cart.count,
                                                                                            cart.color_id,
                                                                                            product_size.id AS prdt_size_id
                                                                        FROM cart
                                                                        LEFT JOIN product_size
                                                                        ON product_size.id = cart.product_size_id
                                                                        LEFT JOIN product_size_color
                                                                        ON product_size_color.product_size_id = cart.product_size_id
                                                                        LEFT JOIN product_size_color_warranty
                                                                        ON product_size_color_warranty.product_size_color_id = product_size_color.id
                                                                        AND product_size_color.color_id = cart.color_id
                                                                        AND product_size_color_warranty.warranty_id = cart.warranty_id
                                                                        LEFT JOIN product_category
                                                                        ON product_category.product_id = product_size.product_id
                                                                        LEFT JOIN category
                                                                        ON category.id = product_category.category_id
                                                                        AND category.status = 'active' 
                                                                        LEFT JOIN category_promo_code
                                                                        ON category_promo_code.category_id = category.id
                                                                        LEFT JOIN promo_code
                                                                        ON promo_code.id = category_promo_code.promo_code_id
                                                                        AND promo_code.code = BINARY '$promo_code'
                                                                        AND (promo_code.expiry_date >= '$current_date'
                                                                        OR promo_code.expiry_date IS NULL)
                                                                        AND promo_code.status = 'active'
                                                                        WHERE cart.user_id = '$user_id'
                                                                        AND cart.vendor_id = '$vendor_id'
                                                                        AND (promo_code.flat_rate IS NOT NULL 
                                                                        OR promo_code.flat_rate_percent IS NOT NULL)
                                                                        GROUP BY cart.id) AS table1");
                if (mysqli_num_rows($promo_code_check)) {
                    $rate = 0;
                    $row = mysqli_fetch_array($promo_code_check);
                    if ($row['id']) {
                        $promo_code_id = $row['id'];
                        $display_price = $row['display_price_val'];
                        $flat_rate = $row['flat_rate'];
                        $flat_rate_percent = $row['flat_rate_percent'];
                        $category = $row['category'];
                        if ($flat_rate) {
                            if ($display_price >= $flat_rate) {
                                $rate = $flat_rate;
                            } else {
                                $success = array('status' => "Failed", 'msg' => "Price of the products under $category must be greater than Rs." . $flat_rate);
                                $this->response($this->json($success), 200);
                            }
                        } else if ($flat_rate_percent) {
                            $rate = $display_price * ($flat_rate_percent / 100);
                            if ($display_price >= $rate) {
                            } else {
                                $success = array('status' => "Failed", 'msg' => "Price of the products under $category must be greater than Rs." . $rate);
                                $this->response($this->json($success), 200);
                            }
                        }

                        $user_promo_code_exist_check = mysqli_query($this->mysqli, "SELECT id FROM orders 
                                                                                       WHERE user_id = '$user_id' 
                                                                                       AND promo_code_id = '$promo_code_id'
                                                                                       AND (status = 'payment_success'
                                                                                       OR status = 'order_success')");
                        if (!mysqli_num_rows($user_promo_code_exist_check)) {
                            $success = array('status' => "Success", 'msg' => "Promo Code Applied Successfully", 'flat_rate' => $rate, 'promo_code_id' => $promo_code_id);
                            $this->response($this->json($success), 200);
                        } else {
                            $success = array('status' => "Failed", 'msg' => "Already Applied", 'flat_rate' => 0, 'promo_code_id' => '');
                            $this->response($this->json($success), 200);
                        }
                    } else {
                        $success = array('status' => "Failed", 'msg' => "Invalid Promo Code");
                        $this->response($this->json($success), 200);
                    }
                } else {
                    $success = array('status' => "Failed", 'msg' => "Invalid Promo Code");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_order_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : 1;
        $user_id = $this->db_user->isValidUserId($uid);

        $limit = 15;
        $lower_limit = ($page - 1) * $limit;

        if ($user_id) {
            //get order list
            $orders = mysqli_query($this->mysqli, "SELECT SQL_CALC_FOUND_ROWS orders.*,
                                                                 vendor.place
                                                          FROM orders
                                                          INNER JOIN vendor
                                                          ON vendor.id = orders.vendor_id
                                                          WHERE orders.user_id = '$user_id'
                                                          ORDER BY orders.timestamp DESC
                                                          LIMIT $lower_limit, $limit");

            $count_rlt = mysqli_query($this->mysqli, "SELECT FOUND_ROWS() AS data_count");
            $count_rlt = mysqli_fetch_assoc($count_rlt);
            $data_count = $count_rlt['data_count'];
            $total_pages = ceil($data_count / $limit);

            $orders_datas = array();
            if (mysqli_num_rows($orders)) {
                $order_rlt = array();
                while ($row = $orders->fetch_assoc()) {
                    $order_rlt['uid'] = $row['uid'];
                    $order_rlt['total_cost'] = $row['total_cost'];
                    $order_rlt['status'] = ucwords(str_replace('_', ' ', $row['status']));
                    $order_rlt['place'] = $row['place'];
                    $order_rlt['order_type'] = $row['order_type'] == 'cod' ? "Cash on Delivery" : "Online Payment";
                    $order_rlt['timestamp'] = $row['timestamp'];
                    array_push($orders_datas, $order_rlt);
                }
                $success = array('status' => "Success", 'msg' => "Orders Fetched", 'orders' => $orders_datas, 'total_pages' => $total_pages);
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Success", 'msg' => "No Orders", 'orders' => $orders_datas, 'total_pages' => 0);
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_order_item_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $order_uid = isset($this->_request['order_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['order_uid']) : null;
        $user_id = $this->db_user->isValidUserId($uid);

        if ($user_id) {
            $order_id = $this->db_user->isValidOrderId($order_uid);

            if ($order_id) {
                $orders = mysqli_query($this->mysqli, "SELECT order_item.*,
                                                                 GROUP_CONCAT(CONCAT(order_item_data.field,':',order_item_data.value) separator ', ') AS order_datas,
                                                                 vendor.place,
                                                                 orders.status AS order_status
                                                          FROM orders 
                                                          INNER JOIN order_item 
                                                          ON order_item.order_id = orders.id 
                                                          INNER JOIN order_item_data 
                                                          ON order_item_data.order_item_id = order_item.id
                                                          INNER JOIN vendor
                                                          ON vendor.id = orders.vendor_id
                                                          LEFT JOIN color
                                                          ON color.id = order_item.color_id  
                                                          LEFT JOIN warranty
                                                          ON warranty.id = order_item.warranty_id  
                                                          WHERE orders.user_id = '$user_id'
                                                          AND orders.id = '$order_id'
                                                          GROUP BY order_item.id
                                                          ORDER BY order_item.timestamp DESC");
                $orders_datas = array();
                if (mysqli_num_rows($orders)) {
                    $order_rlt = array();
                    while ($row = $orders->fetch_assoc()) {
                        if ($row['order_datas']) {
                            $order_data = array();
                            $order_detail = explode(', ', $row['order_datas']);
                            foreach ($order_detail as $value) {
                                $field_value = explode(':', $value);
                                $order_data[$field_value[0]] = $field_value[1];
                            }
                        }
                        $order_rlt['name'] = $order_data['name'];
                        $order_rlt['size'] = $order_data['size'];
                        if (isset($order_data['color_code'])) {
                            $order_rlt['color_code'] = $order_data['color_code'];
                        }
                        if (isset($order_data['warranty'])) {
                            $order_rlt['warranty'] = $order_data['warranty'];
                        }
                        $order_rlt['image'] = self::URL . 'vendor_data/product/' . $order_data['image'];
                        $display_price = $row['offer_price'] > 0 ? $row['offer_price'] : $row['price'];
                        $order_rlt['stock_count'] = $row['count'];
                        $order_rlt['display_price'] = $display_price * $row['count'];
                        if ($row['order_status'] == 'payment_success' || $row['order_status'] == 'order_success') {
                            $order_rlt['status'] = $row['status'];
                        } else {
                            $order_rlt['status'] = '';
                        }
                        $order_rlt['timestamp'] = $row['timestamp'];
                        array_push($orders_datas, $order_rlt);
                    }
                    $success = array('status' => "Success", 'msg' => "Orders Fetched", 'order_item' => $orders_datas);
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Success", 'msg' => "No Orders", 'order_item' => $orders_datas);
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Order not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_product_details()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $product_uid = isset($this->_request['product_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['product_uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;

        $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
        if ($vendor_id) {
            $product_id = $this->db_user->isValidActiveProductId($product_uid);
            if ($product_id) {
                //get product details
                $product = mysqli_query($this->mysqli, "SELECT product.*,
                                                                      GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS category,
                                                                      GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', #') AS tag     
                                                               FROM product
                                                               LEFT JOIN product_category
                                                               ON product_category.product_id = product.id
                                                               LEFT JOIN category
                                                               ON category.id = product_category.category_id
                                                               AND category.status = 'active'
                                                               LEFT JOIN product_tag
                                                               ON product_tag.product_id = product.id
                                                               LEFT JOIN tag
                                                               ON tag.id = product_tag.tag_id
                                                               WHERE product.status = 'active'
                                                               AND product.id = '$product_id'");

                if (mysqli_num_rows($product)) {
                    $row = $product->fetch_assoc();
                    $product_detail['product_id'] = $product_id;
                    $product_detail['vendor_id'] = $vendor_id;
                    $product_detail['name'] = $row['name'];
                    $product_detail['description'] = $row['description'];
                    $product_detail['material'] = $row['material'];
                    $product_detail['brand'] = $row['brand'];
                    $product_detail['code'] = $row['code'];
                    $product_detail['batch_no'] = $row['batch_no'];
                    $product_detail['category'] = $row['category'];
                    $product_detail['tag'] = '#' . $row['tag'];

                    if ($row['category']) {
                    } else {
                        $success = array('status' => "Failed", 'msg' => "Product not found");
                        $this->response($this->json($success), 200);
                    }
                }

                $product_image = mysqli_query($this->mysqli, "SELECT image    
                                                                     FROM product_image
                                                                     WHERE product_id = '$product_id'
                                                                     ORDER BY `timestamp` ASC");
                $product_image_data = array();
                if (mysqli_num_rows($product_image)) {
                    while ($row1 = $product_image->fetch_assoc()) {
                        $product_image_data_rlt = self::URL . 'vendor_data/product/' . $row1['image'];
                        array_push($product_image_data, $product_image_data_rlt);
                    }
                }
                $product_detail['product_image'] = $product_image_data;

                $product_size = mysqli_query($this->mysqli, "SELECT product_size.id,
                                                                            product_size.size,
                                                                            product_size.size_unit,
                                                                            (CASE WHEN product_size.offer_price > 0 
                                                                            THEN product_size.offer_price 
                                                                            ELSE product_size.price END) AS display_price
                                                                    FROM product_size
                                                                    INNER JOIN vendor_stock
                                                                    ON vendor_stock.product_size_id = product_size.id
                                                                    WHERE product_size.product_id = '$product_id'
                                                                    AND vendor_stock.vendor_id = '$vendor_id'
                                                                    AND product_size.status = 'active'
                                                                    GROUP BY product_size.id
                                                                    ORDER BY display_price ASC");
                $product_size_data = array();
                if (mysqli_num_rows($product_size)) {
                    while ($row2 = $product_size->fetch_assoc()) {
                        $product_size_data_rlt['id'] = $row2['id'];
                        $product_size_data_rlt['size'] = $row2['size'] . $row2['size_unit'];
                        array_push($product_size_data, $product_size_data_rlt);
                    }
                }
                $product_detail['product_size'] = $product_size_data;

                $prdt_review_query = mysqli_query($this->mysqli, "SELECT COUNT(id) AS count,
                                                                             SUM(rating) AS rating
                                                                   FROM product_review
                                                                   WHERE product_id = '$product_id'
                                                                   ORDER BY `timestamp` DESC");
                $review_count = $prdt_review_query->fetch_assoc();
                $total_review_user = $review_count['count'];
                $total_rating = $review_count['rating'];

                $average = $total_review_user > 0 ? $total_rating / $total_review_user : 0;
                $product_detail['total_review_count'] = $total_review_user;
                $product_detail['product_rating'] = round($average);

                $success = array('status' => "Success", 'msg' => "Product Details Fetched", 'product_detail' => $product_detail);
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Product not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "Vendor is not available");
            $this->response($this->json($success), 200);
        }
    }

    public function get_product_review()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : 1;
        $product_uid = isset($this->_request['product_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['product_uid']) : null;

        $product_id = $this->db_user->isValidProductId($product_uid);
        if ($product_id) {
            $limit = 5;
            $lower_limit = ($page - 1) * $limit;

            $prdt_review = mysqli_query($this->mysqli, "SELECT SQL_CALC_FOUND_ROWS product_review.*,
                                                                          `user`.full_name,
                                                                          `user`.uid
                                                                   FROM product_review
                                                                   INNER JOIN `user`
                                                                   ON `user`.id = product_review.user_id
                                                                   WHERE product_review.product_id = '$product_id'
                                                                   ORDER BY product_review.timestamp DESC
                                                                   LIMIT $lower_limit, $limit");

            $count_rlt = mysqli_query($this->mysqli, "SELECT FOUND_ROWS() AS data_count");
            $count_rlt = mysqli_fetch_assoc($count_rlt);
            $data_count = $count_rlt['data_count'];
            $total_pages = ceil($data_count / $limit);

            $product_review_data = array();
            $total_rating = 0;
            $total_review_user = mysqli_num_rows($prdt_review);
            if (mysqli_num_rows($prdt_review)) {
                while ($row = $prdt_review->fetch_assoc()) {
                    $product_review_rlt['name'] = $row['full_name'];
                    $product_review_rlt['user_uid'] = $row['uid'];
                    $product_review_rlt['product_review_id'] = $row['id'];
                    $product_review_rlt['review'] = $row['review'];
                    $product_review_rlt['rating'] = $row['rating'];
                    date_default_timezone_set("Asia/Kolkata");
                    $time = new DateTime($row['timestamp'], new DateTimeZone('UTC'));
                    $time->setTimezone(new DateTimezone('Asia/Kolkata'));
                    $product_review_rlt['date'] = $time->format('d M, Y');
                    $total_rating = $total_rating + $row['rating'];
                    array_push($product_review_data, $product_review_rlt);
                }
            }
            $average = $total_review_user > 0 ? $total_rating / $total_review_user : 0;
            $product_data['product_review'] = $product_review_data;
            $product_data['product_rating'] = round($average);

            $success = array('status' => "Success", 'msg' => "Reviews Fetched", 'review' => $product_data, 'total_pages' => $total_pages);
            $this->response($this->json($success), 200);
        } else {
            $success = array('status' => "Failed", 'msg' => "Product not found");
            $this->response($this->json($success), 200);
        }

    }

    public function add_product_review()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $product_uid = isset($this->_request['product_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['product_uid']) : null;
        $rating = isset($this->_request['rating']) ? mysqli_real_escape_string($this->mysqli, $this->_request['rating']) : null;
        $review = isset($this->_request['review']) && mysqli_real_escape_string($this->mysqli, $this->_request['review']) != '' ? "'" . mysqli_real_escape_string($this->mysqli, $this->_request['review']) . "'" : 'null';

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $product_id = $this->db_user->isValidProductId($product_uid);
            if ($product_id) {
                $prdt_review_rlt = mysqli_query($this->mysqli, "INSERT INTO product_review (product_id, user_id, rating, review)
                                                                        VALUES('$product_id', '$user_id', '$rating', $review)");
                if ($prdt_review_rlt) {
                    date_default_timezone_set("Asia/Kolkata");
                    $time = new DateTime();
                    $time->setTimezone(new DateTimezone('Asia/Kolkata'));
                    $date = $time->format('d M, y');
                    $success = array('status' => "Success", 'msg' => "Review Added Successfully", "date" => $date);
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Failed", 'msg' => "Failed to Add Review");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Product not found");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function get_product_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : 1;
        $search = isset($this->_request['search']) ? mysqli_real_escape_string($this->mysqli, $this->_request['search']) : null;
        $category_id = isset($this->_request['category_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['category_id']) : null;
        $tag_id = isset($this->_request['tag_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['tag_id']) : null;
        $limit = isset($this->_request['limit']) ? mysqli_real_escape_string($this->mysqli, $this->_request['limit']) : 10;
        $price_start = isset($this->_request['price_start']) ? mysqli_real_escape_string($this->mysqli, $this->_request['price_start']) : null;
        $price_end = isset($this->_request['price_end']) ? mysqli_real_escape_string($this->mysqli, $this->_request['price_end']) : null;
        $sort_by = isset($this->_request['sort_by']) ? mysqli_real_escape_string($this->mysqli, $this->_request['sort_by']) : null;

        $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
        if ($vendor_id) {
            $lower_limit = ($page - 1) * $limit;

            $query = "SELECT SQL_CALC_FOUND_ROWS product.*, 
                             MIN(product_size.price) AS price,
                             MIN(CASE WHEN product_size.offer_price != 0 THEN offer_price END) AS offer_price,
                             MIN(CASE WHEN product_size.offer_price != 0 THEN price END) AS old_price,
                             AVG(product_review.rating) AS rating,
                             product_image.image
                      FROM product 
                      LEFT JOIN product_review  
                      ON product_review.product_id = product.id
                      INNER JOIN product_size 
                      ON product_size.product_id = product.id
                      INNER JOIN product_image 
                      ON product_image.product_id = product.id 
                      INNER JOIN vendor_stock
                      ON vendor_stock.product_size_id = product_size.id
                      INNER JOIN product_category
                      ON product_category.product_id = product.id
                      INNER JOIN category
                      ON category.id = product_category.category_id
                      AND category.status = 'active'
                      LEFT JOIN product_tag
                      ON product_tag.product_id = product.id
                      WHERE product.status = 'active'
                      AND product_size.status = 'active'
                      AND vendor_stock.vendor_id = '$vendor_id'";

            if ($category_id != '') {
                $query .= " AND product_category.category_id = '$category_id'";
            }

            if ($tag_id != '') {
                $query .= " AND product_tag.tag_id = '$tag_id'";
            }

            if ($search != '') {
                $query .= " AND product.name LIKE '%$search%'";
            }

            if ($price_start != '' && $price_end != '') {
                $query .= " AND (((product_size.price BETWEEN '$price_start' AND '$price_end') AND product_size.offer_price IN (0))
                OR ((product_size.offer_price BETWEEN '$price_start' AND '$price_end') AND product_size.offer_price NOT IN (0)))";
            }

            $query .= " GROUP BY product.id";
            if ($sort_by == 'avg_rating') {
                $query .= " ORDER BY rating DESC";
            } else {
                $query .= " ORDER BY vendor_stock.`timestamp` DESC";
            }
            $query .= " LIMIT $lower_limit, $limit";

            $list_query = mysqli_query($this->mysqli, $query);

            $count_rlt = mysqli_query($this->mysqli, "SELECT FOUND_ROWS() AS data_count");
            $count_rlt = mysqli_fetch_assoc($count_rlt);
            $data_count = $count_rlt['data_count'];
            $total_pages = ceil($data_count / $limit);

            $product_list = array();
            if (mysqli_num_rows($list_query)) {
                while ($row = $list_query->fetch_assoc()) {
                    $data = [];
                    $data['uid'] = $row['uid'];
                    $data['name'] = $row['name'];
                    $data['description'] = $row['description'];
                    $data['rating'] = round($row['rating']);
                    if ($row['offer_price'] > 0) {
                        if ($row['offer_price'] < $row['price']) {
                            $data['old_price'] = $row['old_price'];
                            $data['display_price'] = $row['offer_price'];
                        } else {
                            $data['old_price'] = '';
                            $data['display_price'] = $row['price'];
                        }
                    } else {
                        $data['old_price'] = '';
                        $data['display_price'] = $row['price'];
                    }
                    $data['image'] = self::URL . 'vendor_data/product/' . $row['image'];
                    array_push($product_list, $data);
                }
            }

            $success = array('status' => "Success", 'msg' => "Products Fetched", 'products' => $product_list,
                'total_pages' => $total_pages, 'total_count' => $data_count);
            $this->response($this->json($success), 200);
        } else {
            $success = array('status' => "Failed", 'msg' => "Vendor is not available");
            $this->response($this->json($success), 200);
        }
    }

    public function get_vendor_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $page = isset($this->_request['page']) ? mysqli_real_escape_string($this->mysqli, $this->_request['page']) : 1;

        $limit = 6;
        $lower_limit = ($page - 1) * $limit;
        //get store list
        $store_list = mysqli_query($this->mysqli, "SELECT SQL_CALC_FOUND_ROWS vendor.* FROM vendor
                                                          WHERE status = 'active'
                                                          LIMIT $lower_limit, $limit");

        $count_rlt = mysqli_query($this->mysqli, "SELECT FOUND_ROWS() AS data_count");
        $count_rlt = mysqli_fetch_assoc($count_rlt);
        $data_count = $count_rlt['data_count'];
        $total_pages = ceil($data_count / $limit);

        $store_list_data = array();
        if (mysqli_num_rows($store_list)) {
            while ($row5 = $store_list->fetch_assoc()) {
                $store_list_rlt['id'] = $row5['id'];
                $store_list_rlt['vendor_uid'] = $row5['uid'];
                $store_list_rlt['name'] = $row5['full_name'];
                $store_list_rlt['place'] = $row5['place'];
                $store_list_rlt['address'] = $row5['address'];
                $store_list_rlt['email'] = $row5['email'];
                $store_list_rlt['phone_number'] = $row5['phone_number'];
                $store_list_rlt['status'] = $row5['status'];
                array_push($store_list_data, $store_list_rlt);
            }
        }

        $success = array('status' => "Success", 'msg' => "Vendor List Fetched", 'store_list' => $store_list_data, 'total_pages' => $total_pages);
        $this->response($this->json($success), 200);

    }

    public function get_category_tag_list()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $vendor_id = $this->db_user->isValidVendorId($vendor_uid);

        $category = mysqli_query($this->mysqli, "SELECT category.*, 
                                                               COUNT(DISTINCT product.id) AS count
                                                        FROM category 
                                                        LEFT JOIN product_category ON product_category.category_id = category.id 
                                                        LEFT JOIN product ON product.id = product_category.product_id 
                                                        LEFT JOIN product_size ON product_size.product_id = product.id 
                                                        LEFT JOIN vendor_stock ON vendor_stock.product_size_id = product_size.id 
                                                        WHERE category.status = 'active' 
                                                        AND vendor_stock.vendor_id = '$vendor_id' 
                                                        AND product.status = 'active' 
                                                        AND product_size.status = 'active' 
                                                        GROUP BY category.id 
                                                        ORDER BY category.`timestamp` ASC");
        $category_data = array();
        if (mysqli_num_rows($category)) {
            while ($row = $category->fetch_assoc()) {
                $category_rlt['id'] = $row['id'];
                $category_rlt['uid'] = $row['uid'];
                $category_rlt['name'] = ucfirst($row['name']);
                $category_rlt['count'] = $row['count'];
                array_push($category_data, $category_rlt);
            }
        }

        $data['category'] = $category_data;

        $tag = mysqli_query($this->mysqli, "SELECT tag.*
                                                  FROM tag 
                                                  WHERE tag.status = 'active' 
                                                  ORDER BY tag.`timestamp` ASC");
        $tag_data = array();
        if (mysqli_num_rows($tag)) {
            while ($row1 = $tag->fetch_assoc()) {
                $tag_rlt['id'] = $row1['id'];
                $tag_rlt['name'] = ucfirst($row1['name']);
                array_push($tag_data, $tag_rlt);
            }
        }

        $data['tag'] = $tag_data;

        $success = array('status' => "Success", 'msg' => "List Fetched", 'data' => $data);
        $this->response($this->json($success), 200);
    }

    public function create_razorpay_order()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $amount = isset($this->_request['amount']) ? mysqli_real_escape_string($this->mysqli, $this->_request['amount']) : null;
        $promo_code_id = isset($this->_request['promo_code_id']) && mysqli_real_escape_string($this->mysqli, $this->_request['promo_code_id']) != '' ? "'" . mysqli_real_escape_string($this->mysqli, $this->_request['promo_code_id']) . "'" : 'null';
        $promo_code = isset($this->_request['promo_code']) && mysqli_real_escape_string($this->mysqli, $this->_request['promo_code']) != '' ? "'" . mysqli_real_escape_string($this->mysqli, $this->_request['promo_code']) . "'" : 'null';
        $flat_rate = isset($this->_request['flat_rate']) ? mysqli_real_escape_string($this->mysqli, $this->_request['flat_rate']) : null;
        $total_tax = isset($this->_request['tax']) ? mysqli_real_escape_string($this->mysqli, $this->_request['tax']) : null;
        $shipping_fee = isset($this->_request['shipping_fee']) ? mysqli_real_escape_string($this->mysqli, $this->_request['shipping_fee']) : null;
        $address_id = isset($this->_request['address_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['address_id']) : null;
        $order_type = isset($this->_request['order_type']) ? mysqli_real_escape_string($this->mysqli, $this->_request['order_type']) : null;
        $order_uid = $this->create_alpha_numeric_string(16);
        $order_id = '';
        $total_amount = round($amount, 2);

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $query = "SELECT cart.count,
                              cart.warranty_id,
                              product_size_color_warranty.price AS color_price,
                              product_size_color_warranty.offer_price AS color_offer_price,
                              color.id AS color_id,
                              color.code AS color_code,
                              color.name AS color_name,
                              warranty.warranty AS warranty_name,
                              vendor_stock.id AS vendor_stock_id,
                              vendor_stock.stock,
                              product.name,
                              product.material,
                              product.brand,
                              product.code,
                              product.batch_no,
                              product.status AS product_status,
                              product_size.*,
                              product_image.image,
                              tax.tax
                       FROM cart
                       INNER JOIN product_size
                       ON product_size.id = cart.product_size_id
                       INNER JOIN product
                       ON product.id = product_size.product_id
                       INNER JOIN product_image
                       ON product_image.product_id = product.id
                       LEFT JOIN tax
                       ON tax.id = product.tax_id
                       LEFT JOIN vendor_stock
                       ON vendor_stock.product_size_id = cart.product_size_id
                       AND vendor_stock.vendor_id = cart.vendor_id
                       AND (vendor_stock.color_id = cart.color_id
                       OR cart.color_id IS NULL)
                       LEFT JOIN color
                       ON color.id = cart.color_id
                       LEFT JOIN warranty
                       ON warranty.id = cart.warranty_id
                       LEFT JOIN product_size_color
                       ON product_size_color.product_size_id = cart.product_size_id 
                       AND product_size_color.color_id = cart.color_id
                       LEFT JOIN product_size_color_warranty
                       ON product_size_color_warranty.product_size_color_id = product_size_color.id 
                       AND product_size_color_warranty.warranty_id = cart.warranty_id
                       WHERE cart.user_id = '$user_id'
                       AND cart.vendor_id = '$vendor_id'
                       GROUP BY cart.id";

                $query_rlt = mysqli_query($this->mysqli, $query);
                if (mysqli_num_rows($query_rlt)) {
                    while ($row = mysqli_fetch_array($query_rlt)) {
                        $product_name = $row['name'];
                        $stock = $row['stock'];
                        $count = $row['count'];
                        $product_size_id = $row['id'];
                        $color_id = $row['color_id'];
                        if ($row['product_status'] == 'active') {
                            if ($row['status'] == 'active') {
                                if ($stock != 'unlimited') {
                                    if ($row['warranty_id']) {
                                        $warranty_query = mysqli_query($this->mysqli, "SELECT SUM(cart.count) AS total_cart_count
                                                                                              FROM cart
                                                                                              WHERE cart.user_id = '$user_id'
                                                                                              AND cart.vendor_id = '$vendor_id'
                                                                                              AND cart.product_size_id = '$product_size_id'
                                                                                              AND cart.color_id = '$color_id'
                                                                                              AND cart.warranty_id IS NOT NULL");
                                        if (mysqli_num_rows($warranty_query)) {
                                            $row_warranty = mysqli_fetch_array($warranty_query);
                                            $count = $row_warranty['total_cart_count'];
                                        }
                                    }
                                    if ($stock >= $count) {
                                    } else {
                                        $success = array('status' => "Failed", 'msg' => "Not Available", 'error_type' => 1);
                                        $this->response($this->json($success), 200);
                                    }
                                }
                            } else {
                                $success = array('status' => "Failed", 'msg' => "$product_name is Not Available", 'error_type' => 1);
                                $this->response($this->json($success), 200);
                            }
                        } else {
                            $success = array('status' => "Failed", 'msg' => "$product_name is Not Available", 'error_type' => 1);
                            $this->response($this->json($success), 200);
                        }
                    }
                }

                $query_rlt2 = mysqli_query($this->mysqli, $query);
                if (mysqli_num_rows($query_rlt2)) {
                    $promo_code_rlt = mysqli_query($this->mysqli, "SELECT *
                                                                          FROM promo_code
                                                                          WHERE id = $promo_code_id");
                    $flat_rate_percent = 0;
                    if (mysqli_num_rows($promo_code_rlt)) {
                        $promo_row = mysqli_fetch_array($promo_code_rlt);
                        $flat_rate_percent = $promo_row['flat_rate_percent'] ? $promo_row['flat_rate_percent'] : 0;
                    }

                    if ($order_type == 'online') {
                        $current_status = 'payment_cancelled';
                    } else {
                        $current_status = 'order_failed';
                    }

                    $orders = mysqli_query($this->mysqli, "INSERT INTO orders (uid, vendor_id, user_id,
                                                                                     promo_code_id, promo_code, flat_rate,
                                                                                     flat_rate_percent,
                                                                                     tax, shipping_fee, total_cost,
                                                                                     order_type, status)
                                                                  VALUES('$order_uid', '$vendor_id', '$user_id',
                                                                          $promo_code_id, $promo_code, '$flat_rate',
                                                                          '$flat_rate_percent',
                                                                          '$total_tax', '$shipping_fee', '$total_amount',
                                                                          '$order_type', '$current_status')");
                    $order_id = $this->mysqli->insert_id;
                    if ($order_id) {
                        $address_rlt = mysqli_query($this->mysqli, "SELECT * FROM user_address
                                                                           WHERE id = '$address_id'");
                        $full_name = '';
                        $phone_number = '';
                        $city_district = '';
                        $pincode = '';
                        $address = '';
                        $type = '';
                        if (mysqli_num_rows($address_rlt)) {
                            $address_row = mysqli_fetch_array($address_rlt);
                            $full_name = $address_row['full_name'];
                            $phone_number = $address_row['phone_number'];
                            $city_district = $address_row['city_district'];
                            $pincode = $address_row['zip'];
                            $address = $address_row['address'];
                            $type = $address_row['type'];
                        }

                        $order_address_rlt = mysqli_query($this->mysqli, "INSERT INTO order_address (order_id, full_name, phone_number,
                                                                                             city_district, zip, address, `type`)
                                                                                 VALUES('$order_id', '$full_name', '$phone_number',
                                                                                 '$city_district', '$pincode', '$address', '$type')");

                        $tax = 0;
                        while ($row2 = mysqli_fetch_array($query_rlt2)) {
                            $product_size_id = $row2['id'];
                            $color_id = $row2['color_id'];
                            $is_warranty_id = $row2['warranty_id'];
                            $warranty_id = $row2['warranty_id'] ? $row2['warranty_id'] : 'null';
                            if ($row2['warranty_id']) {
                                $price = $row2['color_price'];
                                $offer_price = $row2['color_offer_price'];
                            } else {
                                $price = $row2['price'];
                                $offer_price = $row2['offer_price'];
                            }
                            $count = $row2['count'];
                            $display_price = $offer_price > 0 ? $offer_price : $price;
                            $tax = ($count * $display_price) * ($row2['tax'] / 100);

                            $order_item_uid = $this->create_alpha_numeric_string(16);
                            if ($color_id) {
                                $order_items = mysqli_query($this->mysqli, "INSERT INTO order_item (uid, order_id, product_size_id, color_id, warranty_id, price, offer_price, `count`, tax)
                                                                               VALUES('$order_item_uid', '$order_id', '$product_size_id', $color_id, $warranty_id, '$price', '$offer_price', '$count', '$tax')");
                            } else {
                                $order_items = mysqli_query($this->mysqli, "INSERT INTO order_item (uid, order_id, product_size_id, price, offer_price, `count`, tax)
                                                                               VALUES('$order_item_uid', '$order_id', '$product_size_id', '$price', '$offer_price', '$count', '$tax')");
                            }
                            $order_item_id = $this->mysqli->insert_id;

                            $data = array();
                            $data['name'] = $row2['name'];
                            $data['image'] = $row2['image'];
                            if ($row2['material']) {
                                $data['material'] = $row2['material'];
                            }
                            if ($row2['brand']) {
                                $data['brand'] = $row2['brand'];
                            }
                            if ($row2['code']) {
                                $data['code'] = $row2['code'];
                            }
                            if ($row2['batch_no']) {
                                $data['batch_no'] = $row2['batch_no'];
                            }
                            if ($row2['size']) {
                                $data['size'] = $row2['size'] . $row2['size_unit'];
                            }
                            if ($color_id) {
                                $data['color'] = $row2['color_name'];
                                $data['color_code'] = $row2['color_code'];
                            }
                            if ($is_warranty_id) {
                                $data['warranty'] = $row2['warranty_name'];
                            }
                            if ($row2['unit_length']) {
                                $data['unit_length'] = $row2['unit_length'] . $row2['unit_length_unit'];
                            }
                            if ($row2['length']) {
                                $data['length'] = $row2['length'] . $row2['length_unit'];
                            }
                            if ($row2['width']) {
                                $data['width'] = $row2['width'] . $row2['width_unit'];
                            }
                            if ($row2['height']) {
                                $data['height'] = $row2['height'] . $row2['height_unit'];
                            }
                            if ($row2['thickness']) {
                                $data['thickness'] = $row2['thickness'] . $row2['thickness_unit'];
                            }
                            if ($row2['weight']) {
                                $data['weight'] = $row2['weight'] . $row2['weight_unit'];
                            }
                            if ($row2['diameter']) {
                                $data['diameter'] = $row2['diameter'] . $row2['diameter_unit'];
                            }

                            foreach ($data as $key => $value) {
                                $order_items = mysqli_query($this->mysqli, "INSERT INTO order_item_data (order_item_id, field, `value`)
                                                                               VALUES('$order_item_id', '$key', '$value')");
                            }
                        }
                    } else {
                        $success = array('status' => "Failed", 'msg' => "Failed to Order", 'error_type' => 0);
                        $this->response($this->json($success), 200);
                    }
                } else {
                    $success = array('status' => "Failed", 'msg' => "Cart Empty", 'error_type' => 0);
                    $this->response($this->json($success), 200);
                }

                if ($order_type == 'online') {
                    $fields = array();
                    $amount_val = round($total_amount * 100);
                    $fields["amount"] = $amount_val;
                    $fields["currency"] = "INR";
                    $url = 'https://api.razorpay.com/v1/orders';
                    $key_id = self::RAZOR_KEY_ID;
                    $key_secret = self::RAZOR_KEY_SECRET;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $key_id . ":" . $key_secret);
                    $headers = array();
                    $headers[] = 'Accept: application/json';
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $data = curl_exec($ch);

                    if (empty($data) OR (curl_getinfo($ch, CURLINFO_HTTP_CODE != 200))) {
                        curl_close($ch);
                        $this->send_mail_variables('order_failed', $order_id);

                        $success = array('status' => "Failed", 'msg' => "Failed", 'error_type' => 0);
                        $this->response($this->json($success), 200);
                    } else {
                        $data = json_decode($data, TRUE);
                        curl_close($ch);
                        if (isset($data['id'])) {
                            $payment_order_id = $data['id'];
                            $success = array('status' => "Success", 'msg' => "Success", 'payment_order_id' => $payment_order_id, 'amount' => $amount_val, 'order_id' => $order_id, 'order_uid' => $order_uid, 'key' => $key_id);
                            $this->response($this->json($success), 200);
                        } else {
                            $this->send_mail_variables('order_failed', $order_id);

                            $error = isset($data['error']['description']) ? $data['error']['description'] : 'Failed';
                            $success = array('status' => "Failed", 'msg' => $error, 'error_type' => 0);
                            $this->response($this->json($success), 200);
                        }
                    }
                } else {
                    $orders_cash_rlt = mysqli_query($this->mysqli, "UPDATE orders
                                                                  SET status = 'order_success'
                                                                  WHERE id = '$order_id'");

                    if ($orders_cash_rlt) {
                        $query = "SELECT SUM(cart.count) AS count,
                                         vendor_stock.id AS vendor_stock_id,
                                         vendor_stock.stock
                                  FROM cart
                                  INNER JOIN vendor_stock
                                  ON vendor_stock.product_size_id = cart.product_size_id
                                  AND vendor_stock.vendor_id = cart.vendor_id
                                  AND (vendor_stock.color_id = cart.color_id
                                  OR cart.color_id IS NULL)
                                  WHERE cart.user_id = '$user_id'
                                  AND cart.vendor_id = '$vendor_id'
                                  GROUP BY vendor_stock.id";

                        $query_rlt = mysqli_query($this->mysqli, $query);
                        while ($row = mysqli_fetch_array($query_rlt)) {
                            $vendor_stock_id = $row['vendor_stock_id'];
                            $stock = $row['stock'];
                            $count = $row['count'];
                            if ($stock != 'unlimited') {
                                $stock_count = $stock - $count;
                                $vendor_stock_rlt = mysqli_query($this->mysqli, "UPDATE vendor_stock
                                                                                    SET stock = '$stock_count'
                                                                                    WHERE id = '$vendor_stock_id'");
                            }
                        }

                        $delete_cart = mysqli_query($this->mysqli, "DELETE FROM cart
                                                                           WHERE user_id = '$user_id'
                                                                           AND vendor_id = '$vendor_id'");

                        $this->send_mail_variables('order_confirmed', $order_id);

                        $success = array('status' => "Success", 'msg' => "Order Placed Successfully");
                        $this->response($this->json($success), 200);
                    } else {
                        $this->send_mail_variables('order_failed', $order_id);

                        $success = array('status' => "Failed", 'msg' => "Failed to Order", 'error_type' => 0);
                        $this->response($this->json($success), 200);
                    }
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available", 'error_type' => 0);
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found", 'error_type' => 0);
            $this->response($this->json($success), 200);
        }
    }

    public function place_order()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $order_id = isset($this->_request['order_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['order_id']) : null;
        $payment_order_id = isset($this->_request['payment_order_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['payment_order_id']) : null;
        $payment_id = isset($this->_request['payment_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['payment_id']) : null;
        $signature = isset($this->_request['signature']) ? mysqli_real_escape_string($this->mysqli, $this->_request['signature']) : null;
        $total_amt = isset($this->_request['total_amt']) ? mysqli_real_escape_string($this->mysqli, $this->_request['total_amt']) : null;

        $user_id = $this->db_user->isValidUserId($uid);

        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $expectedSignature = hash_hmac('sha256', $payment_order_id . '|' . $payment_id, self::RAZOR_KEY_SECRET);

                if ($expectedSignature == $signature) {
                    $fields = array();
                    $fields["amount"] = $total_amt;
                    $fields["settle_full_balance"] = false;
                    $fields["description"] = 'OrderId: ' . $payment_order_id;
                    $url = 'https://api.razorpay.com/v1/settlements/ondemand';
                    $key_id = self::RAZOR_KEY_ID;
                    $key_secret = self::RAZOR_KEY_SECRET;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $key_id . ":" . $key_secret);
                    $headers = array();
                    $headers[] = 'Accept: application/json';
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $data = curl_exec($ch);

                    $settlement_id = 'null';
                    $settlement_fee = 'null';
                    $settlement_tax = 'null';
                    $settlement_amt_pending = 'null';
                    if (empty($data) OR (curl_getinfo($ch, CURLINFO_HTTP_CODE != 200))) {
                        curl_close($ch);
                    } else {
                        $data = json_decode($data, TRUE);
                        curl_close($ch);
                        if (isset($data['id'])) {
                            $settlement_id = "'" . $data['id'] . "'";
                            $settlement_fee = "'" . $data['fees'] / 100 . "'";
                            $settlement_tax = "'" . $data['tax'] / 100 . "'";
                            $settlement_amt_pending = "'" . $data['amount_pending'] / 100 . "'";
                        }
                    }

                    $orders = mysqli_query($this->mysqli, "UPDATE orders
                                                                  SET order_id = '$payment_order_id',
                                                                      payment_id = '$payment_id',
                                                                      signature = '$signature',
                                                                      settlement_id = $settlement_id,
                                                                      settlement_fee = $settlement_fee,
                                                                      settlement_tax = $settlement_tax,
                                                                      settlement_amt_pending = $settlement_amt_pending,
                                                                      status = 'payment_success'
                                                                  WHERE id = '$order_id'");
                    if ($orders) {
                        $query = "SELECT SUM(cart.count) AS count,
                                         vendor_stock.id AS vendor_stock_id,
                                         vendor_stock.stock
                                  FROM cart
                                  INNER JOIN vendor_stock
                                  ON vendor_stock.product_size_id = cart.product_size_id
                                  AND vendor_stock.vendor_id = cart.vendor_id
                                  AND (vendor_stock.color_id = cart.color_id
                                  OR cart.color_id IS NULL)
                                  WHERE cart.user_id = '$user_id'
                                  AND cart.vendor_id = '$vendor_id'
                                  GROUP BY vendor_stock.id";

                        $query_rlt = mysqli_query($this->mysqli, $query);
                        while ($row = mysqli_fetch_array($query_rlt)) {
                            $vendor_stock_id = $row['vendor_stock_id'];
                            $stock = $row['stock'];
                            $count = $row['count'];
                            if ($stock != 'unlimited') {
                                $stock_count = $stock - $count;
                                $vendor_stock_rlt = mysqli_query($this->mysqli, "UPDATE vendor_stock
                                                                                    SET stock = '$stock_count'
                                                                                    WHERE id = '$vendor_stock_id'");
                            }
                        }

                        $delete_cart = mysqli_query($this->mysqli, "DELETE FROM cart
                                                                           WHERE user_id = '$user_id'
                                                                           AND vendor_id = '$vendor_id'");

                        $this->send_mail_variables('order_confirmed', $order_id);

                        $success = array('status' => "Success", 'msg' => "Order Placed Successfully");
                        $this->response($this->json($success), 200);
                    } else {
                        $this->send_mail_variables('order_failed', $order_id);

                        $success = array('status' => "Failed", 'msg' => "Failed to Order");
                        $this->response($this->json($success), 200);
                    }
                } else {
                    $this->send_mail_variables('order_failed', $order_id);

                    $success = array('status' => "Failed", 'msg' => "Payment verification failed");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function failed_order()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $vendor_uid = isset($this->_request['vendor_uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['vendor_uid']) : null;
        $order_id = isset($this->_request['order_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['order_id']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $vendor_id = $this->db_user->isValidVendorId($vendor_uid);
            if ($vendor_id) {
                $orders = mysqli_query($this->mysqli, "UPDATE orders
                                                              SET status = 'payment_failed'
                                                              WHERE id = '$order_id'");
                $this->send_mail_variables('order_failed', $order_id);
                $success = array('status' => "Success", 'msg' => "Failed to Order");
                $this->response($this->json($success), 200);
            } else {
                $success = array('status' => "Failed", 'msg' => "Vendor is not available");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    public function send_mail_variables($type, $order_id)
    {
        $email_type = mysqli_query($this->mysqli, "SELECT * FROM email_template WHERE `type` = '$type' AND status = 'active'");

        $variables = array();
        $email = '';
        if (mysqli_num_rows($email_type)) {
            $row = mysqli_fetch_array($email_type);
            if ($type == 'order_confirmed' || $type == 'order_failed') {
                $col_name = 'order';

                $query_rlt2 = mysqli_query($this->mysqli, "SELECT 
                                                                 GROUP_CONCAT(order_item_data.field,':',order_item_data.value) AS product_detail
                                                                 FROM orders
                                                                 LEFT JOIN order_item
                                                                 ON order_item.order_id = orders.id
                                                                 LEFT JOIN order_item_data
                                                                 ON order_item_data.order_item_id = order_item.id
                                                                 WHERE orders.id = '$order_id'
                                                                 AND order_item_data.field IN ('name','size','color')
                                                                 GROUP BY order_item.id");
                $prdt_detail_array_value = array();
                if (mysqli_num_rows($query_rlt2)) {
                    while ($prdt_row = mysqli_fetch_array($query_rlt2)) {
                        $prdt_detail = $prdt_row['product_detail'];
                        $prdt_detail1 = $row['product_detail'];
                        $prdt1 = explode(',', $prdt_detail);
                        foreach ($prdt1 AS $prdt_val) {
                            $prdt2 = explode(':', $prdt_val);
                            $field = "'" . $col_name . "_" . $prdt2[0] . "'";
                            $field = strtoupper($field);
                            $prdt_key = trim($field, '\'"');
                            $detail_value = str_replace('{' . $prdt_key . '}', $prdt2[1], $prdt_detail1);
                            $prdt_detail1 = $detail_value;
                        }
                        array_push($prdt_detail_array_value, $prdt_detail1);
                    }
                }

                $prdt_detail_data = implode("<br/> ", $prdt_detail_array_value);
                $prdt_detail_value = str_replace('{ORDER_COLOR}', 'NIL', $prdt_detail_data);

                $query_rlt = mysqli_query($this->mysqli, "SELECT orders.*,
                                                                       `user`.email,
                                                                       order_address.full_name,
                                                                       order_address.phone_number,
                                                                       order_address.city_district,
                                                                       order_address.zip,
                                                                       order_address.address
                                                                 FROM orders
                                                                 INNER JOIN `user` 
                                                                 ON `user`.id = orders.user_id
                                                                 INNER JOIN order_address
                                                                 ON order_address.order_id = orders.id
                                                                 WHERE orders.id = '$order_id'");
                if (mysqli_num_rows($query_rlt)) {
                    $row1 = mysqli_fetch_array($query_rlt);
                    $email = $row1['email'];
                    foreach ($query_rlt as $key1 => $value1) {
                        foreach ($value1 as $key => $value) {
                            $field = "'" . $col_name . "_" . $key . "'";
                            $field = strtoupper($field);
                            $variables[$field] = $value;
                        }
                    }
                    $variables['ORDER_PRODUCT_DETAIL'] = $prdt_detail_value;
                }

            }
            $subject = $row['subject'];
            $template = $row['template'];
            foreach ($variables as $key => $value) {
                $key = trim($key, '\'"');
                $template = str_replace('{' . $key . '}', $value, $template);
            }

            $this->send_mail($email, $subject, $template);
        }
    }

    function send_mail($email, $subject, $template)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;  // Enable verbose debug output
            $mail->isSMTP();     // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;   // Enable SMTP authentication
            $mail->Username = self::EMAIL;     // SMTP username
            $mail->Password = self::EMAIL_PASSWORD;  // SMTP password
            $mail->SMTPSecure = 'tls';  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587;   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            // From email address and name
            $mail->setFrom(self::EMAIL, 'Naiz');

            // To email addresss
            $mail->addAddress($email);   // Add a recipient
            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $template;
            $mail->send();
        } catch (Exception $e) {
        }
    }

    public function forgot_password()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $email = isset($this->_request['email']) ? mysqli_real_escape_string($this->mysqli, $this->_request['email']) : null;
        $user_email = mysqli_query($this->mysqli, "SELECT id FROM `user` WHERE email = '$email'");
        if (mysqli_num_rows($user_email)) {
            $row = mysqli_fetch_array($user_email);
            $user_id = $row['id'];
            $token = $this->create_random_string(16);
            $update_user = mysqli_query($this->mysqli, "UPDATE `user` SET token = '$token' WHERE id = '$user_id'");
            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->SMTPDebug = 0;  // Enable verbose debug output
                $mail->isSMTP();     // Send using SMTP
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;   // Enable SMTP authentication
                $mail->Username = self::EMAIL;     // SMTP username
                $mail->Password = self::EMAIL_PASSWORD;  // SMTP password
                $mail->SMTPSecure = 'tls';  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port = 587;   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                // From email address and name
                $mail->setFrom(self::EMAIL, 'Naiz');

                // To email addresss
                $mail->addAddress($email);   // Add a recipient
                // Content
                $mail->isHTML(true);  // Set email format to HTML
                $mail->Subject = 'Reset Password';
                $forgot_pwd_url = self::WEB_URL . 'reset_password?token=' . $token;
                $mail->Body = "<a href='$forgot_pwd_url'>Click here</a> to reset your password for Naiz";

                $mail->send();
                $success = array('status' => "Success", 'msg' => 'Mail has been sent');
                $this->response($this->json($success), 200);

            } catch (Exception $e) {
                $success = array('status' => "Failed", 'msg' => $e->getMessage());
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "Mail has been sent");
            $this->response($this->json($success), 200);
        }
    }

    public function update_user_address()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $uid = isset($this->_request['uid']) ? mysqli_real_escape_string($this->mysqli, $this->_request['uid']) : null;
        $address_id = isset($this->_request['address_id']) ? mysqli_real_escape_string($this->mysqli, $this->_request['address_id']) : null;
        $full_name = isset($this->_request['full_name']) ? mysqli_real_escape_string($this->mysqli, $this->_request['full_name']) : null;
        $phone_number = isset($this->_request['phone_number']) ? mysqli_real_escape_string($this->mysqli, $this->_request['phone_number']) : null;
        $city_district = isset($this->_request['city_district']) ? mysqli_real_escape_string($this->mysqli, $this->_request['city_district']) : null;
        $zip = isset($this->_request['zip']) ? mysqli_real_escape_string($this->mysqli, $this->_request['zip']) : null;
        $address = isset($this->_request['address']) ? mysqli_real_escape_string($this->mysqli, $this->_request['address']) : null;
        $type = isset($this->_request['type']) ? mysqli_real_escape_string($this->mysqli, $this->_request['type']) : null;

        $user_id = $this->db_user->isValidUserId($uid);
        if ($user_id) {
            $user_address_id = $this->db_user->isValidUserAddressId($user_id, $address_id);
            if ($user_address_id) {
                $user_address_rlt = mysqli_query($this->mysqli, "UPDATE user_address 
                                                                        SET full_name = '$full_name',
                                                                            phone_number = '$phone_number',
                                                                            city_district = '$city_district',
                                                                            zip = '$zip',
                                                                            address = '$address',
                                                                            `type` = '$type'
                                                                        WHERE user_id = '$user_id'
                                                                        AND id = '$address_id'");
                if ($user_address_rlt) {
                    $success = array('status' => "Success", 'msg' => "Address Updated Successfully");
                    $this->response($this->json($success), 200);
                } else {
                    $success = array('status' => "Failed", 'msg' => "Failed to Update Address");
                    $this->response($this->json($success), 200);
                }
            } else {
                $success = array('status' => "Failed", 'msg' => "Unable to edit address");
                $this->response($this->json($success), 200);
            }
        } else {
            $success = array('status' => "Failed", 'msg' => "User not found");
            $this->response($this->json($success), 200);
        }
    }

    //TODO    private functions starts
    private function json($data)
    {
        if (is_array($data)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    function create_random_string($length)
    {
        $characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function create_alpha_numeric_string($length)
    {
        $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// Initiate Library
$api = new API;
$api->processApi();