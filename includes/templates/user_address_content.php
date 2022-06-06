<?php
require_once("../functions.php");
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 15;

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

<div class="row col-md-12">
    <?php foreach ($result['user_address_data'] as $row) { ?>
        <div class="col-md-4 mb-5">
            <address>
                <p><strong><?php echo $row['full_name'] ?></strong></p>
                <p><?php echo $row['address'] ?> <br>
                    <?php echo $row['pincode'] ?></p>
                <p>Mobile: <?php echo $row['phone_number']; ?></p>
            </address>
            <a href="" class="check-btn sqr-btn edit-user-address" address-id="<?php echo $row['id']; ?>"><i
                        class="fa fa-edit"></i> Edit
                Address</a>
        </div>
    <?php } ?>
    <?php if ($total_pages > 1) { ?>
        <footer class="pagination-footer  d-flex justify-content-end mt-3" type-id="userAddressContent"
                scroll-id="userAddressContent">
            <?php include 'pagination.php'; ?>
        </footer>
    <?php } ?>
</div>