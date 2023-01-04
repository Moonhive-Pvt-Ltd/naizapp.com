<?php
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';

$post = [
    'uid' => $user_uid,
    'vendor_uid' => $vendor_uid,
    'type' => 'initial',
];
$url = BASE_URL . "get_vendor_pincode";
$result = getApiData($url, $post);
if ($result['status'] == 'Success') {
//    print_r($result['pincode']);
}

$url1 = BASE_URL . "get_cart_list";
$result1 = getApiData($url1, $post);
if ($result['status'] == 'Success') {
//    print_r($result1['cart_detail']);
    $subtotal = $result1['cart_detail']['total_cost'];
    $flat_rate = 0;
    $shipping_fee = 0;
    $total = ($subtotal - $flat_rate) + $shipping_fee;
    $tax = $result1['cart_detail']['total_tax'];
    $total_cost = $total + $tax;
}

$full_name = '';
$email = '';
$mobile = '';
if ($user_uid) {
    $user_data = getUserData($mysqli, $user_uid);
    $full_name = $user_data['full_name'];
    $email = $user_data['email'];
    $mobile = $user_data['mobile'];
}
?>

<div class="checkout-main-area pb-100 pt-100">
    <input type="hidden" value="<?php echo $result1['cart_detail']['total_tax']; ?>" id="taxValue">
    <input type="hidden" value="" id="promoCodeId">
    <input type="hidden" value="" id="selectedAddressId">
    <div class="container">
        <div class="customer-zone mb-20">
            <p class="cart-page-title">Have a coupon? <a class="checkout-click3" href="#">Click here to enter your
                    code</a></p>
            <div class="checkout-login-info3">
                <form id="appliedPromoCode" method="post">
                    <input type="text" placeholder="Coupon code" name="promo_code" class="change-promo-code"
                           id="promoCode" required>
                    <input type="hidden" value="<?php echo $subtotal; ?>" name="total_cost">
                    <input type="hidden" name="vendor_uid" value="<?php echo $vendor_uid; ?>">
                    <input type="hidden" name="uid" value="<?php echo $user_uid; ?>">
                    <input type="submit" value="Apply Coupon" id="applyCouponBtn">
                </form>
            </div>
        </div>
        <div class="checkout-wrap pt-30">
            <div class="row">
                <div class="col-lg-7">
                    <div class="billing-info-wrap">
                        <h3>Billing Details</h3>
                        <div class="checkout-account mt-25">
                            <input class="checkout-toggle" type="checkbox">
                            <span>Add Address</span>
                        </div>
                        <div class="different-address open-toggle mt-30">
                            <form id="addUserBillingAddressForm" method="post">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="billing-info mb-20">
                                            <label>Full Name <abbr class="required" title="required">*</abbr></label>
                                            <input type="text" name="full_name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="billing-info mb-20">
                                            <label>Phone <abbr class="required" title="required">*</abbr></label>
                                            <input type="text" name="phone_number" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="billing-info mb-20">
                                            <label>Address <abbr class="required" title="required">*</abbr></label>
                                            <input type="text" name="address" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="billing-info mb-20">
                                            <label>City <abbr class="required"
                                                              title="required">*</abbr></label>
                                            <input type="text" name="city_district" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="billing-select select-style mb-20">
                                            <label>Postcode / ZIP <abbr class="required"
                                                                        title="required">*</abbr></label>
                                            <select class="select-two-active" name="zip" required>
                                                <option value="" hidden></option>
                                                <?php foreach ($result['pincode'] as $row) { ?>
                                                    <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="billing-select select-style mb-20">
                                            <label>House / Company <abbr class="required"
                                                                         title="required">*</abbr></label>
                                            <select class="select-two-active" name="type" required>
                                                <option value="" hidden></option>
                                                <option value="house">House / Apartment</option>
                                                <option value="company">Agency / Company</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="button-box btn-hover">
                                    <input type="hidden" name="uid"
                                           value="<?php echo $user_uid; ?>">
                                    <button type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="userAddressCheckoutContent"></div>
                </div>
                <div class="col-lg-5">
                    <div class="your-order-area">
                        <h3>Your order</h3>
                        <div class="your-order-wrap gray-bg-4">
                            <div class="your-order-info-wrap">
                                <div class="your-order-info">
                                    <ul>
                                        <li>Product <span>Total</span></li>
                                    </ul>
                                </div>
                                <div class="your-order-middle">
                                    <ul>
                                        <?php
                                        $length = count($result1['cart_detail']['cart']);
                                        $j=0;
                                        foreach ($result1['cart_detail']['cart'] as $row1) {
                                            $j++; ?>
                                            <li class="<?php if($length !== $j) { echo 'checkout-order-list';}?>">
                                                <?php echo $row1['name'];
                                                if ($row1['is_warranty'] == 0) { ?>
                                                    <span><?php echo '₹' . ($row1['display_price'] * $row1['count']); ?> </span>
                                                <?php } ?>
                                                <br/>
                                                <?php if ($row1['is_warranty'] == 1) { ?>
                                                    <div>
                                                        <div class="d-flex flex-row align-items-center">
                                                    <span class="color-code-div"
                                                          style="background-color: <?php echo $row1['color_code']; ?>"></span>
                                                            <span class="margin-left-7px font-size-13px"><?php echo $row1['size']; ?></span>
                                                        </div>
                                                        <br/>
                                                        <?php foreach ($row1['warranty'] AS $warranty) { ?>
                                                            <div class="d-flex flex-row justify-content-between">
                                                                <span class="margin-left-7px font-size-13px"><?php echo $warranty['warranty'] . ' (' . $warranty['count'] . ')'; ?></span>
                                                                <span><?php echo '₹' . ($warranty['display_price'] * $warranty['count']); ?> </span>
                                                            </div>
                                                        <?php } ?>
                                                        <br/>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="d-flex flex-row align-items-center">
                                                    <span class="color-code-div"
                                                          style="background-color: <?php echo $row1['color_code']; ?>"></span>
                                                        <span class="margin-left-7px font-size-13px"><?php echo $row1['size'] . ' (' . $row1['count'] . ')'; ?></span>
                                                    </div>
                                                    <br/>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>

                                </div>
                                <div class="your-order-info order-subtotal">
                                    <ul>
                                        <li>Subtotal
                                            <span class="d-flex flex-row">₹<span
                                                        class="sub-total"><?php echo $subtotal ?></span></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <!--                                    <ul>-->
                                    <!--                                        <li>Shipping <p>Enter your full address </p>-->
                                    <!--                                        </li>-->
                                    <!--                                    </ul>-->
                                    <ul>
                                        <li class="d-flex flex-row justify-content-between">Promo Code
                                            <p class="d-flex flex-row">₹<span
                                                        class="flat-rate"><?php echo $flat_rate ?></span></p>
                                        </li>
                                        <li class="d-flex flex-row justify-content-between">Shipping Fee
                                            <p class="d-flex flex-row">₹<span
                                                        class="shipping-fee"><?php echo $shipping_fee ?></span></p>
                                        <li class="d-flex flex-row justify-content-between">Tax
                                            <p class="d-flex flex-row">₹<span class="tax"><?php echo $tax ?></span>
                                            </p>
                                    </ul>
                                </div>
                                <div class="your-order-info order-total">
                                    <ul>
                                        <li>Total
                                            <span class="d-flex flex-row">₹<span
                                                        class="total-cost"><?php echo $total_cost ?></span></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="Place-order btn-hover">
                            <input type="hidden" value="<?php echo $vendor_uid; ?>" id="vendorUid">
                            <input type="hidden" value="<?php echo $full_name; ?>" id="userFullName">
                            <input type="hidden" value="<?php echo $email; ?>" id="userEmail">
                            <input type="hidden" value="<?php echo $mobile; ?>" id="userMobile">
                            <input type="hidden" value="<?php echo $total_cost; ?>" id="totalAmount">
                            <a href="" class="place-order-btn">Place Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
