<?php
require_once("../functions.php");

$color_id = $_GET['color_id'];
$prdt_id = $_GET['product_uid'];
$design_id = $_GET['design_id'];

$product_color_img_data = array();
$product_rlt = mysqli_query($mysqli, "SELECT id
                                             FROM product
                                             WHERE uid = '$prdt_id'");

$product_id = '';
if (mysqli_num_rows($product_rlt)) {
    $row1 = mysqli_fetch_array($product_rlt);
    $product_id = $row1['id'];
}

$product_color_img_rlt = mysqli_query($mysqli, "SELECT image
                                                       FROM product_color_image
                                                       WHERE product_id = '$product_id'
                                                       AND color_id = '$color_id'");
if (mysqli_num_rows($product_color_img_rlt)) {
    while ($row_color_img = $product_color_img_rlt->fetch_assoc()) {
        $product_color_img_data1 = IMG_URL . 'vendor_data/product/' . $row_color_img['image'];
        array_push($product_color_img_data, $product_color_img_data1);
    }
} else {
    $product_design_img_rlt = mysqli_query($mysqli, "SELECT image
                                                       FROM product_design_image
                                                       LEFT JOIN product_design
                                                       ON product_design.id = product_design_image.product_design_id
                                                       WHERE product_design.product_id = '$product_id'
                                                       AND product_design.id = '$design_id'
                                                       AND product_design.status = 'active'");
    if (mysqli_num_rows($product_design_img_rlt)) {
        while ($row_design_img = $product_design_img_rlt->fetch_assoc()) {
            $product_color_img_data1 = IMG_URL . 'vendor_data/product/' . $row_design_img['image'];
            array_push($product_color_img_data, $product_color_img_data1);
        }
    } else {
        $product_color_img_rlt = mysqli_query($mysqli, "SELECT image
                                                       FROM product_image
                                                       WHERE product_id = '$product_id' ");
        if (mysqli_num_rows($product_color_img_rlt)) {
            while ($row_color_img = $product_color_img_rlt->fetch_assoc()) {
                $product_color_img_data1 = IMG_URL . 'vendor_data/product/' . $row_color_img['image'];
                array_push($product_color_img_data, $product_color_img_data1);
            }
        }
    }
}

$color_img_count = count($product_color_img_data); ?>
<?php if ($color_img_count > 0) { ?>
    <div class="product-details-img-wrap product-details-vertical-wrap" data-aos="fade-up"
         data-aos-delay="200">
        <div class="product-details-small-img-wrap">
            <div class="swiper-container product-details-small-img-slider-1 pd-small-img-style">
                <div class="swiper-wrapper d-flex flex-column">
                    <?php foreach ($product_color_img_data AS $prdt_img) { ?>
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
                <?php foreach ($product_color_img_data AS $prdt_img) { ?>
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
<?php } ?>

<script src="assets/js/main.js"></script>
