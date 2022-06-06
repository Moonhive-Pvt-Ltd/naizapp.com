<?php
require_once("../functions.php");
$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$total_pages = 0;
$post = [
  'page' => $pagenum,
];
$url = BASE_URL . "get_vendor_list";
$result = getApiData($url, $post);
$vendor_list = [];
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';

if ($result['status'] == 'Success') {
    $vendor_list = $result['store_list'];
    $total_pages = $result['total_pages'];
}
?>

<?php foreach ($vendor_list as $vendor) { ?>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="single-store mb-50 store-<?php echo $vendor['vendor_uid']; ?> <?php echo $vendor_uid == $vendor['vendor_uid'] ? 'selected-store' : ''; ?>"
             vendor-uid="<?php echo $vendor['vendor_uid']; ?>" data-aos="fade-up" data-aos-delay="200">
            <div class="content">
                <h3><?php echo $vendor['place'] ?></h3>
                <ul>
                    <li><?php echo $vendor['address'] ?></li>
                    <li><?php echo $vendor['phone_number'] ?></li>
                    <li><?php echo $vendor['email'] ?></li>
                </ul>
            </div>
            <i class="fa fa-check <?php echo $vendor_uid == $vendor['vendor_uid'] ? '' : 'hidden'; ?>"
               style="color: green; "></i>
        </div>
    </div>
<?php }
if ($total_pages > 1) { ?>
    <footer class="pagination-footer" type-id="vendorListId" scroll-id="vendorListId">
        <?php include 'pagination.php'; ?>
    </footer>
<?php } ?>