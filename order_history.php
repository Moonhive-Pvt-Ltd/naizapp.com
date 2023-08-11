<?php include_once 'includes/functions.php';
if (login_check($mysqli) == true) {
    unset($_COOKIE['cart_id_array']);
    setcookie('cart_id_array', null, -1, '/');
    $order_s = isset($_GET['s']) ? $_GET['s'] : '';
    ?>
    <!DOCTYPE html>
    <html lang="zxx">
    <?php include_once 'includes/header.php'; ?>
    <body>
    <div class="main-wrapper main-wrapper-2">
        <?php include_once 'includes/navbar.php'; ?>
        <!-- mini cart start -->
        <?php
        $title = 'My Orders ';
        include_once 'includes/templates/header_content.php'; ?>
        <?php
        if ($order_s == 1) { ?>
            <div class="pt-80">
                <div class="text-center">
                    <img src="assets/images/tick.jpg" alt="" height="70">
                    <div class="pt-15">
                        <h4 class="text-green">Order Placed Successfully</h4>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="<?php echo $order_s == 1 ? 'pb-15 pt-80' : 'pb-100 pt-100' ?>">
            <div class="container">
                <?php include_once 'includes/templates/order_history_content.php'; ?>
            </div>
        </div>
        <div class="pt-2">
            <?php if ($order_s == 1) { ?>
                <div class="text-center">
                    <div class="cart-shiping-update-wrapper align-items-center justify-content-center"
                         style="width: 100%;">
                        <div class="cart-shiping-update btn-hover">
                            <a href="products">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
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