<?php include_once 'includes/functions.php';
if (login_check($mysqli) == true) {
    $user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
    $full_name = 'User';
    if ($user_uid) {
        $user_data = getUserData($mysqli, $user_uid);
        $full_name = $user_data['full_name'];
    } ?>
    <!DOCTYPE html>
    <html lang="zxx">
    <?php include_once 'includes/header.php'; ?>
    <body>
    <div class="main-wrapper main-wrapper-2">
        <?php include_once 'includes/navbar.php'; ?>
        <!-- mini cart start -->
        <?php
        $title = 'My Account';
        include_once 'includes/templates/header_content.php'; ?>

        <!-- my account wrapper start -->
        <div class="my-account-wrapper pb-100 pt-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- My Account Page Start -->
                        <div class="myaccount-page-wrapper">
                            <!-- My Account Tab Menu Start -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <div class="myaccount-tab-menu nav" role="tablist">
                                        <a href="#dashboad" class="active dashboard-tab"
                                           data-bs-toggle="tab">Dashboard</a>
                                        <a href="#orders" class="order-tab" data-bs-toggle="tab">Orders</a>
                                        <a href="#address-edit" class="address-tab" data-bs-toggle="tab">Address</a>
                                        <a href="#account-info" class="account-tab" data-bs-toggle="tab">Account
                                            Details</a>
                                        <a href="logout" class="logout-tab">Logout</a>
                                    </div>
                                </div>
                                <!-- My Account Tab Menu End -->
                                <!-- My Account Tab Content Start -->
                                <div class="col-lg-9 col-md-8">
                                    <div class="tab-content" id="myaccountContent">
                                        <!-- Single Tab Content Start -->
                                        <div class="tab-pane fade show active" id="dashboad" role="tabpanel">
                                            <div class="myaccount-content">
                                                <h3>Dashboard</h3>
                                                <div class="welcome">
                                                    <p>Hello, <strong><?php echo $full_name; ?></strong> (If Not
                                                        <strong><?php echo $full_name; ?>
                                                            ! </strong><a href="logout">Logout</a>)</p>
                                                </div>
                                                <p class="mb-0">From your account dashboard. you can easily check & view
                                                    your recent orders, manage your shipping and billing addresses and
                                                    edit your password and account details.</p>
                                            </div>
                                        </div>
                                        <!-- Single Tab Content End -->
                                        <!-- Single Tab Content Start -->
                                        <div class="tab-pane fade" id="orders" role="tabpanel">
                                            <div class="myaccount-content">
                                                <h3>Orders</h3>
                                                <div class="tab-content"></div>
                                            </div>
                                        </div>
                                        <!-- Single Tab Content End -->
                                        <!-- Single Tab Content Start -->
                                        <div class="tab-pane fade" id="address-edit" role="tabpanel">
                                            <div class="myaccount-content">
                                                <h3>Billing Address</h3>
                                                <div class="tab-content"></div>
                                            </div>
                                        </div>
                                        <!-- Single Tab Content End -->
                                        <!-- Single Tab Content Start -->
                                        <div class="tab-pane fade" id="account-info" role="tabpanel">
                                            <div class="myaccount-content">
                                                <h3>Account Details</h3>
                                                <div class="tab-content"></div>
                                            </div>
                                        </div> <!-- Single Tab Content End -->
                                    </div>
                                </div> <!-- My Account Tab Content End -->
                            </div>
                        </div> <!-- My Account Page End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- my account wrapper end -->

        <?php include_once 'includes/footer_bottom.php'; ?>
        <?php include_once 'includes/sidebar.php'; ?>
    </div>
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/sha512.js"></script>
    </body>
    </html>
<?php } else {
    echo("<script>location.href = './login_register';</script>");
} ?>