<?php
require_once("../functions.php");
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$category_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : '';
$tag_id = isset($_GET['tag_id']) ? $_GET['tag_id'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$view_mode = isset($_GET['view_mode']) ? $_GET['view_mode'] : 'shop1';
$price_start = isset($_GET['price_start']) ? $_GET['price_start'] : '';
$price_end = isset($_GET['price_end']) ? $_GET['price_end'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

$total_pages = 0;
$limit = $view_mode == 'shop1' ? 15 : 10;
$total_count = 0;

$post = [
  'page' => $pagenum,
  'limit' => $limit,
  'vendor_uid' => $vendor_uid,
  'category_id' => $category_id,
  'search' => $search,
  'tag_id' => $tag_id,
  'price_start' => $price_start,
  'price_end' => $price_end,
  'sort_by' => $sort_by,
];
$url = BASE_URL . "get_product_list";
$result = getApiData($url, $post);
$prdt_list = [];

if ($result['status'] == 'Success') {
    $prdt_list = $result['products'];
    $total_pages = $result['total_pages'];
    $total_count = $result['total_count'];
}
?>
<input type="hidden" value="<?php echo $limit; ?>" id="limit">
<input type="hidden" value="<?php echo $total_count; ?>" id="totalCount">
<input type="hidden" value="<?php echo count($prdt_list); ?>" id="listLength">
<?php if ($view_mode == 'shop1') { ?>
    <div class="row">
        <?php foreach ($prdt_list as $prdt_item) { ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <div class="product-wrap mb-35" data-aos="fade-up" data-aos-delay="200">
                    <div class="product-img img-zoom mb-25">
                        <a>
                            <img src="<?php echo $prdt_item['image'] ?>" alt="">
                        </a>
                        <div class="product-action-2-wrap">
                            <a href="product_details?id=<?php echo $prdt_item['uid'] ?>">
                                <button class="product-action-btn-2">More Info</button>
                            </a>
                        </div>
                    </div>
                    <div class="product-content">
                        <h3><a href="#"><?php echo $prdt_item['name']; ?></a></h3>
                        <div class="product-price">
                            <?php if ($prdt_item['offer_price'] > 0) { ?>
                                <span class="old-price">₹<?php echo $prdt_item['price']; ?></span>
                                <span class="new-price">₹<?php echo $prdt_item['offer_price']; ?></span>
                            <?php } else { ?>
                                <span class="new-price">₹<?php echo $prdt_item['price']; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else {
    foreach ($prdt_list as $prdt_item) { ?>
        <div class="shop-list-wrap mb-30">
            <div class="row">
                <div class="col-lg-4 col-sm-5">
                    <div class="product-list-img">
                        <a>
                            <img src="<?php echo $prdt_item['image'] ?>" alt="">
                        </a>
                        <div class="product-list-quickview">
                            <a href="product_details?id=<?php echo $prdt_item['uid']; ?>">
                                <button class="product-action-btn-2" title="Quick View"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    <i class="pe-7s-look"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-sm-7">
                    <div class="shop-list-content">
                        <h3><a href="product_details?id=<?php echo $prdt_item['uid']; ?>"><?php echo $prdt_item['name']; ?></a></h3>
                        <div class="product-price">
                            <?php if ($prdt_item['offer_price'] > 0) { ?>
                                <span class="old-price">₹<?php echo $prdt_item['price']; ?></span>
                                <span class="new-price">₹<?php echo $prdt_item['offer_price']; ?></span>
                            <?php } else { ?>
                                <span class="new-price">₹<?php echo $prdt_item['price']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="product-rating">
                            <?php for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $prdt_item['rating']) { ?>
                                    <i class="ti-star star-active"></i>
                                <?php } else { ?>
                                    <i class="ti-star"></i>
                                <?php }
                            } ?>
                        </div>
                        <p><?php echo $prdt_item['description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>

<footer class="pagination-footer d-flex justify-content-center" type-id="prdtListItemDiv"
        scroll-id="prdtListItemDiv">
    <?php include 'pagination.php'; ?>
</footer>