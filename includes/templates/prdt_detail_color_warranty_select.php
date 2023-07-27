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
    $design_id = $_POST['design_id'];
    $cart_id = $_POST['cart_id'];

    $cart_vendor_id = '';
    $cart_product_size_id = '';
    $cart_color_id = '';
    $cart_warranty_id = '';
    $cart_product_design_id = '';
    $cart_count_value = '';
    $cart_query = mysqli_query($mysqli, "SELECT * FROM cart WHERE id = '$cart_id'");
    if (mysqli_num_rows($cart_query)) {
        $cart_row = mysqli_fetch_array($cart_query);
        $cart_vendor_id = $cart_row['vendor_id'];
        $cart_product_size_id = $cart_row['product_size_id'];
        $cart_color_id = $cart_row['color_id'];
        $cart_warranty_id = $cart_row['warranty_id'];
        $cart_product_design_id = $cart_row['product_design_id'];
        $cart_count_value = $cart_row['count'];
    }

    $product_size_color_warranty_rlt = mysqli_query($mysqli, "SELECT product_size_color_warranty.*,
                                                                                            warranty.warranty
                                                                                     FROM product_size_color_warranty
                                                                                     INNER JOIN warranty
                                                                                     ON warranty.id = product_size_color_warranty.warranty_id
                                                                                     WHERE product_size_color_warranty.product_size_color_id = '$prdt_size_color_id'");

    $product_color_warranty_data = array();
    if (mysqli_num_rows($product_size_color_warranty_rlt)) {
        $product_color_data_rlt['warranty_check'] = 1;
        while ($row_warranty = $product_size_color_warranty_rlt->fetch_assoc()) {
            $product_color_warranty_data1['warranty_id'] = $row_warranty['warranty_id'];
            $product_color_warranty_data1['warranty_name'] = $row_warranty['warranty'];
            $product_color_warranty_data1['offer_price'] = $row_warranty['offer_price'];
            $product_color_warranty_data1['price'] = $row_warranty['price'];
            array_push($product_color_warranty_data, $product_color_warranty_data1);
        }
        $color_warranty_list = $product_color_warranty_data;
    }

} else {
    $color_warranty_list = $product_size['color_stock'][$a]['warranty_data'];
}

?>
<div class="select-style mb-15 mt-15 col-md-4">
    <select class="select-two-active prdt-detail-color-warranty-select"
            data-minimum-results-for-search="Infinity">
        <?php foreach ($color_warranty_list AS $prdt_warranty) {
            $war_count = getCartCount($user_id, $vendor_id, $product_size_id, $color_id, $prdt_warranty['warranty_id'], $design_id, false, $mysqli);
            ?>
            <option value="<?php echo $prdt_warranty['warranty_id']; ?>"
                    warranty-id="<?php echo $prdt_warranty['warranty_id']; ?>"
                    count-id="<?php echo $war_count; ?>"
                    price-id="<?php echo $prdt_warranty['price']; ?>"
                    offer-price-id="<?php echo $prdt_warranty['offer_price']; ?>"
                <?php if ($cart_warranty_id != '') {
                    if ($cart_warranty_id == $prdt_warranty['warranty_id']) {
                        echo "selected";
                    }
                } ?>>
            <?php echo $prdt_warranty['warranty_name']; ?>
            </option>
        <?php } ?>
    </select>
</div>
