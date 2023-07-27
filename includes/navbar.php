<?php include_once 'modal.php';
$page = basename($_SERVER['PHP_SELF']);
?>
<header class="header-area header-responsive-padding header-height-1" id="navBarId">
    <div class="header-top d-none d-lg-block bg-gray">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-6">
                    <div class="welcome-text">
                        <p>Welcome to Naiz! </p>
                    </div>
                </div>
                <div class="col-lg-6 col-6">
                    <div style="height: 51px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom sticky-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="logo">
                        <a href="index"><img src="assets/images/logo.jpeg" alt="logo" height="70"></a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block d-flex justify-content-center">
                    <div class="main-menu text-center">
                        <nav>
                            <ul>
                                <li>
                                    <a href="index" class="<?php if ($page == 'index.php') {
                                        echo "selected-main-header-menu";
                                    } else {
                                        echo "";
                                    } ?>">HOME</a>
                                </li>
                                <li>
                                    <a href="shop" class="<?php if ($page == 'shop.php') {
                                        echo "selected-main-header-menu";
                                    } else {
                                        echo "";
                                    } ?>">SHOP</a>
                                </li>
                                <li>
                                    <a href="products" class="<?php if ($page == 'products.php') {
                                        echo "selected-main-header-menu";
                                    } else {
                                        echo "";
                                    } ?>">PRODUCTS</a>
                                </li>
                                <li><a href="about_us" class="<?php if ($page == 'about_us.php') {
                                        echo "selected-main-header-menu";
                                    } else {
                                        echo "";
                                    } ?>">ABOUT</a></li>
                                <li><a href="contact_us" class="<?php if ($page == 'contact_us.php') {
                                        echo "selected-main-header-menu";
                                    } else {
                                        echo "";
                                    } ?>">CONTACT US</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="header-action-wrap">
                        <div class="header-action-style">
                            <a title="Account" href="account"><i class="pe-7s-user"></i></a>
                        </div>

                        <div id="cartCountContent"></div>

                        <div class="header-action-style d-block d-lg-none">
                            <a class="mobile-menu-active-button" href="#"><i class="pe-7s-menu"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
