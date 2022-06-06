<?php include_once 'includes/functions.php';
if (login_check($mysqli) == true) { ?>
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
        <div class="pb-100 pt-100">
            <div class="container">
                <?php include_once 'includes/templates/order_history_content.php'; ?>
            </div>
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