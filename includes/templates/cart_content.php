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
                                        <th class="width-price"> Price</th>
                                        <th class="width-quantity">Quantity</th>
                                        <th class="width-subtotal">Subtotal</th>
                                        <th class="width-remove"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="cart-table-body-div">
                                    <?php
                                    if (count($result['cart_detail']['cart']) > 0) {
                                        foreach ($result['cart_detail']['cart'] as $row) { ?>
                                            <tr class="cart-tr" style="position: relative"
                                                product-uid="<?php echo $row['product_uid']; ?>"
                                                vendor-uid="<?php echo $vendor_uid; ?>"
                                                product-size-id="<?php echo $row['product_size_id']; ?>"
                                                color-id="<?php echo $row['color_id']; ?>"
                                                count="<?php echo $row['count']; ?>"
                                                display-price="<?php echo $row['display_price']; ?>">
                                                <input type="hidden" value="<?php echo $row['count']; ?>"
                                                       class="stock-count">
                                                <input type="hidden" value="<?php echo $row['display_price']; ?>"
                                                       class="stock-price">
                                                <td class="product-thumbnail">
                                                    <a href="#">
                                                        <img src="<?php echo $row['image']; ?>" alt=""/>
                                                    </a>
                                                </td>
                                                <td class="product-name">
                                                    <h6>
                                                        <a href="#">
                                                            <?php echo $row['name']; ?>
                                                        </a>
                                                        <div class="d-flex align-items-center">
                                                            <span class="cart-size-span"><?php echo $row['size']; ?></span>
                                                            <?php if ($row['color_code']) { ?>
                                                                <div class="cart-color-div"
                                                                     style="background-color: <?php echo $row['color_code']; ?>"></div>
                                                            <?php } ?>
                                                        </div>
                                                    </h6>
                                                </td>
                                                <td class="product-cart-price"><span
                                                            class="amount">₹<?php echo $row['display_price']; ?></span>
                                                </td>
                                                <td class="cart-quality">
                                                    <div class="product-quality">
                                                        <input class="cart-plus-minus-box input-text qty text"
                                                               name="qtybutton"
                                                               product-uid="<?php echo $row['product_uid']; ?>"
                                                               vendor-uid="<?php echo $vendor_uid; ?>"
                                                               product-size-id="<?php echo $row['product_size_id']; ?>"
                                                               color-id="<?php echo $row['color_id']; ?>"
                                                               stock="<?php echo $row['stock']; ?>"
                                                               current-val="<?php echo $row['count']; ?>"
                                                               value="<?php echo $row['count']; ?>">
                                                    </div>
                                                </td>
                                                <td class="product-total">
                                                    ₹<span
                                                            class="total-display-price-amount"><?php echo $row['total_display_price']; ?></span>
                                                </td>
                                                <td class="product-remove">
                                                    <i class="ti-trash delete-cart-item cursor-pointer"></i>
                                                </td>
                                            </tr>
                                            <?php if ($row['error']) { ?>
                                                <tr>
                                                    <td colspan="6" class="no-stock-available-tr no-stock-available-td">
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
                                            <td colspan="6">
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