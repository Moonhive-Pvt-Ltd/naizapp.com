<?php
include_once 'connect.php';
require __DIR__ . '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

const EmailId = 'naiztrading2021@gmail.com';
const EmailPswd = 'xktiublkkpriftmm';

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'setWebCookie':
            setWebCookie($_POST);
            break;
        case 'updateUserPassword':
            updateUserPassword($_POST, $mysqli);
            break;
        case 'getCartCount':
            getCartCount($_POST['user_id'], $_POST['vendor_id'], $_POST['product_size_id'], $_POST['color_id'], $_POST['warranty_id'], $_POST['design_id'], $_POST['type'], $mysqli);
            break;
        case 'getCartSubTotalCount':
            getCartSubTotalCount($_POST, $mysqli);
            break;
        case 'sendContactUs':
            sendContactUs($_POST, $mysqli);
            break;
    }
}

function login($email, $password, $mysqli)
{
    // Using prepared statements means that SQL injection is not possible.
    if ($stmt = $mysqli->prepare("SELECT uid, password, salt
FROM user
WHERE status = 'active'
AND email = ?
LIMIT 1")) {
        $stmt->bind_param('s', $email); // Bind "$email" to parameter.
        $stmt->execute(); // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($user_uid, $db_password, $salt);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            $password = hash('sha512', $password . $salt);
            // Check if the password in the database matches
            // the password the user submitted.
            if ($password == $db_password) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];
                // XSS protection as we might print this value
                $login_string = hash('sha512', $db_password . $user_browser);
                // Login successful.

                //storing data in cookie since session is not handled properly
                setcookie("naiz_web_user_uid", $user_uid, time() + 60 * 60 * 24 * 365, "/");
                setcookie("naiz_web_login_string", $login_string, time() + 60 * 60 * 24 * 365, "/");
                return true;
            } else {
                // Password is not correct
                return false;
            }
        } else {
            //No user exists
            return false;
        }
    }
}

function login_check($mysqli)
{
    // Check if all cookie variables are set
    if (isset($_COOKIE['naiz_web_email']) && isset($_COOKIE['naiz_web_password'])) {
        if (login($_COOKIE['naiz_web_email'], $_COOKIE['naiz_web_password'], $mysqli) == true) {
            // Login success using cookie data
            return true;
        } else {
            // Login failed
            header('location: logout');
        }
    } else {
        // Not logged in
        return false;
    }
}

function setWebCookie($post)
{
    setcookie($post['cookie'], $post['value'], time() + 60 * 60 * 24 * 365, "/");
}

function getApiData($url, $post)
{
    $curl = curl_init($url); //initialising our url
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    return json_decode($curl_response, true);
}

function getUserData($mysqli, $user_uid)
{
    $result = mysqli_query($mysqli, "SELECT * FROM `user` WHERE uid = '$user_uid' AND status = 'active'");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row;
        return $user_id;
    }
}

function getVendorData($mysqli, $vendor_uid)
{
    $result = mysqli_query($mysqli, "SELECT id FROM vendor WHERE uid = '$vendor_uid' AND status = 'active'");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        $row = $result->fetch_assoc();
        $vendor_id = $row;
        return $vendor_id;
    }
}


function updateUserPassword($post, $mysqli)
{
    $user_id = $post['user_id'];
    $pswd = mysqli_real_escape_string($mysqli, $post['password']);
    $confirm_pswd = mysqli_real_escape_string($mysqli, $post['confirm_pswd']);
    if ($pswd === $confirm_pswd) {
        $ps = hash('sha512', $confirm_pswd);
        $salt = hash('sha512', $ps);
        $password = hash('sha512', $ps . $salt);
        $rlt = mysqli_query($mysqli, "UPDATE `user` SET password = '$password', salt = '$salt', token = null WHERE id = '$user_id'");
        echo $rlt ? $rlt : $mysqli->error;
    } else {
        echo "Password not match";
        return;
    }
}

function getCartCount($user_id, $vendor_id, $product_size_id, $color_id, $warranty_id, $design_id, $type, $mysqli)
{
    $cart_color_id = isset($color_id) && mysqli_real_escape_string($mysqli, $color_id) != '' ? "'" . mysqli_real_escape_string($mysqli, $color_id) . "'" : 'null';
    $cart_warranty_id = isset($warranty_id) && mysqli_real_escape_string($mysqli, $warranty_id) != '' ? "'" . mysqli_real_escape_string($mysqli, $warranty_id) . "'" : 'null';
    $cart_design_id = isset($design_id) && mysqli_real_escape_string($mysqli, $design_id) != '' ? "'" . mysqli_real_escape_string($mysqli, $design_id) . "'" : 'null';

    $cart_rlt_query = "SELECT * FROM cart 
                       WHERE product_size_id = '$product_size_id'
                       AND vendor_id = '$vendor_id'
                       AND user_id = '$user_id'";

    if ($cart_color_id == 'null') {
        $cart_rlt_query .= " AND color_id IS NULL";
    } else {
        $cart_rlt_query .= " AND color_id = $cart_color_id";
    }

    if ($cart_design_id == 'null') {
        $cart_rlt_query .= " AND product_design_id IS NULL";
    } else {
        $cart_rlt_query .= " AND product_design_id = $cart_design_id";
    }

    if ($cart_warranty_id == 'null') {
        $cart_rlt_query .= " AND warranty_id IS NULL";
    } else {
        $cart_rlt_query .= " AND warranty_id = $cart_warranty_id";
    }
    $cart_rlt = mysqli_query($mysqli, $cart_rlt_query);


    if (mysqli_num_rows($cart_rlt)) {
        $cart_row = mysqli_fetch_array($cart_rlt);
        $count = $cart_row['count'] ? $cart_row['count'] : 0;
        if ($type) {
            echo $count;
            return;
        } else {
            return $count;
        }
    } else {
        if ($type) {
            echo 0;
            return;
        } else {
            return 0;
        }
    }
}


function getCartSubTotalCount($post, $mysqli)
{
    $user_id = mysqli_real_escape_string($mysqli, $post['user_id']);
    $vendor_id = mysqli_real_escape_string($mysqli, $post['vendor_id']);
    $product_size_id = mysqli_real_escape_string($mysqli, $post['product_size_id']);
    $cart_color_id = isset($post['color_id']) && mysqli_real_escape_string($mysqli, $post['color_id']) != '' ? "'" . mysqli_real_escape_string($mysqli, $post['color_id']) . "'" : 'null';

    $cart_rlt_query = "SELECT SUM(cart.count) AS `count` FROM cart 
                       WHERE product_size_id = '$product_size_id'
                       AND vendor_id = '$vendor_id'
                       AND user_id = '$user_id'";

    if ($cart_color_id == 'null') {
        $cart_rlt_query .= " AND color_id IS NULL";
    } else {
        $cart_rlt_query .= " AND color_id = $cart_color_id";
    }

    $cart_rlt = mysqli_query($mysqli, $cart_rlt_query);

    if (mysqli_num_rows($cart_rlt)) {
        $cart_row = mysqli_fetch_array($cart_rlt);
        $count = $cart_row['count'] ? $cart_row['count'] : 0;
        echo $count;
        return;
    } else {
        echo 0;
        return;
    }
}

function sendContactUs($post, $mysqli)
{
    $name = mysqli_real_escape_string($mysqli, $post['name']);
    $email = mysqli_real_escape_string($mysqli, $post['email']);
    $subject = mysqli_real_escape_string($mysqli, $post['subject']);
    $phone = mysqli_real_escape_string($mysqli, $post['phone']);
    $message = mysqli_real_escape_string($mysqli, $post['message']);

    if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
        echo "Please enter a valid Email";
        return;
    }

    if (!preg_match('/^[0-9]{10}+$/', $phone)) {
        echo "Please enter a valid mobile";
        return;
    }

    $rlt = mysqli_query($mysqli, "INSERT INTO contact_us (`name`, email, mobile, subject, message)
                                                 VALUES('$name', '$email', '$phone', '$subject', '$message')");

    if ($rlt) {
        sendMail($email, $subject, $message);
        echo 1;
        return;
    } else {
        echo $mysqli->error;
    }
}

function sendMail($email, $subject, $message)
{
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 0;  // Enable verbose debug output
        $mail->isSMTP();     // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;   // Enable SMTP authentication
        $mail->Username = EmailId;     // SMTP username
        $mail->Password = EmailPswd;  // SMTP password
        $mail->SMTPSecure = 'tls';  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port = 587;   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        // From email address and name
        $mail->setFrom(EmailId, 'Naiz');

        // To email addresss
        $mail->addAddress($email);   // Add a recipient
        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
    }
}
