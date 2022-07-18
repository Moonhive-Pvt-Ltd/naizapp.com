<?php
if (isset($_POST['prdt_size_color_id'])) {
    require_once("../functions.php");
    $user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
    $user_id = '';

    if ($user_uid) {
        $user_data = getUserData($mysqli, $user_uid);
        $user_id = $user_data['id'];
    }

    $vendor_id = $_POST['vendor_id'];
    $color_id = $_POST['color_id'];
    $product_size_id = $_POST['product_size_id'];
    $prdt_size_color_id = $_POST['prdt_size_color_id'];

    $product_size_color_warranty_rlt = mysqli_query($mysqli, "SELECT product_size_color_warranty.*,
                                                                            warranty.warranty,
                                                                            cart.count
                                                                     FROM product_size_color_warranty
                                                                     INNER JOIN warranty ON warranty.id = product_size_color_warranty.warranty_id
                                                                     LEFT JOIN cart ON cart.warranty_id = warranty.id 
                                                                     AND cart.color_id = '$color_id' 
                                                                     AND cart.user_id = '$user_id' 
                                                                     AND cart.vendor_id = '$vendor_id' 
                                                                     AND cart.product_size_id = '$product_size_id' 
                                                                     WHERE product_size_color_warranty.product_size_color_id = '$prdt_size_color_id'");

    $product_color_warranty_data = array();
    if (mysqli_num_rows($product_size_color_warranty_rlt)) {
        while ($row_warranty = $product_size_color_warranty_rlt->fetch_assoc()) {
            $product_color_warranty_data1['warranty_id'] = $row_warranty['warranty_id'];
            $product_color_warranty_data1['warranty_name'] = $row_warranty['warranty'];
            $product_color_warranty_data1['offer_price'] = $row_warranty['offer_price'];
            $product_color_warranty_data1['price'] = $row_warranty['price'];
            $warranty_count = $row_warranty['count'] ? $row_warranty['count'] : 0;
            $product_color_warranty_data1['warranty_count'] = $warranty_count;
            array_push($product_color_warranty_data, $product_color_warranty_data1);
        }

        $color_warranty_list = $product_color_warranty_data;
    }
} else {
    $color_warranty_list = $product_size['color_stock'][0]['warranty_data'];
}


?>
<div class="select-style mb-15 mt-15 col-md-4">
    <select class="select-two-active prdt-detail-color-warranty-select"
            data-minimum-results-for-search="Infinity">
        <?php foreach ($color_warranty_list AS $prdt_warranty) { ?>
            <option value="<?php echo $prdt_warranty['warranty_id']; ?>"
                    warranty-id="<?php echo $prdt_warranty['warranty_id']; ?>"
                    count-id="<?php echo $prdt_warranty['warranty_count']; ?>"
                    price-id="<?php echo $prdt_warranty['price']; ?>"
                    offer-price-id="<?php echo $prdt_warranty['offer_price']; ?>">
                <?php echo $prdt_warranty['warranty_name']; ?>
            </option>
        <?php } ?>
    </select>
</div>
