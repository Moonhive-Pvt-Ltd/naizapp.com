<?php
require_once("../functions.php");
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$address_id = isset($_GET['address_id']) ? $_GET['address_id'] : '';
$limit = 6;

$post = [
  'uid' => $user_uid,
  'vendor_uid' => $vendor_uid,
  'page' => $pagenum,
  'limit' => $limit,
];

$total_pages = 0;

$url = BASE_URL . "get_user_address";
$result = getApiData($url, $post);
if ($result['status'] == 'Success') {
    $total_pages = $result['total_pages'];
}
?>
    <div class="row col-md-12 mt-4">
        <?php foreach ($result['user_address_data'] as $row2) { ?>
            <div class="col-md-12 mt-2 cursor-pointer select-address-div select-user-address"
                 shippingFee="<?php echo $row2['shipping_fee']; ?>"
                 addressId="<?php echo $row2['id']; ?>">
                <div style="width: 100%">
                    <div><strong><?php echo $row2['full_name'] ?></strong>
                        <span class="address-type-badge"><?php echo $row2['type'] ?></span>
                    </div>
                    <?php echo $row2['address']; ?>,
                    <?php echo $row2['city_district'] ?>, <?php echo $row2['pincode'] ?>
                    <p><?php echo $row2['phone_number']; ?></p>
                </div>

                <?php if ($address_id == $row2['id']) { ?>
                    <i class="fa fa-check-square-o checked-box" style="font-size: 22px"></i>
                    <i class="fa fa-square-o unchecked-box" style="font-size: 22px; display: none"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square-o checked-box" style="font-size: 22px; display: none"></i>
                    <i class="fa fa-square-o unchecked-box" style="font-size: 22px"></i>
                <?php } ?>

            </div>
            <span class="checkout-address-bottom"></span>
        <?php } ?>
    </div>
<?php if ($total_pages > 1) { ?>
    <footer class="pagination-footer  d-flex justify-content-end mt-3" type-id="userAddressCheckoutContent"
            scroll-id="userAddressCheckoutContent">
        <?php include 'pagination.php'; ?>
    </footer>
<?php } ?>