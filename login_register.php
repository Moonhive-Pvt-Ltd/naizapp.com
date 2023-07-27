<?php include_once 'includes/functions.php';
if (login_check($mysqli) == false) { ?>
    <!DOCTYPE html>
    <html lang="zxx">
    <?php include_once 'includes/header.php'; ?>
    <body>
    <div class="main-wrapper main-wrapper-2">
        <?php include_once 'includes/navbar.php'; ?>
        <!-- mini cart start -->
        <?php
        $title = 'Login - Register ';
        include_once 'includes/templates/header_content.php'; ?>
        <div class="login-register-area pb-100 pt-95">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-12 offset-lg-2">
                        <div class="login-register-wrapper">
                            <div class="login-register-tab-list nav">
                                <a class="active" data-bs-toggle="tab" href="#lg1">
                                    <h4> login </h4>
                                </a>
                                <a data-bs-toggle="tab" href="#lg2">
                                    <h4> register </h4>
                                </a>
                            </div>
                            <div class="tab-content">
                                <div id="lg1" class="tab-pane active">
                                    <div class="login-form-container">
                                        <div class="login-register-form">
                                            <form id="userLoginForm" method="post" type-id="account_login">
                                                <input type="text" name="email" id="email" placeholder="Email" required>
                                                <input type="password" name="password" id="password"
                                                       placeholder="Password" required>
                                                <div class="login-toggle-btn padding-0px">
                                                    <a href="forgot_password">Forgot Password?</a>
                                                </div>
                                                <div class="button-box btn-hover margin-bottom-4px">
                                                    <button type="submit" class="login-form-btn">Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="lg2" class="tab-pane">
                                    <div class="login-form-container">
                                        <div class="login-register-form">
                                            <form id="userRegisterForm" method="post">
                                                <input type="text" name="full_name" placeholder="Full name" required>
                                                <div class="reg-pswd-eye-div">
                                                    <input type="password" name="password" class="password-type"
                                                           placeholder="Password" required>
                                                    <div class="pswd-eye-icon-div on-reg-password-eye-click cursor-pointer">
                                                        <i class="fa fa-eye-slash eye-icon"></i>
                                                    </div>
                                                </div>
                                                <input name="email" placeholder="Email" type="email" required>
                                                <input name="mobile" placeholder="Mobile" type="text" required>
                                                <div class="button-box btn-hover">
                                                    <button type="submit" class="register-form-btn">Register</button>
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

        <?php include_once 'includes/footer_bottom.php'; ?>
        <?php include_once 'includes/sidebar.php'; ?>
    </div>
    <?php include_once 'includes/footer.php'; ?>
    </body>
    </html>
<?php } else {
    echo("<script>location.href = './account';</script>");
} ?>