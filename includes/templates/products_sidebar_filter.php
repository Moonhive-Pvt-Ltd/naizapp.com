<?php
$cat_uid = isset($_GET['c_id']) ? $_GET['c_id'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
$post = [
  'vendor_uid' => $vendor_uid,
];
$url = BASE_URL . "get_category_tag_list";
$result = getApiData($url, $post);

$category_list = [];
$tag_list = [];

if ($result['status'] == 'Success') {
    $category_list = $result['data']['category'];
    $tag_list = $result['data']['tag'];
}
?>
<div class="col-lg-3">
    <div class="sidebar-wrapper">
        <div class="sidebar-widget mb-40" data-aos="fade-up" data-aos-delay="200">
            <div class="search-wrap-2">
                <div class="search-2-form" action="#">
                    <input placeholder="Search*" type="text" class="search-product-input">
                    <div class="button-search"><i class=" ti-search "></i></div>
                </div>
            </div>
        </div>
        <div class="sidebar-widget sidebar-widget-border mb-40 pb-35" data-aos="fade-up" data-aos-delay="200">
            <div class="sidebar-widget-title mb-30">
                <h3>Filter By Price</h3>
            </div>
            <div class="price-filter">
                <div id="slider-range"></div>
                <div class="price-slider-amount">
                    <div class="label-input">
                        <label>Price:</label>
                        <input type="text" id="amount" name="price" placeholder="Add Your Price"/>
                    </div>
                    <button type="button" class="filter-btn">Filter</button>
                </div>
            </div>
        </div>
        <div class="sidebar-widget sidebar-widget-border mb-40 pb-35" data-aos="fade-up" data-aos-delay="200">
            <div class="sidebar-widget-title mb-25">
                <h3>Product Categories</h3>
            </div>
            <div class="sidebar-list-style">
                <ul class="category-filter-list">
                    <li cat-id="" class="<?php echo $cat_uid == '' ? 'active' : ''; ?>"><a href="#">All</a></li>
                    <?php foreach ($category_list AS $cat) { ?>
                        <li cat-id="<?php echo $cat['id']; ?>"
                            class="<?php echo $cat_uid == $cat['uid'] ? 'active' : ''; ?>">
                            <a href="#"><?php echo $cat['name']; ?>
                                <span><?php echo $cat['count']; ?> </span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="sidebar-widget" data-aos="fade-up" data-aos-delay="200">
            <div class="sidebar-widget-title mb-25">
                <h3>Tags</h3>
            </div>
            <div class="sidebar-widget-tag tag-filter-list">
                <a tag-id="" href="#" class="active">All, </a>
                <?php $ti = 1;
                foreach ($tag_list AS $tag) { ?>
                    <a href="#" tag-id="<?php echo $tag['id']; ?>">
                        <?php echo $tag['name'];
                        echo count($tag_list) == $ti ? '' : ',' ?>
                    </a>
                    <?php $ti++;
                } ?>
            </div>
        </div>
    </div>
</div>

