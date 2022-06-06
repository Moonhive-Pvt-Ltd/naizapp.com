<?php
$page = basename($_SERVER['PHP_SELF']);
$title = 'Web';

switch ($page) {
    case 'login.php':
        $title = 'Login';
        break;
    case 'index.php':
        $title = 'Home';
        break;
    case 'shop.php':
        $title = 'Shop';
        break;
    case 'cart.php':
        $title = 'Cart';
        break;
    case 'checkout.php':
        $title = 'Checkout';
        break;
    case 'order_history.php':
        $title = 'Order History';
        break;
    case 'account.php':
        $title = 'My Account';
        break;
    case 'about_us.php':
        $title = 'About Us';
        break;
    case 'contact_us.php':
        $title = 'Contact Us';
        break;
    case 'products.php':
        $title = 'Products';
        break;
    case 'product_details.php':
        $title = 'Product Details';
        break;
    case 'forgot_password.php':
        $title = 'Forgot Password';
        break;
    case 'reset_password.php':
        $title = 'Reset Password';
        break;
    case 'terms_and_condition.php':
        $title = 'Terms & Conditions';
        break;
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Naiz - <?php echo $title; ?></title>
    <meta name="robots" content="noindex, follow"/>
    <meta name="description"
          content="Naiz is a delivery platform for Aluminum and related construction products.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="canonical" href="https://naizapp.com/index"/>

    <!-- Open Graph (OG) meta tags are snippets of code that control how URLs are displayed when shared on social media  -->
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Naiz"/>
    <meta property="og:url" content="https://naizapp.com/index"/>
    <meta property="og:site_name" content="Naiz"/>
    <!-- For the og:image content, replace the # with a link of an image -->
    <meta property="og:image" content="https://naizapp.com/assets/images/logo.jpeg"/>
    <meta property="og:description"
          content="Naiz is a delivery platform for Aluminum and related construction products."/>
    <!-- Add site Favicon -->
    <link rel="icon" href="assets/images/favicon/cropped-favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" href="assets/images/favicon/cropped-favicon-192x192.png" sizes="192x192"/>
    <link rel="apple-touch-icon" href="assets/images/favicon/cropped-favicon-180x180.png"/>
    <meta name="msapplication-TileImage" content="assets/images/favicon/cropped-favicon-270x270.png"/>

    <!-- All CSS is here
	============================================ -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css"/>
    <link rel="stylesheet" href="assets/css/vendor/pe-icon-7-stroke.css"/>
    <link rel="stylesheet" href="assets/css/vendor/themify-icons.css"/>
    <link rel="stylesheet" href="assets/css/vendor/font-awesome.min.css"/>
    <link rel="stylesheet" href="assets/css/plugins/animate.css"/>
    <link rel="stylesheet" href="assets/css/plugins/aos.css"/>
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup.css"/>
    <link rel="stylesheet" href="assets/css/plugins/swiper.min.css"/>
    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.css"/>
    <link rel="stylesheet" href="assets/css/plugins/nice-select.css"/>
    <link rel="stylesheet" href="assets/css/plugins/select2.min.css"/>
    <link rel="stylesheet" href="assets/css/plugins/easyzoom.css"/>
    <link rel="stylesheet" href="assets/css/plugins/slinky.css"/>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link rel="stylesheet" href="assets/libs/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="assets/css/custom.css?ver=<?php echo rand(); ?>"/>
</head>

