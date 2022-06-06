<?php
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$product_uid = isset($_GET['id']) ? $_GET['id'] : '';

$post = [
  'product_uid' => $product_uid,
  'vendor_uid' => $vendor_uid,
];
$url = BASE_URL . "get_product_details";
$result = getApiData($url, $post);
$prdt_detail = null;

if ($result['status'] == 'Success') {
    $prdt_detail = $result['product_detail'];
}

if ($prdt_detail != null) {
    $product_image = $prdt_detail['product_image'];
    $product_size_list = $prdt_detail['product_size'];
    $product_size = $prdt_detail['product_size'][0];
    ?>
    <input type="hidden" value="<?php echo $prdt_detail['product_id']; ?>" id="productId">
    <input type="hidden" value="<?php echo $prdt_detail['vendor_id']; ?>" id="vendorId">
    <input type="hidden" value="<?php echo $vendor_uid; ?>" id="vendorUId">
    <input type="hidden" value="<?php echo $product_uid; ?>" id="productUid">
    <div class="product-details-area pb-50 pt-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="product-details-img-wrap product-details-vertical-wrap" data-aos="fade-up"
                         data-aos-delay="200">
                        <div class="product-details-small-img-wrap">
                            <div class="swiper-container product-details-small-img-slider-1 pd-small-img-style">
                                <div class="swiper-wrapper">
                                    <?php foreach ($product_image AS $prdt_img) { ?>
                                        <div class="swiper-slide">
                                            <div class="product-details-small-img">
                                                <img src="<?php echo $prdt_img; ?>"
                                                     alt="Product Thumnail">
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="pd-prev pd-nav-style"><i class="ti-angle-up"></i></div>
                            <div class="pd-next pd-nav-style"><i class="ti-angle-down"></i></div>
                        </div>
                        <div class="swiper-container product-details-big-img-slider-1 pd-big-img-style">
                            <div class="swiper-wrapper">
                                <?php foreach ($product_image AS $prdt_img) { ?>
                                    <div class="swiper-slide">
                                        <div class="easyzoom-style">
                                            <div class="easyzoom easyzoom--overlay">
                                                <a href="<?php echo $prdt_img; ?>">
                                                    <img src="<?php echo $prdt_img; ?>"
                                                         alt="">
                                                </a>
                                            </div>
                                            <a class="easyzoom-pop-up img-popup"
                                               href="<?php echo $prdt_img; ?>">
                                                <i class="pe-7s-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product-details-content" data-aos="fade-up" data-aos-delay="400">
                        <h2><?php echo $prdt_detail['name']; ?></h2>
                        <div id="prdtSizeDetailPriceContent">
                        </div>
                        <div class="product-details-review">
                            <div class="product-rating">
                                <?php for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $prdt_detail['product_rating']) { ?>
                                        <i class="ti-star star-active"></i>
                                    <?php } else { ?>
                                        <i class="ti-star"></i>
                                    <?php }
                                } ?>
                            </div>
                            <span>( <?php echo $prdt_detail['total_review_count']; ?> Customer Review )</span>
                        </div>
                        <div class="select-style mb-15 mt-15 col-md-4">
                            <select class="select-two-active prdt-detail-size-select"
                                    data-minimum-results-for-search="Infinity">
                                <?php $i = 0;
                                foreach ($product_size_list AS $prdt_size) { ?>
                                    <option value="<?php echo $prdt_size['id']; ?>"><?php echo $prdt_size['size']; ?></option>
                                    <?php $i++;
                                } ?>
                            </select>
                        </div>
                        <div id="prdtSizeDetailColorCartContent"></div>
                        <div id="prdtSizeDetailContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="description-review-area pb-85">
        <div class="container">
            <div class="description-review-topbar nav" data-aos="fade-up" data-aos-delay="200">
                <a class="active" data-bs-toggle="tab" href="#des-details1"> Description </a>
                <a data-bs-toggle="tab" href="#des-details2" class=""> Information </a>
                <a data-bs-toggle="tab" href="#des-details3" class="prdt-review-btn"> Reviews </a>
            </div>
            <div class="tab-content">
                <div id="des-details1" class="tab-pane active">
                    <div class="product-description-content text-center">
                        <p data-aos="fade-up" data-aos-delay="200">
                            <?php echo $prdt_detail['description']; ?>
                        </p>
                    </div>
                </div>
                <div id="des-details2" class="tab-pane">
                    <div class="specification-wrap table-responsive">
                        <table>
                            <tbody>
                            <tr>
                                <td class="width1">Categories</td>
                                <td><?php echo $prdt_detail['category']; ?></td>
                            </tr>
                            <tr>
                                <td class="width1">Tags</td>
                                <td><?php echo $prdt_detail['tag']; ?></td>
                            </tr>
                            <tr>
                                <td class="width1">Brand</td>
                                <td><?php echo $prdt_detail['brand']; ?></td>
                            </tr>
                            <tr>
                                <td class="width1">Material</td>
                                <td><?php echo $prdt_detail['material']; ?></td>
                            </tr>
                            <?php if ($prdt_detail['code']) { ?>
                                <tr>
                                    <td class="width1">Code</td>
                                    <td><?php echo $prdt_detail['code']; ?></td>
                                </tr>
                            <?php }
                            if ($prdt_detail['batch_no']) { ?>
                                <tr>
                                    <td class="width1">Batch Number</td>
                                    <td><?php echo $prdt_detail['batch_no']; ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td class="width1">Size</td>
                                <td>
                                    <?php
                                    echo implode(", ", array_column($product_size_list, "size"));
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="des-details3" class="tab-pane">
                    <div class="review-wrapper">
                        <h3><?php echo $prdt_detail['total_review_count']; ?> review
                            for <?php echo $prdt_detail['name']; ?></h3>
                        <div id="prdtDetailReviewTableContent"></div>
                    </div>
                    <div id="prdtDetailAddReviewTableContent">
                   </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

