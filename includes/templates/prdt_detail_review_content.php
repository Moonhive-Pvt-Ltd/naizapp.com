<?php
require_once("../functions.php");

$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$product_uid = isset($_GET['product_uid']) ? $_GET['product_uid'] : '';
$total_pages = 0;
$post = [
  'page' => $pagenum,
  'product_uid' => $product_uid,
];
$url = BASE_URL . "get_product_review";
$result = getApiData($url, $post);
$prdt_review = [];
if ($result['status'] == 'Success') {
    $total_pages = $result['total_pages'];
    $prdt_review = $result['review']['product_review'];
}
?>

<?php foreach ($prdt_review AS $review) { ?>
    <div class="single-review">
        <div class="review-img">
            <img src="assets/images/product-details/review-1.png" alt="">
        </div>
        <div class="review-content">
            <div class="review-rating">
                <?php for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $review['rating']) { ?>
                        <span><i class="ti-star star-active"></i></span>
                    <?php } else { ?>
                        <span><i class="ti-star"></i></span>
                    <?php }
                } ?>
            </div>
            <h5><span><?php echo $review['name'] ?></span> - <?php echo $review['date']; ?></h5>
            <p><?php echo $review['review'] ?></p>
        </div>
    </div>
<?php } ?>

<footer class="pagination-footer d-flex justify-content-end" type-id="prdtDetailReviewTableContent"
        scroll-id="prdtDetailReviewTableContent">
    <?php include 'pagination.php'; ?>
</footer>
