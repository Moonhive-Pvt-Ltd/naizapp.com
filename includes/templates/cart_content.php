<?php
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';

$post = [
  'uid' => $user_uid,
  'vendor_uid' => $vendor_uid,
];
$url = BASE_URL . "get_cart_list";
$result = getApiData($url, $post);
if ($result['status'] == 'Success') {
    ?>
    <div class="cart-area pt-100 pb-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <form action="#">
                        <div class="cart-table-content">
                            <div class="table-content table-responsive">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="width-thumbnail"></th>
                                        <th class="width-name">Product</th>
                                        <th></th>
                                        <th class="width-price"> Price</th>
                                        <th class="width-quantity">Quantity</th>
                                        <th class="width-subtotal">Subtotal</th>
                                        <th class="width-remove"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="cart-table-body-div">
                                    <?php
                                    if (count($result['cart_detail']['cart']) > 0) {
                                        foreach ($result['cart_detail']['cart'] as $row) {
                                            $i = 0;
                                            if (count($row['warranty']) > 0) {
                                                foreach ($row['warranty'] as $wrnty) {
                                                    $row['cart_id'] = $wrnty['cart_id'];
                                                    $row['count'] = $wrnty['count'];
                                                    $row['price'] = $wrnty['price'];
                                                    $row['offer_price'] = $wrnty['offer_price'];
                                                    $row['display_price'] = $wrnty['display_price'];
                                                    $row['total_display_price'] = $wrnty['total_display_price'];
                                                    $row['warranty_id'] = $wrnty['warranty_id'];

                                                    include 'cart_table_tr.php';
                                                    $i++;
                                                }
                                            } else {
                                                include 'cart_table_tr.php';
                                            }
                                            if ($row['error']) { ?>
                                                <tr>
                                                    <td colspan="7"
                                                        class="no-stock-available-tr no-stock-available-td
                                                        no-stock-available-td-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>">
                                                        <h6 class="d-flex flex-row align-items-center color-red">
                                                            <?php if ($row['color_code']) { ?>
                                                                <div class="cart-color-div margin-right-7px"
                                                                     style="background-color: <?php echo $row['color_code']; ?>"></div>
                                                            <?php }
                                                            echo $row['name'] . ' - ' . $row['size'] . ' - ' . $row['error']; ?>
                                                        </h6>
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                    } else { ?>
                                        <tr>
                                            <td colspan="7">
                                                <h4 class="text-center">Cart Empty</h4>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="cart-shiping-update-wrapper">
                                    <div class="cart-shiping-update btn-hover">
                                        <a href="products">Continue Shopping</a>
                                    </div>
                                    <?php if (count($result['cart_detail']['cart']) > 0) { ?>
                                        <div class="cart-clear-wrap">
                                            <!--                                    <div class="cart-clear btn-hover">-->
                                            <!--                                        <button>Update Cart</button>-->
                                            <!--                                    </div>-->
                                            <div class="cart-clear btn-hover">
                                                <button class="clear-cart-btn" vendor-uid="<?php echo $vendor_uid; ?>">
                                                    Clear
                                                    Cart
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row justify-content-end">
                <?php if (count($result['cart_detail']['cart']) > 0) { ?>
                    <div class="col-lg-4 col-md-12 col-12 checkout-btn-div">
                        <div class="grand-total-wrap">
                            <div class="grand-total">
                                <h4>Total <span>
                                        ₹<span class="total-price-cost"><?php echo $result['cart_detail']['total_cost']; ?></span>
                                        </span>
                                </h4>
                            </div>
                            <div class="grand-total-btn btn-hover">
                            <span class="btn theme-color proceed-to-checkout-btn"
                                  vendor-uid="<?php echo $vendor_uid; ?>">Proceed to checkout</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>