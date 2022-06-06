<?php
require_once("../functions.php");
$product_uid = isset($_GET['product_uid']) ? $_GET['product_uid'] : '';
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';

if ($user_uid == '') { ?>
    <div class="customer-zone mb-20 my-4">
        <p class="cart-page-title">Add a review? <a class="checkout-click1">Click here to login</a></p>
        <div class="checkout-login-info">
            <form id="userLoginForm" method="post" type-id="review_login">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="sin-checkout-login">
                            <label>Email address <span>*</span></label>
                            <input type="text" name="email" id="email" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="sin-checkout-login">
                            <label>Passwords <span>*</span></label>
                            <input type="password" name="password" id="password" required>
                        </div>
                    </div>
                </div>
                <div class="button-remember-wrap">
                    <button class="button" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
<?php } else { ?>
    <div class="ratting-form-wrapper add-prdt-review-div">
        <h3>Add a Review</h3>
        <div class="your-rating-wrap">
            <span>Your rating</span>
            <div class="your-rating">
                <span><i class="ti-star star-active" val-id="1"></i></span>
                <span><i class="ti-star" val-id="2"></i></span>
                <span><i class="ti-star" val-id="3"></i></span>
                <span><i class="ti-star" val-id="4"></i></span>
                <span><i class="ti-star" val-id="5"></i></span>
            </div>
        </div>
        <div class="ratting-form">
            <form id="addPrdtDetailReviewForm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="rating-form-style mb-15">
                            <label>Your review <span>*</span></label>
                            <textarea class="review-val" name="review"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-submit">
                            <input type="hidden" class="rating-val" name="rating" value="1">
                            <input type="hidden" class="prdt-uid-val" name="product_uid"
                                   value="<?php echo $product_uid; ?>">
                            <input type="submit" value="Submit" class="add-prdt-detail-review-btn">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
