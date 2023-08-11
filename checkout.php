<?php include_once 'includes/functions.php';
if (login_check($mysqli) == true) {
    $user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
    $vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';

    $user_id = '';
    if ($user_uid) {
        $user_data = getUserData($mysqli, $user_uid);
        $user_id = $user_data['id'];
    }

    $vendor_id = '';
    if ($vendor_uid) {
        $vendor_data = getVendorData($mysqli, $vendor_uid);
        $vendor_id = $vendor_data['id'];
    }

    $cart_query = mysqli_query($mysqli, "SELECT id FROM cart WHERE user_id = '$user_id' AND vendor_id = '$vendor_id'");
    if (mysqli_num_rows($cart_query)) { ?>
        <script type="text/javascript">
            function preventBack() {
                window.history.forward();
            }

            setTimeout("preventBack()", 0);
            window.onunload = function () {
                null
            };
        </script>
        <!DOCTYPE html>
        <html lang="zxx">
        <?php include_once 'includes/header.php'; ?>
        <body>
        <div class="main-wrapper main-wrapper-2">
            <?php include_once 'includes/navbar.php'; ?>
            <!-- mini cart start -->
            <?php
            if ($vendor_uid != '') {
                $title = 'Checkout';
                include_once 'includes/templates/header_content.php'; ?>
                <?php include_once 'includes/templates/checkout_content.php'; ?>
                <?php include_once 'includes/footer_bottom.php'; ?>
                <?php include_once 'includes/sidebar.php';
            } ?>
        </div>
        <?php include_once 'includes/footer.php'; ?>
        </body>
        </html>
    <?php } else {
        echo("<script>location.href = './index';</script>");
    }
} else {
    echo("<script>location.href = './login_register';</script>");
} ?>