<?php
require_once("../functions.php");

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

$rlt = mysqli_query($mysqli, "SELECT COUNT(id) AS cart_count
                                     FROM cart WHERE user_id = '$user_id'
                                     AND vendor_id = '$vendor_id'");
$cart_count = 0;
if (mysqli_num_rows($rlt)) {
    $row = mysqli_fetch_array($rlt);
    $cart_count = $row['cart_count'];
} ?>

<div class="header-action-style header-action-cart">
    <a title="Cart" href="cart">
        <i class="pe-7s-shopbag">
            <span class="product-count bg-black"><?php echo $cart_count; ?></span>
        </i>
    </a>
</div>
