<?php include_once 'includes/functions.php';
if (login_check($mysqli) == true) {
    unset($_COOKIE['cart_id_array']);
    setcookie('cart_id_array', null, -1, '/');
    ?>
    <!DOCTYPE html>
    <html lang="zxx">
    <?php include_once 'includes/header.php'; ?>
    <body>
    <div class="main-wrapper main-wrapper-2">
        <?php include_once 'includes/navbar.php'; ?>
        <!-- mini cart start -->
        <?php
        $vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
        if ($vendor_uid != '') {
            $title = 'Cart';
            include_once 'includes/templates/header_content.php'; ?>
            <?php include_once 'includes/templates/cart_content.php'; ?>
            <?php include_once 'includes/footer_bottom.php'; ?>
            <?php include_once 'includes/sidebar.php';
        } ?>
    </div>
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/sha512.js"></script>
    </body>
    </html>
<?php } else {
    echo("<script>location.href = './login_register';</script>");
} ?>