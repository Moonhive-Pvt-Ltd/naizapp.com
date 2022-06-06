<?php include_once 'includes/functions.php';
if (isset($_GET['token']) && $_GET['token'] != '') {
    $token = $_GET['token'];
} else {
    $token = '';
}
$user = mysqli_query($mysqli, "SELECT id FROM `user` WHERE token = '$token'");
?>
<!DOCTYPE html>
<html lang="zxx">
<?php include_once 'includes/header.php'; ?>
<body>
<div class="main-wrapper main-wrapper-2">
    <!-- mini cart start -->
    <?php if (mysqli_num_rows($user)) {
        $row = mysqli_fetch_array($user);
        $user_id = $row['id']; ?>
        <div class="login-register-area pb-100 pt-95 reset-password-div">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-12 offset-lg-2">
                        <div class="login-register-wrapper">
                            <div class="login-register-tab-list nav">
                                <a class="active" data-bs-toggle="tab" href="#lg1">
                                    <h4> Reset Password </h4>
                                </a>
                            </div>
                            <div class="tab-content">
                                <div id="lg1" class="tab-pane active">
                                    <div class="login-form-container">
                                        <div class="login-register-form">
                                            <form id="resetPswd" method="post">
                                                <input type="text" id="password" placeholder="Password" required>
                                                <input type="password" id="confirmPassword"
                                                       placeholder="Confirm Password"
                                                       required>
                                                <div class="button-box btn-hover">
                                                    <input type="hidden" value="<?php echo $user_id; ?>" id="userId">
                                                    <button type="submit" class="reset-pswd">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="text-center pt-4 color-black">Invalid Link</div>
    <?php } ?>
</div>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>