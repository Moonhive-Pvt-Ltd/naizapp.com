<?php
include_once '../functions.php';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$post = [
    'vendor_uid' => $vendor_uid,
];
$url = BASE_URL . "get_home_page";
$home_result = getApiData($url, $post);
//print_r($home_result);
//print_r($home_result['status']);
//print_r($home_result['msg']);

if ($home_result['status'] == 'Success') {
//    print_r($home_result['home_page']);
}
?>

<?php if (count($home_result['home_page']['home_main_slider']) > 0) { ?>
    <div class="slider-area">
        <div class="slider-active swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($home_result['home_page']['home_main_slider'] AS $main_slider_row) { ?>
                    <div class="swiper-slide">
                        <div class="intro-section slider-height-1 slider-content-center bg-img single-animation-wrap slider-bg-color-2"
                             style="background-image:url(<?php echo $main_slider_row['image']; ?>)">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 hm2-slider-animation">
                                        <div class="slider-content-2 slider-content-2-wrap slider-animated-2">
                                            <h3 class="animated"><?php echo $main_slider_row['sub_text']; ?></h3>
                                            <h1 class="animated"><?php echo $main_slider_row['main_text']; ?></h1>
                                            <div class="slider-btn-2 btn-hover">
                                                <a href="<?php echo $main_slider_row['button_url']; ?>"
                                                   class="btn hover-border-radius theme-color animated">
                                                    <?php echo $main_slider_row['button_name']; ?>
                                                </a>
                                            </div>
                                            <h2 class="animated">Furniture</h2>
                                            <img class="animated" src="assets/images/icon-img/chair.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="home-slider-prev2 main-slider-nav2"><i class="fa fa-angle-left"></i></div>
                <div class="home-slider-next2 main-slider-nav2"><i class="fa fa-angle-right"></i></div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (count($home_result['home_page']['category']) > 0) { ?>
    <div class="category-area bg-gray-4 pt-95 pb-100">
        <div class="container">
            <div class="section-title-2 st-border-center text-center mb-75" data-aos="fade-up" data-aos-delay="200">
                <h2>Products Category</h2>
            </div>
            <div class="category-slider-active-2 swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($home_result['home_page']['category'] AS $category_row) { ?>
                        <div class="swiper-slide">
                            <div class="single-category-wrap-2 text-center" data-aos="fade-up" data-aos-delay="200">
                                <div class="category-img-2">
                                    <a href="products?c_id=<?php echo $category_row['uid']; ?>">
                                        <img class="category-normal-img"
                                             src="<?php echo $category_row['image']; ?>"
                                             alt="" height="100">
                                        <img class="category-hover-img"
                                             src="<?php echo $category_row['image']; ?>"
                                             alt="icon">
                                    </a>
                                </div>
                                <div class="category-content-2 category-content-2-black">
                                    <h4>
                                        <a href="products?c_id=<?php echo $category_row['uid']; ?>">
                                            <?php echo $category_row['name']; ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    <div class="product-area pt-95 pb-60">
        <div class="container">
            <div class="section-title-tab-wrap mb-75" data-aos="fade-up" data-aos-delay="200">
                <div class="section-title-2">
                    <h2>Hot Products</h2>
                </div>
                <div class="tab-style-1 nav">
                    <a class="active" href="#newLaunches" data-bs-toggle="tab">New Launches </a>
                    <a href="#topPicks" data-bs-toggle="tab" class=""> Top Picks </a>
                    <a href="#mostViewed" data-bs-toggle="tab" class=""> Most Viewed </a>
                    <a href="#mostPopular" data-bs-toggle="tab" class=""> Most Popular </a>
                </div>
            </div>
            <div class="tab-content jump">
                <div id="newLaunches" class="tab-pane active">
                    <div class="row">
                        <?php
                        if (count($home_result['home_page']['new_launches']) > 0) {
                            foreach ($home_result['home_page']['new_launches'] AS $launches_row) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <div class="product-wrap mb-35" data-aos="fade-up" data-aos-delay="200">
                                        <div class="product-img img-zoom mb-25">
                                            <a>
                                                <img src="<?php echo $launches_row['image']; ?>" alt="">
                                            </a>
                                            <div class="product-action-2-wrap">
                                                <a href="product_details?id=<?php echo $launches_row['uid'] ?>">
                                                    <button class="product-action-btn-2" title="More Info">More Info
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a><?php echo $launches_row['name']; ?></a></h3>
                                            <div class="product-price">
                                                <?php if ($launches_row['old_price']) { ?>
                                                    <span class="old-price">₹<?php echo $launches_row['old_price']; ?></span>
                                                <?php } ?>
                                                <span class="new-price">₹<?php echo $launches_row['display_price']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
                <div id="topPicks" class="tab-pane">
                    <div class="row">
                        <?php
                        if (count($home_result['home_page']['top_picks']) > 0) {
                            foreach ($home_result['home_page']['top_picks'] AS $picks_row) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <div class="product-wrap mb-35">
                                        <div class="product-img img-zoom mb-25">
                                            <a>
                                                <img src="<?php echo $picks_row['image']; ?>" alt="">
                                            </a>
                                            <div class="product-action-2-wrap">
                                                <a href="product_details?id=<?php echo $picks_row['uid'] ?>">
                                                    <button class="product-action-btn-2" title="More Info">More Info
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a><?php echo $picks_row['name']; ?></a></h3>
                                            <div class="product-price">
                                                <?php if ($picks_row['old_price']) { ?>
                                                    <span class="old-price">₹<?php echo $picks_row['old_price']; ?></span>
                                                <?php } ?>
                                                <span class="new-price">₹<?php echo $picks_row['display_price']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
                <div id="mostViewed" class="tab-pane">
                    <div class="row">
                        <?php
                        if (count($home_result['home_page']['most_viewed']) > 0) {
                            foreach ($home_result['home_page']['most_viewed'] AS $viewed_row) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <div class="product-wrap mb-35">
                                        <div class="product-img img-zoom mb-25">
                                            <a>
                                                <img src="<?php echo $viewed_row['image']; ?>" alt="">
                                            </a>
                                            <div class="product-action-2-wrap">
                                                <a href="product_details?id=<?php echo $viewed_row['uid'] ?>">
                                                    <button class="product-action-btn-2" title="More Info">More Info
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a><?php echo $viewed_row['name']; ?></a></h3>
                                            <div class="product-price">
                                                <?php if ($viewed_row['old_price']) { ?>
                                                    <span
                                                            class="old-price">₹<?php echo $viewed_row['old_price']; ?></span>
                                                <?php } ?>
                                                <span
                                                        class="new-price">₹<?php echo $viewed_row['display_price']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        }
                        ?>
                    </div>
                </div>
                <div id="mostPopular" class="tab-pane">
                    <div class="row">
                        <?php
                        if (count($home_result['home_page']['most_popular']) > 0) {
                            foreach ($home_result['home_page']['most_popular'] AS $popular_row) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <div class="product-wrap mb-35">
                                        <div class="product-img img-zoom mb-25">
                                            <a>
                                                <img src="<?php echo $popular_row['image']; ?>" alt="">
                                            </a>
                                            <div class="product-action-2-wrap">
                                                <a href="product_details?id=<?php echo $popular_row['uid'] ?>">
                                                    <button class="product-action-btn-2" title="More Info">More Info
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a><?php echo $popular_row['name']; ?></a></h3>
                                            <div class="product-price">
                                                <?php if ($popular_row['old_price']) { ?>
                                                    <span class="old-price">₹<?php echo $popular_row['old_price']; ?></span>
                                                <?php } ?>
                                                <span class="new-price">₹<?php echo $popular_row['display_price']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php if (count($home_result['home_page']['most_category_product']) > 0) { ?>
    <div class="banner-area pb-70">
        <div class="container">
            <div class="row">
                <?php foreach ($home_result['home_page']['most_category_product'] AS $most_category_product_row) { ?>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="banner-wrap mb-30" data-aos="fade-up" data-aos-delay="200">
                            <a><img src="<?php echo $most_category_product_row['image']; ?>" alt="" height="340"></a>
                            <div class="banner-content-5">
                                <h2> <?php echo $most_category_product_row['name']; ?></h2>
                                <div class="btn-style-3 btn-hover">
                                    <a class="btn hover-border-radius"
                                       href="products?c_id=<?php echo $most_category_product_row['uid']; ?>">Shop
                                        Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (count($home_result['home_page']['most_rated']) > 0) { ?>
    <div class="product-area pb-95">
        <div class="container">
            <div class="section-border section-border-margin-1" data-aos="fade-up" data-aos-delay="200">
                <div class="section-title-timer-wrap bg-white">
                    <div class="section-title-1">
                        <h2>Most Rated</h2>
                    </div>
                </div>
            </div>
            <div class="product-slider-active-1 swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($home_result['home_page']['most_rated'] AS $rated_row) { ?>
                        <div class="swiper-slide">
                            <div class="product-wrap">
                                <div class="product-img img-zoom mb-25">
                                    <a>
                                        <img src="<?php echo $rated_row['image']; ?>" alt="" height="300">
                                    </a>
                                    <div class="product-action-2-wrap">
                                        <a href="product_details?id=<?php echo $rated_row['uid'] ?>">
                                            <button class="product-action-btn-2" title="More Info">
                                                More Info
                                            </button>
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content">
                                    <h3><a><?php echo $rated_row['name']; ?></a></h3>
                                    <div class="product-price">
                                        <?php if ($rated_row['old_price']) { ?>
                                            <span class="old-price">₹<?php echo $rated_row['old_price']; ?></span>
                                        <?php } ?>
                                        <span class="new-price">₹<?php echo $rated_row['display_price']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (isset($home_result['home_page']['home_center_slider'])) {
    $center_slider = $home_result['home_page']['home_center_slider']; ?>
    <div class="banner-area pb-100">
        <div class="bg-img bg-padding-2" style="background-image:url(<?php echo $center_slider['image']; ?>)">
            <div class="container">
                <div class="banner-content-5 banner-content-5-static">
                    <span data-aos="fade-up" data-aos-delay="200"><?php echo $center_slider['sub_text']; ?></span>
                    <h1 data-aos="fade-up" data-aos-delay="400"><?php echo $center_slider['main_text']; ?></h1>
                    <div class="btn-style-3 btn-hover" data-aos="fade-up" data-aos-delay="600">
                        <a class="btn hover-border-radius"
                           href="<?php echo $center_slider['button_url']; ?>"><?php echo $center_slider['button_name']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php include_once '../footer_bottom.php'; ?>
<?php include_once '../sidebar.php'; ?>
