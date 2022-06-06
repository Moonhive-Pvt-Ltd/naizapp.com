<?php
require_once("../functions.php");
$post = [];
$url = BASE_URL . "get_vendor_list";
$result = getApiData($url, $post);
$vendor_list = [];
if ($result['status'] == 'Success') {
    $vendor_list = $result['store_list'];
}
?>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Select Store</h5>
    </div>
    <div id="storeListModal">
        <div class="modal-scroll">
            <?php foreach ($vendor_list as $vendor) { ?>
                <div class="col-12 content">
                    <div class="content-data">
                        <h6><?php echo $vendor['place'] ?></h6>
                        <ul>
                            <li><?php echo $vendor['address'] ?></li>
                            <li><?php echo $vendor['phone_number'] ?></li>
                            <li><?php echo $vendor['email'] ?></li>
                        </ul>
                    </div>
                    <div>
                        <i class="fa fa-check-square-o checked-box"
                           vendor-uid="<?php echo $vendor['vendor_uid']; ?>"></i>
                        <i class="fa fa-square-o unchecked-box active"></i>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="model-btns">
            <button type="submit" class="modal-submit">Submit</button>
        </div>
    </div>
</div>
