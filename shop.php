<!DOCTYPE html>
<html lang="zxx">
<?php include_once 'includes/header.php'; ?>
<body>
<div class="main-wrapper main-wrapper-2">
    <?php include_once 'includes/navbar.php'; ?>
    <!-- mini cart start -->
    <?php
    $title = 'Shop';
    include_once 'includes/templates/header_content.php'; ?>
    <div class="shop-location pt-95 pb-45">
        <div class="container">
            <div class="row" id="vendorListId">
            </div>
        </div>
    </div>
    <?php include_once 'includes/footer_bottom.php'; ?>
    <?php include_once 'includes/sidebar.php'; ?>
</div>
<!-- All JS is here -->
<?php include_once 'includes/footer.php'; ?>
</body>
</html>