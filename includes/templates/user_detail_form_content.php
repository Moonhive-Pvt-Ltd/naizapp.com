<?php
require_once("../functions.php");

$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';

$full_name = '';
$mobile = '';
$email = '';

$user = mysqli_query($mysqli, "SELECT *
                                                    FROM `user`
                                                    WHERE uid = '$user_uid'
                                                    AND status = 'active'");
if (mysqli_num_rows($user)) {
    $row = $user->fetch_assoc();
    $full_name = $row['full_name'];
    $mobile = $row['mobile'];
    $email = $row['email'];
}
?>

<div class="account-details-form">
    <form id="updateUserDetailForm" method="post">
        <div class="row">
            <div class="single-input-item">
                <label for="full-name" class="required">Full
                    Name</label>
                <input type="text" id="full-name" name="full_name" value="<?php echo $full_name; ?>" required/>
            </div>
            <div class="single-input-item">
                <label for="email" class="required">Email Addres</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required/>
            </div>
            <div class="single-input-item">
                <label for="mobile" class="required">Mobile</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo $mobile; ?>" required/>
            </div>
            <div class="single-input-item btn-hover">
                <input type="hidden" name="uid" value="<?php echo $user_uid; ?>"/>
                <button type="submit" class="check-btn sqr-btn">Save Changes</button>
            </div>
    </form>
</div>

<form id="updateUserPassword" method="post" class="mt-4">
    <div class="row">
        <div class="single-input-item">
            <label for="current-pwd" class="required">Current
                Password</label>
            <input type="password" id="current-pwd" name="current_password" required/>
        </div>
        <div class="col-lg-6">
            <div class="single-input-item">
                <label for="new-pwd" class="required">New
                    Password</label>
                <input type="password" id="new-pwd" name="new_password" required/>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="single-input-item">
                <label for="confirm-pwd" class="required">Confirm
                    Password</label>
                <input type="password" id="confirm-pwd" name="confirm_password" required/>
            </div>
        </div>
        <div class="single-input-item btn-hover">
            <input type="hidden" name="uid" value="<?php echo $user_uid; ?>"/>
            <button type="submit" class="check-btn sqr-btn">Save Changes</button>
        </div>
</form>
