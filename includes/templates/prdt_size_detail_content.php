<?php
require_once("../functions.php");
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$user_id = '';

if ($user_uid) {
    $user_data = getUserData($mysqli, $user_uid);
    $user_id = $user_data['id'];
}

if (isset($_POST['size_id'])) {
    $prdt_size_id = $_POST['size_id'];
    $product_id = $_POST['product_id'];
    $vendor_id = $_POST['vendor_id'];
    $product_size_query = mysqli_query($mysqli, "SELECT product_size.*,
                                                                           (CASE WHEN product_size.offer_price > 0 
                                                                            THEN product_size.offer_price 
                                                                            ELSE product_size.price END) AS display_price
                                                                    FROM product_size
                                                                    INNER JOIN vendor_stock
                                                                    ON vendor_stock.product_size_id = product_size.id
                                                                    WHERE product_size.product_id = '$product_id'
                                                                    AND vendor_stock.vendor_id = '$vendor_id'
                                                                    AND product_size.status = 'active'
                                                                    AND product_size.id = '$prdt_size_id'
                                                                    GROUP BY product_size.id
                                                                    ORDER BY display_price ASC");
    $product_size_data_rlt = array();
    if (mysqli_num_rows($product_size_query)) {
        while ($row2 = $product_size_query->fetch_assoc()) {
            $product_size_data_rlt['product_size_stock'] = 0;
            $product_size_data_rlt['product_cart_count'] = 0;

            $product_size_id = $row2['id'];
            $product_size_data_rlt['id'] = $row2['id'];
            $product_size_data_rlt['size'] = $row2['size'];
            $product_size_data_rlt['unit_length'] = $row2['unit_length'] . $row2['unit_length_unit'];
            if ($row2['length'] && $row2['width'] && $row2['height']) {
                $product_size_data_rlt['dimensions'] = $row2['length'] . $row2['length_unit'] . 'X' . $row2['width'] . $row2['width_unit'] . 'X' . $row2['height'] . $row2['height_unit'];
            } else if ($row2['length'] && $row2['width']) {
                $product_size_data_rlt['dimensions'] = $row2['length'] . $row2['length_unit'] . 'X' . $row2['width'] . $row2['width_unit'];
            } else if ($row2['width'] && $row2['height']) {
                $product_size_data_rlt['dimensions'] = $row2['width'] . $row2['width_unit'] . 'X' . $row2['height'] . $row2['height_unit'];
            } else if ($row2['length'] && $row2['height']) {
                $product_size_data_rlt['dimensions'] = $row2['length'] . $row2['length_unit'] . 'X' . $row2['height'] . $row2['height_unit'];
            } else if ($row2['length']) {
                $product_size_data_rlt['dimensions'] = $row2['length'] . $row2['length_unit'];
            } else if ($row2['width']) {
                $product_size_data_rlt['dimensions'] = $row2['width'] . $row2['width_unit'];
            } else if ($row2['height']) {
                $product_size_data_rlt['dimensions'] = $row2['height'] . $row2['height_unit'];
            } else {
                $product_size_data_rlt['dimensions'] = '';
            }
            $product_size_data_rlt['thickness'] = $row2['thickness'] . $row2['thickness_unit'];
            $product_size_data_rlt['weight'] = $row2['weight'] . $row2['weight_unit'];
            $product_size_data_rlt['diameter'] = $row2['diameter'] . $row2['diameter_unit'];
            $product_size_data_rlt['display_price'] = $row2['display_price'];
            $product_size_data_rlt['offer_price'] = $row2['offer_price'];
            $product_size_data_rlt['price'] = $row2['price'];

            $product_size_color_rlt = mysqli_query($mysqli, "SELECT color.id AS color_id,
                                                                                             color.name,
                                                                                             color.code,
                                                                                             vendor_stock.stock,
                                                                                             SUM(cart.count) AS count,
                                                                                             product_size_color.id AS prdt_size_color_id  
                                                                                      FROM product_size_color
                                                                                      LEFT JOIN color
                                                                                      ON color.id = product_size_color.color_id
                                                                                      LEFT JOIN vendor_stock
                                                                                      ON vendor_stock.product_size_id = product_size_color.product_size_id
                                                                                      AND vendor_stock.color_id = product_size_color.color_id
                                                                                      AND vendor_stock.vendor_id = '$vendor_id'
                                                                                      LEFT JOIN cart
                                                                                      ON cart.product_size_id = vendor_stock.product_size_id
                                                                                      AND cart.user_id = '$user_id'
                                                                                      AND cart.color_id = vendor_stock.color_id
                                                                                      AND cart.vendor_id = '$vendor_id'
                                                                                      WHERE product_size_color.product_size_id = '$product_size_id'
                                                                                      GROUP BY color_id");
            $product_color_data = array();
            if (mysqli_num_rows($product_size_color_rlt)) {
                $product_size_data_rlt['color_check'] = 1;
                while ($row3 = $product_size_color_rlt->fetch_assoc()) {
                    $prdt_size_color_id = $row3['prdt_size_color_id'];
                    $color_id = $row3['color_id'];
                    $product_color_data_rlt['color_id'] = $color_id;
                    $product_color_data_rlt['prdt_size_color_id'] = $prdt_size_color_id;
                    $product_color_data_rlt['color_name'] = $row3['name'];
                    $product_color_data_rlt['color_code'] = $row3['code'];
                    $product_color_data_rlt['warranty_check'] = 0;
                    $product_color_data_rlt['stock'] = $row3['stock'] ? $row3['stock'] : 0;
                    $product_color_data_rlt['count'] = $row3['count'] ? $row3['count'] : 0;

                    $product_size_color_warranty_rlt = mysqli_query($mysqli, "SELECT product_size_color_warranty.*,
                                                                                                                  warranty.warranty,
                                                                                                                  cart.count
                                                                                                          FROM product_size_color_warranty
                                                                                                          INNER JOIN warranty
                                                                                                          ON warranty.id = product_size_color_warranty.warranty_id
                                                                                                          LEFT JOIN cart
                                                                                                          ON cart.warranty_id = warranty.id
                                                                                                          AND cart.color_id = '$color_id'
                                                                                                          AND cart.user_id = '$user_id'
                                                                                                          AND cart.vendor_id = '$vendor_id'
                                                                                                          AND cart.product_size_id = '$product_size_id'
                                                                                                          WHERE product_size_color_warranty.product_size_color_id = '$prdt_size_color_id'");
                    $product_color_warranty_data = array();
                    if (mysqli_num_rows($product_size_color_warranty_rlt)) {
                        $product_color_data_rlt['warranty_check'] = 1;
                        while ($row_warranty = $product_size_color_warranty_rlt->fetch_assoc()) {
                            $product_color_warranty_data1['warranty_id'] = $row_warranty['warranty_id'];
                            $product_color_warranty_data1['warranty_name'] = $row_warranty['warranty'];
                            $product_color_warranty_data1['offer_price'] = $row_warranty['offer_price'];
                            $product_color_warranty_data1['price'] = $row_warranty['price'];
                            $warranty_count = $row_warranty['count'] ? $row_warranty['count'] : 0;
                            $product_color_warranty_data1['warranty_count'] = $warranty_count;
                            array_push($product_color_warranty_data, $product_color_warranty_data1);
                        }
                    }

                    $product_color_data_rlt['warranty_data'] = $product_color_warranty_data;

                    array_push($product_color_data, $product_color_data_rlt);
                }
            } else {
                $product_size_data_rlt['color_check'] = 0;
                $product_size_stock_rlt = mysqli_query($mysqli, "SELECT vendor_stock.stock,
                                                                                                 cart.count  
                                                                                         FROM product_size
                                                                                         LEFT JOIN vendor_stock
                                                                                         ON vendor_stock.product_size_id = product_size.id
                                                                                         AND vendor_stock.vendor_id = '$vendor_id'
                                                                                         LEFT JOIN cart
                                                                                         ON cart.product_size_id = vendor_stock.product_size_id
                                                                                         AND cart.user_id = '$user_id'
                                                                                         AND cart.vendor_id = '$vendor_id'
                                                                                         WHERE product_size.id = '$product_size_id'");
                if (mysqli_num_rows($product_size_stock_rlt)) {
                    $stock_row = $product_size_stock_rlt->fetch_assoc();
                    $product_size_data_rlt['product_size_stock'] = $stock_row['stock'] ? $stock_row['stock'] : 0;
                    $product_size_data_rlt['product_cart_count'] = $stock_row['count'] ? $stock_row['count'] : 0;
                }
            }
            $product_size_data_rlt['color_stock'] = $product_color_data;
        }
    }
    $product_size = $product_size_data_rlt;
}

if (mysqli_num_rows($product_size_query)) { ?>
    <div class="product-details-price">
        <?php if ($product_size['color_check']) { ?>
            <?php if (count($product_size['color_stock'][0]['warranty_data']) > 0) {
                if ($product_size['color_stock'][0]['warranty_data'][0]['offer_price'] > 0) { ?>
                    <span class="old-price"><?php echo '₹' . $product_size['color_stock'][0]['warranty_data'][0]['price']; ?></span>
                    <span class="new-price"><?php echo '₹' . $product_size['color_stock'][0]['warranty_data'][0]['offer_price']; ?></span>
                <?php } else { ?>
                    <span class="new-price"><?php echo '₹' . $product_size['color_stock'][0]['warranty_data'][0]['price']; ?></span>
                <?php }
            } else {
                if ($product_size['offer_price'] > 0) { ?>
                    <span class="old-price"><?php echo '₹' . $product_size['price']; ?></span>
                    <span class="new-price"><?php echo '₹' . $product_size['offer_price']; ?></span>
                <?php } else { ?>
                    <span class="new-price"><?php echo '₹' . $product_size['price']; ?></span>
                <?php }
            }
        } else {
            if ($product_size['offer_price'] > 0) { ?>
                <span class="old-price"><?php echo '₹' . $product_size['price']; ?></span>
                <span class="new-price"><?php echo '₹' . $product_size['offer_price']; ?></span>
            <?php } else { ?>
                <span class="new-price"><?php echo '₹' . $product_size['price']; ?></span>
            <?php }
        } ?>
    </div>
    <div class="product-details-color">
        <?php if ($product_size['color_check']) { ?>
            <div class="product-color product-color-active product-details-color">
                <span>Color :</span>
                <ul>
                    <?php $c = 0;
                    foreach ($product_size['color_stock'] as $row) {
                        $col_count = $row['warranty_data'];
                        if (count($row['warranty_data']) > 0) {
                            $col_count = $row['warranty_data'][0]['warranty_count'];
                        }
                        ?>
                        <li><a title="<?php $row['color_name']; ?>"
                               class="prdt-color-select <?php echo $c == 0 ? 'active' : '';
                               echo ($row['color_code'] == '#FFFFFF' || $row['color_code'] == '#FFF') ? 'white-a' : ''; ?>"
                               warranty-check="<?php echo $row['warranty_check']; ?>"
                               prdt-size-color-id="<?php echo $row['prdt_size_color_id']; ?>"
                               color-id="<?php echo $row['color_id']; ?>"
                               color-count="<?php echo $col_count; ?>"
                               color-stock="<?php echo $row['stock']; ?>"
                               color-code="<?php echo $row['color_code']; ?>"
                               style="background-color: <?php echo $row['color_code']; ?>; border: 1px solid #AAA;">
                                <?php echo $row['color_name']; ?></a>
                        </li>
                        <?php $c++;
                    } ?>
                </ul>
            </div>

            <?php if (count($product_size['color_stock'][0]['warranty_data']) > 0) { ?>
                <div class="color-warranty-select-div">
                    <?php include_once '../templates/prdt_detail_color_warranty_select.php'; ?>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="product-details-action-wrap mt-3">
            <?php if ($product_size['color_check'] == 0) { ?>
                <input type="hidden" value="<?php echo $product_size['product_cart_count']; ?>" id="currentCount">
                <input type="hidden" value="<?php echo $product_size['product_size_stock']; ?>" id="totalStockCount">
            <?php } ?>

            <?php if ($product_size['color_check'] == 0) { ?>
                <div class="product-quality">
                    <input class="count-val cart-plus-minus-box-prdt-detail input-text qty text" name="qtybutton"
                           value="<?php echo ($product_size['product_size_stock'] > 0 || $product_size['product_size_stock'] == 'unlimited') ? ($product_size['product_cart_count'] > 0 ? $product_size['product_cart_count'] : 1) : 0; ?>">
                </div>
            <?php } else {
                if (count($product_size['color_stock'][0]['warranty_data']) > 0) {
                    ?>
                    <div class="product-quality">
                        <input class="count-val cart-plus-minus-box-prdt-detail input-text qty text" name="qtybutton"
                               value="<?php echo ($product_size['color_stock'][0]['stock'] > 0 || $product_size['color_stock'][0]['stock'] == 'unlimited') ? ($product_size['color_stock'][0]['warranty_data'][0]['warranty_count'] > 0 ? $product_size['color_stock'][0]['warranty_data'][0]['warranty_count'] : 1) : 0; ?>">
                    </div>
                <?php } else { ?>
                    <div class="product-quality">
                        <input class="count-val cart-plus-minus-box-prdt-detail input-text qty text" name="qtybutton"
                               value="<?php echo ($product_size['color_stock'][0]['stock'] > 0 || $product_size['color_stock'][0]['stock'] == 'unlimited') ? ($product_size['color_stock'][0]['count'] > 0 ? $product_size['color_stock'][0]['count'] : 1) : 0; ?>">
                    </div>
                <?php }
            } ?>


            <div class="product-detail-cart prdt-detail-add-to-cart-btn
            <?php if ($product_size['color_check'] == 0) {
                if ($product_size['product_size_stock'] > 0 || $product_size['product_size_stock'] == 'unlimited') { ?>
                           product-detail-cart-btn-color btn-hover
                    <?php } else { ?>
                           product-detail-cart-out-of-stock-btn
            <?php }
            } else {
                if ($product_size['color_stock'][0]['stock'] > 0 || $product_size['color_stock'][0]['stock'] == 'unlimited') { ?>
                    product-detail-cart-btn-color btn-hover
                    <?php } else { ?>
                    product-detail-cart-out-of-stock-btn
            <?php }
            } ?>">
                <input type="hidden" value="<?php echo $prdt_size_id; ?>" id="prdtSizeId">
                <input type="hidden" value="<?php echo $product_size['color_check']; ?>" id="colorCheck">
                <?php
                if ($product_size['color_check'] == 0) {
                    if ($product_size['product_size_stock'] > 0 || $product_size['product_size_stock'] == 'unlimited') {
                        if ($product_size['product_cart_count'] > 0) { ?>
                            <a href="" type="go_to_cart" class="cart-btn">Go to Cart</a>
                        <?php } else { ?>
                            <a href="" type="add_to_cart" class="cart-btn">Add to Cart</a>
                        <?php }
                    } else { ?>
                        <a href="" type="out_of_stock" class="cart-btn">Out of Stock</a>
                    <?php }
                } else {
                    if ($product_size['color_stock'][0]['stock'] > 0 || $product_size['color_stock'][0]['stock'] == 'unlimited') {
                        if (count($product_size['color_stock'][0]['warranty_data']) > 0) {
                            if ($product_size['color_stock'][0]['warranty_data'][0]['warranty_count'] > 0) { ?>
                                <a href="" type="go_to_cart" class="cart-btn">Go to Cart</a>
                            <?php } else { ?>
                                <a href="" type="add_to_cart" class="cart-btn">Add to Cart</a>
                            <?php }
                        } else {
                            if ($product_size['color_stock'][0]['count'] > 0) { ?>
                                <a href="" type="go_to_cart" class="cart-btn">Go to Cart</a>
                            <?php } else { ?>
                                <a href="" type="add_to_cart" class="cart-btn">Add to Cart</a>
                            <?php }
                        }
                    } else { ?>
                        <a href="" type="out_of_stock" class="cart-btn">Out of Stock</a>
                    <?php }
                } ?>
            </div>
        </div>
        <script src="assets/js/main.js"></script>
    </div>

    <div class="product-details-meta col-md-12 row">
        <?php
        $remove = array(
          "id",
          "size",
          "product_cart_count",
          "product_size_stock",
          "color_check",
          "display_price",
          "color_stock",
          "offer_price",
          "price",
        );

        foreach ($remove as $key) {
            unset($product_size[$key]);
        }

        foreach ($product_size AS $key => $data1) {
            $explode = explode("_", $key);
            $label = implode(" ", $explode);
            if ($data1) { ?>
                <div class="col-md-12 mb-3"><span class="title"><?php echo ucwords($label); ?>:</span>
                    <?php echo $data1; ?>
                </div>
            <?php }
        }
        ?>
    </div>
<?php } ?>


