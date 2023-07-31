<?php include_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="zxx">
<?php include_once 'includes/header.php'; ?>
<body>
<div class="main-wrapper main-wrapper-2">
    <?php include_once 'includes/navbar.php'; ?>
    <!-- mini cart start -->
    <?php
    $vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';
    if ($vendor_uid != '') {
        $title = 'Products';
        include_once 'includes/templates/header_content.php'; ?>
        <div class="shop-area shop-page-responsive pt-100 pb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="shop-topbar-wrapper mb-40">
                            <div class="shop-topbar-left">
                                <div class="showing-item">
                                    <span></span>
                                </div>
                            </div>
                            <div class="shop-topbar-right">
                                <div class="shop-sorting-area">
                                    <select class="nice-select nice-select-style-1 shop-sorting-select">
                                        <option value="avg_rating">Sort by average rating</option>
                                        <option value="latest" selected>Sort by latest</option>
                                    </select>
                                </div>
                                <div class="shop-view-mode nav">
                                    <a class="active" view-mode="shop1" href="#shop-1" data-bs-toggle="tab">
                                        <i class=" ti-layout-grid3 "></i>
                                    </a>
                                    <a href="#shop-2" view-mode="shop2" data-bs-toggle="tab" class="">
                                        <i class=" ti-view-list-alt "></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="shop-bottom-area">
                            <div class="tab-content jump">
                                <div id="shop-1" class="tab-pane active">
                                    <div id="prdtListItemDiv"></div>
                                </div>
                                <div id="shop-2" class="tab-pane">
                                    <div id="prdtListItemDiv1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include_once 'includes/templates/products_sidebar_filter.php'; ?>
                </div>
            </div>
        </div>
        <?php include_once 'includes/footer_bottom.php'; ?>
        <?php include_once 'includes/sidebar.php';
    } ?>
    <div id="menuNavDiv"></div>
</div>
<!-- All JS is here -->
<?php include_once 'includes/footer.php'; ?>
</body>
</html>