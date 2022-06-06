<?php
require_once("../functions.php");
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$vendor_uid = isset($_COOKIE['naiz_web_vendor_uid']) ? $_COOKIE['naiz_web_vendor_uid'] : '';

$post = [
    'uid' => $user_uid,
    'vendor_uid' => $vendor_uid,
];
$url = BASE_URL . "get_vendor_pincode";
$result = getApiData($url, $post);
if ($result['status'] == 'Success') {
//    print_r($result['pincode']);
}

$address_id = $_GET['address_id'];
$address = mysqli_query($mysqli, "SELECT * FROM user_address WHERE id = '$address_id'");

if (mysqli_num_rows($address)) {
    $row = mysqli_fetch_array($address); ?>
    <div class="billing-info-wrap account-details-form">
        <form id="updateUserAddressForm" method="post">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="billing-info mb-20">
                        <label>Full Name <abbr class="required"
                                               title="required">*</abbr></label>
                        <input type="text" name="full_name"
                               value="<?php echo $row['full_name']; ?>" required>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="billing-info mb-20">
                        <label>Phone <abbr class="required"
                                           title="required">*</abbr></label>
                        <input type="text" name="phone_number"
                               value="<?php echo $row['phone_number']; ?>" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="billing-info mb-20">
                        <label>Address <abbr class="required"
                                             title="required">*</abbr></label>
                        <input type="text" name="address"
                               value="<?php echo $row['address']; ?>" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="billing-info mb-20">
                        <label>City <abbr class="required"
                                          title="required">*</abbr></label>
                        <input type="text" name="city_district"
                               value="<?php echo $row['city_district']; ?>"
                               required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="edit-address-select-style mb-20">
                        <label>Postcode / ZIP <abbr class="required"
                                                    title="required">*</abbr></label>
                        <select class="edit-address-select-two-active" name="zip" required>
                            <option value="<?php echo $row['zip']; ?>" hidden><?php echo $row['zip']; ?></option>
                            <?php foreach ($result['pincode'] as $pincode_val) { ?>
                                <option value="<?php echo $pincode_val; ?>"><?php echo $pincode_val; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="edit-address-select-style mb-20">
                        <label>House / Company <abbr class="required"
                                                     title="required">*</abbr></label>
                        <select class="edit-address-select-two-active" name="type" required>
                            <option value="" hidden></option>
                            <option value="house" <?php if ($row['type'] == 'house') {
                                echo 'selected';
                            } ?>>House / Apartment
                            </option>
                            <option value="company" <?php if ($row['type'] == 'company') {
                                echo 'selected';
                            } ?>>Agency / Company
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="single-input-item btn-hover">
                <input type="hidden" name="address_id"
                       value="<?php echo $address_id; ?>">
                <input type="hidden" name="uid"
                       value="<?php echo $user_uid; ?>">
                <button type="submit" class="update-user-address-form">Update</button>
            </div>
        </form>
    </div>
<?php } else {
    echo("<script>location.href = './login_register';</script>");
} ?>