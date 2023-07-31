<?php include_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="zxx">
<?php include_once 'includes/header.php'; ?>
<body>
<div class="main-wrapper main-wrapper-2">
    <?php
    if (!isset($_COOKIE['landing_page'])) { ?>
        <div id="landingPage"></div>
    <?php } else if (isset($_COOKIE['landing_page']) && ($_COOKIE['landing_page'] === 'yes')) { ?>
        <div id="landingPage"></div>
    <?php } ?>
    <?php include_once 'includes/navbar.php'; ?>

    <?php
    $vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
    if ($vendor_uid != '') {
        include_once 'includes/templates/home_content.php'; ?>
        <?php include_once 'includes/footer_bottom.php'; ?>
        <?php include_once 'includes/sidebar.php';
    } ?>

</div>
<?php include_once 'includes/footer.php'; ?>
</body>

</html>
