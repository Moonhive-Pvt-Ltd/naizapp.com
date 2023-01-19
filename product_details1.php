<?php include_once 'includes/functions.php'; ?>
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
        $title = 'Product Details';
        include_once 'includes/templates/header_content.php';
        include_once 'includes/templates/product_detail_content1.php';
        include_once 'includes/footer_bottom.php';
        include_once 'includes/sidebar.php';
    } ?>
</div>
<!-- All JS is here -->
<?php include_once 'includes/footer.php'; ?>
</body>

</html>
