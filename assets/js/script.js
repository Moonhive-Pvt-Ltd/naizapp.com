$(document).ready(function () {
    let BASE_URL = 'https://naizapp.com/api/v1/user_web/';

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $(document).on('submit', '#userLoginForm', function (e) {
        e.preventDefault();
        let btn = $('#userLoginForm .login-form-btn');
        let type = $('#userLoginForm').attr('type-id');
        let form_data = new FormData(this);
        let password = $('#password').val();
        let email = $('#email').val();
        let pswd = hex_sha512(password);
        $(btn).prop('disabled', true);
        $(btn).html('Please Wait');
        let url = BASE_URL + "user_login";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $(btn).prop('disabled', false);
                $(btn).html('Login');
                if (data.status == 'Success') {
                    setCookie('naiz_web_user_uid', data.user.uid);
                    setCookie('naiz_web_password', pswd);
                    setCookie('naiz_web_email', email);
                    if (type == 'add_to_cart') {
                        const modal_id = document.querySelector('#modalDivId');
                        const modal = bootstrap.Modal.getInstance(modal_id);
                        modal.hide();
                        getPrdtSizeDetail()
                    } else if (type == 'review_login') {
                        getPrdtDetailAddReviewContent();
                    } else {
                        location.href = './account';
                    }
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('submit', '#userRegisterForm', function (e) {
        e.preventDefault();
        let btn = $('#userRegisterForm .register-form-btn');
        let form_data = new FormData(this);
        let url = BASE_URL + "user_register";
        $(btn).prop('disabled', true);
        $(btn).html('Please Wait');
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $(btn).prop('disabled', false);
                $(btn).html('Register');
                if (data.status == 'Success') {
                    Swal.fire({
                        title: '<div class="mt-4"><h5>Registered Successfully</h5></div>',
                        allowOutsideClick: false,
                        showCancelButton: false,
                        width: 400,
                        padding: 8,
                        confirmButtonText: `Ok`,
                        confirmButtonColor: "#e97730",
                    }).then((result) => {
                        if (result.value) {
                            location.href = './login_register';
                        }
                    });
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    if (document.getElementById('myaccountContent')) {
        let type = '#dashboad';
        var hash = window.location.hash.substr(1);

        if (hash == 'order') {
            type = '#orders';
            $('.myaccount-tab-menu').find('a').removeClass('active');
            $('.myaccount-tab-menu').find('.order-tab').addClass('active');
            $('#myaccountContent').find('.tab-pane').removeClass('active show');
            $('#myaccountContent').find('#orders').addClass('active show');
        }
        getAccountTabContent(type, 1);
    }

    $(document).on("click", ".footer-bottom-order-history", function () {
        let type = '#dashboad';
        window.location.href = "account#order";
        var hash = window.location.hash.substr(1);
        if (hash == 'order') {
            type = '#orders';
            $('.myaccount-tab-menu').find('a').removeClass('active');
            $('.myaccount-tab-menu').find('.order-tab').addClass('active');
            $('#myaccountContent').find('.tab-pane').removeClass('active show');
            $('#myaccountContent').find('#orders').addClass('active show');
        }
        getAccountTabContent(type, 1);
    });

    $(document).on("click", ".myaccount-tab-menu a", function () {
        let type = $(this).attr("href");
        getAccountTabContent(type, 1);
    });

    $(document).on('submit', '#updateUserDetailForm', function (e) {
        e.preventDefault();
        let email = $('#email').val();
        let form_data = new FormData(this);
        let url = BASE_URL + "update_user_profile";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'Success') {
                    setCookie('naiz_web_email', email);
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('submit', '#updateUserPassword', function (e) {
        e.preventDefault();
        let new_pwd = $('#new-pwd').val();
        let confirm_pwd = $('#confirm-pwd').val();
        let pswd = hex_sha512(new_pwd);

        if (new_pwd != confirm_pwd) {
            Swal.fire({text: 'Password Mismatch', confirmButtonColor: "#e97730"});
            return;
        }

        let form_data = new FormData(this);
        let url = BASE_URL + "change_password";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'Success') {
                    setCookie('naiz_web_password', pswd);
                    $('#new-pwd').val('');
                    $('#confirm-pwd').val('');
                    $('#current-pwd').val('');
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('submit', '#addUserBillingAddressForm', function (e) {
        e.preventDefault();
        let form_data = new FormData(this);
        let url = BASE_URL + "add_user_address";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'Success') {
                    location.href = './checkout';
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('change keyup', '.cart-plus-minus-box', function (e) {
        var btn = this;
        let uid = getCookie('naiz_web_user_uid');
        let val = $(this).val();

        if (!val.match(/^[0-9]+$/) && val != '') {
            $(this).val(val.replace(/[^0-9]/g, ''));
            return;
        }

        let product_uid = $(this).attr('product-uid');
        let product_size_id = $(this).attr('product-size-id');
        let color_id = $(this).attr('color-id');
        let warranty_id = $(this).attr('warranty-id');
        let design_id = $(this).attr('design-id');
        let stock = $(this).attr('stock');
        let current_val = $(this).attr('current-val');
        let cart_id = $(this).attr('cart-id');
        let user_id = $('#userId').val();
        let vendor_id = $('#vendorId').val();
        let vendor_uid = $('#vendorUid').val();

        let btn_sign = $(this).attr('btn-sign');
        btn_sign = typeof btn_sign == 'undefined' ? '' : btn_sign;
        $(this).attr('btn-sign', '');

        var ajaxurl = 'includes/functions.php',
            data = {
                'action': 'getCartSubTotalCount',
                'user_id': user_id,
                'vendor_id': vendor_id,
                'product_size_id': product_size_id,
                'color_id': color_id,
            };
        $.post(ajaxurl, data, function (response) {
            if (stock != 'unlimited') {
                if (val != '') {
                    if (warranty_id == '' && design_id == '') {
                    } else {
                        let total_value = parseInt(val) + parseInt(response);

                        if (total_value <= parseInt(stock)) {
                            $('.no-stock-available-td-' + cart_id).remove();
                        }

                        if (total_value > parseInt(stock)) {
                            if (btn_sign == '+' || btn_sign == '') {
                                if (stock == 0) {
                                    Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                                } else {
                                    Swal.fire({
                                        text: 'Total ' + stock + ' is available',
                                        confirmButtonColor: "#e97730"
                                    });
                                }

                                if (btn_sign == '+') {
                                    $(btn).val(parseInt(val) - 1);
                                    $(btn).attr('current-val', parseInt(val) - 1);
                                } else {
                                    $(btn).val(current_val);
                                }
                                return;
                            }
                        }
                    }
                }

                if (parseInt(val) > parseInt(stock)) {
                    if (btn_sign == '+' || btn_sign == '') {
                        if (stock == 0) {
                            Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                        } else {
                            Swal.fire({text: 'Only ' + stock + ' is available', confirmButtonColor: "#e97730"});
                        }

                        if (btn_sign == '+') {
                            $(btn).val(parseInt(val) - 1);
                            $(btn).attr('current-val', parseInt(val) - 1);
                        } else {
                            $(btn).val(current_val);
                        }
                        return;
                    }
                }
            }

            $(btn).closest('tr').find('.stock-count').val(val);
            $(btn).attr('current-val', val);

            let total_price_cost = 0;
            let cart_tr = $('.cart-tr');

            let cart_all_check_length = $(cart_tr).find('.cart-checkbox-input').is(':checked');

            if (cart_all_check_length) {
                let cart_check_length = $(btn).closest('tr').find('.cart-checkbox-input').is(':checked');

                if (cart_check_length) {
                    for (var i = 0; i < cart_tr.length; i++) {
                        if ($(cart_tr[i]).find('.cart-checkbox-input').is(':checked')) {
                            let stock_count = $(cart_tr[i]).find('.stock-count').val();
                            let display_price = $(cart_tr[i]).find('.stock-price').val();
                            total_price_cost = parseInt(total_price_cost) + parseInt(stock_count * display_price);
                            $(cart_tr[i]).find('.total-display-price-amount').html(stock_count * display_price);
                        }
                    }
                } else {
                    for (var i = 0; i < cart_tr.length; i++) {
                        let stock_count = $(cart_tr[i]).find('.stock-count').val();
                        let display_price = $(cart_tr[i]).find('.stock-price').val();
                        $(cart_tr[i]).find('.total-display-price-amount').html(stock_count * display_price);
                        total_price_cost = $('.total-price-cost').html();
                    }
                }
            } else {
                for (var i = 0; i < cart_tr.length; i++) {
                    let stock_count = $(cart_tr[i]).find('.stock-count').val();
                    let display_price = $(cart_tr[i]).find('.stock-price').val();
                    total_price_cost = parseInt(total_price_cost) + parseInt(stock_count * display_price);
                    $(cart_tr[i]).find('.total-display-price-amount').html(stock_count * display_price);
                }
            }

            $('.total-price-cost').html(total_price_cost);

            let count_value = val;
            if (val == '') {
                count_value = 0;
            }

            let form_data = new FormData();
            form_data.append('uid', uid);
            form_data.append('page', 'change_cart');
            form_data.append('count', count_value);
            form_data.append('product_uid', product_uid);
            form_data.append('vendor_uid', vendor_uid);
            form_data.append('product_size_id', product_size_id);
            form_data.append('color_id', color_id);
            form_data.append('warranty_id', warranty_id);
            form_data.append('design_id', design_id);
            let url = BASE_URL + "add_to_cart";
            $.ajax({
                url: url,
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == 'Success') {
                    } else {
                        Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                    }
                }
            });
        })
    });

    $(document).on('click', '.prdt-detail-add-to-cart-btn', function (e) {
        e.preventDefault();
        let uid = getCookie('naiz_web_user_uid');

        if (uid == '') {
            var myModal = new bootstrap.Modal(document.getElementById('modalDivId'));
            myModal.show();
            modalContent('login', '');
            return;
        }

        let btn = $(this);
        let btn_type = $(this).find('a').attr('type');

        if (btn_type == 'add_to_cart') {
            let val = $('.count-val').val();
            if (!val.match(/^[0-9]+$/) && val != '') {
                Swal.fire({text: 'Please enter a valid stock', confirmButtonColor: "#5e72e4"});
                return;
            }

            if (val == '' || val == 0) {
                Swal.fire({text: 'Atleast 1 is required', confirmButtonColor: "#e97730"});
                return;
            }


            let color_check = $('#colorCheck').val();
            let current_count = 0;
            let total_stock_count = 0;
            let product_uid = $('#productUid').val();
            let vendor_uid = $('#vendorUId').val();
            let product_size_id = $('#prdtSizeId').val();
            let vendor_id = $('#vendorId').val();
            let user_id = $('#userId').val();
            let design_id = $('.prdt-detail-design-select').val() ? $('.prdt-detail-design-select').val() : '';
            let color_id = '';
            let warranty_id = '';
            let warranty_check = 0;
            let remaining_warranty_count = 0;

            if (color_check == 0) {
                current_count = $('#currentCount').val();
                total_stock_count = $('#totalStockCount').val();
            } else {
                total_stock_count = $('.prdt-color-select.active').attr('color-stock');
                color_id = $('.prdt-color-select.active').attr('color-id');
                warranty_check = $('.prdt-color-select.active').attr('warranty-check');
                if (warranty_check == 1) {
                    current_count = $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id');
                    warranty_id = $('.prdt-detail-color-warranty-select').find(':selected').attr('warranty-id');
                } else {
                    current_count = $('.prdt-color-select.active').attr('color-count');
                }
            }

            var ajaxurl = 'includes/functions.php',
                data = {
                    'action': 'getCartSubTotalCount',
                    'user_id': user_id,
                    'vendor_id': vendor_id,
                    'product_size_id': product_size_id,
                    'color_id': color_id,
                };
            $.post(ajaxurl, data, function (response) {
                if (total_stock_count != 'unlimited') {
                    if (response > 0) {
                        remaining_warranty_count = parseInt(response) - parseInt(current_count);
                    }

                    if ((parseInt(val) + parseInt(remaining_warranty_count)) > parseInt(total_stock_count)) {
                        if (total_stock_count == 0) {
                            Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                            return;
                        } else {
                            if (warranty_check == 1) {
                                let current_value = $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id');
                                if (val >= 1 && val < current_value) {
                                    // if (val > 1 && val < current_value) {

                                } else {
                                    Swal.fire({
                                        text: 'Total ' + total_stock_count + ' is available',
                                        confirmButtonColor: "#e97730"
                                    });
                                    return;
                                }
                            } else {
                                Swal.fire({
                                    text: 'Only ' + total_stock_count + ' is available',
                                    confirmButtonColor: "#e97730"
                                });
                                return;
                            }
                        }
                    }
                }

                let form_data = new FormData();
                form_data.append('uid', uid);
                form_data.append('page', 'product_detail');
                form_data.append('count', val);
                form_data.append('product_uid', product_uid);
                form_data.append('vendor_uid', vendor_uid);
                form_data.append('product_size_id', product_size_id);
                form_data.append('color_id', color_id);
                form_data.append('warranty_id', warranty_id);
                form_data.append('design_id', design_id);

                let url = BASE_URL + "add_to_cart";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        if (data.status == 'Success') {
                            btn.find('.cart-btn').html('Go to Cart');
                            btn.find('.cart-btn').attr("type", "go_to_cart");
                            if (color_check == 0) {
                                $('#currentCount').val(val);
                            } else {
                                $('.prdt-color-select.active').attr('color-count', val);
                                if (warranty_check == 1) {
                                    $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id', val);
                                }
                            }
                            prdtDetailAddToCartCountBtn(total_stock_count, val);
                            getCartCountContent();
                        } else {
                            Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                        }
                    }
                });
            })

        } else if (btn_type == 'go_to_cart') {
            window.location.href = "cart";
        }
    });

    $(document).on('change keyup', '.cart-plus-minus-box-prdt-detail', function () {
        var btn = this;
        let color_check = $('#colorCheck').val();
        let count = $(this).val();
        let vendor_id = $('#vendorId').val();
        let user_id = $('#userId').val();
        let product_size_id = $('#prdtSizeId').val();

        if (!count.match(/^[0-9]+$/) && count != '') {
            Swal.fire({text: 'Please enter a valid stock', confirmButtonColor: "#5e72e4"});
            return;
        }
        if (count == '') {
            count = 0;
        }
        let btn_type = $('.cart-btn').attr('type');
        let btn_sign = $(this).attr('btn-sign');
        if (typeof btn_sign == 'undefined') {
            btn_sign = '';
        }

        let remaining_warranty_count = 0;
        let current_count = 0;
        let total_stock_count = 0;
        let warranty_check = 0;
        let color_id = '';
        let design_id = $('.prdt-detail-design-select').val() ? $('.prdt-detail-design-select').val() : '';
        if (color_check == 0) {
            current_count = $('#currentCount').val();
            total_stock_count = $('#totalStockCount').val();
        } else {
            total_stock_count = $('.prdt-color-select.active').attr('color-stock');
            color_id = $('.prdt-color-select.active').attr('color-id');
            warranty_check = $('.prdt-color-select.active').attr('warranty-check');
            if (warranty_check == 1) {
                current_count = $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id');
            } else {
                current_count = $('.prdt-color-select.active').attr('color-count');
            }
        }

        var ajaxurl = 'includes/functions.php',
            data = {
                'action': 'getCartSubTotalCount',
                'user_id': user_id,
                'vendor_id': vendor_id,
                'product_size_id': product_size_id,
                'color_id': color_id,
            };
        $.post(ajaxurl, data, function (response) {
            if (response > 0) {
                remaining_warranty_count = parseInt(response) - parseInt(current_count);
            }

            if (btn_type == 'out_of_stock') {
                let input_val = 0;
                if (btn_sign == '+') {
                    Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                    input_val = parseInt(count) - 1;
                } else if (btn_sign == '-') {
                    input_val = 0;
                } else if (btn_sign == '') {
                    Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                    input_val = current_count;
                }

                $('.cart-plus-minus-box-prdt-detail').val(input_val);
            } else {
                let total_stock_count1 = total_stock_count;
                if (total_stock_count == 'unlimited') {
                    total_stock_count1 = 0;
                }
                if (((parseInt(count) + parseInt(remaining_warranty_count)) <= parseInt(total_stock_count1)) || (total_stock_count == 'unlimited')) {
                    if (count == current_count && (count != 0 && current_count != 0)) {
                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').html('Go to Cart');
                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').attr("type", "go_to_cart");
                    } else {
                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').html('Add to Cart');
                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').attr("type", "add_to_cart");
                    }
                } else {
                    if ((parseInt(count) + parseInt(remaining_warranty_count)) > parseInt(total_stock_count)) {
                        if (btn_sign == '+' || btn_sign == '') {
                            if (total_stock_count == 0) {
                                Swal.fire({text: 'No stock is available', confirmButtonColor: "#e97730"});
                            } else {
                                // if (warranty_check && btn_sign == '') {
                                if ((warranty_check == 1 && (btn_sign == '' || btn_sign == '+')) || (design_id && (btn_sign == '' || btn_sign == '+'))) {
                                    if (count < current_count && count < 1) {
                                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').html('Add to Cart');
                                        $(btn).closest('.product-details-action-wrap').find('.cart-btn').attr("type", "add_to_cart");
                                    } else {
                                        Swal.fire({
                                            text: 'Total ' + total_stock_count + ' is available',
                                            confirmButtonColor: "#e97730"
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        text: 'Only ' + total_stock_count + ' is available',
                                        confirmButtonColor: "#e97730"
                                    });
                                }
                            }
                            if (btn_sign == '+') {
                                $('.cart-plus-minus-box-prdt-detail').val(parseInt(count) - 1);
                            }
                        } else if (btn_sign == '-') {
                            if ((warranty_check == 1 && count >= 1) || (design_id && count >= 1)) {
                                $(btn).closest('.product-details-action-wrap').find('.cart-btn').html('Add to Cart');
                                $(btn).closest('.product-details-action-wrap').find('.cart-btn').attr("type", "add_to_cart");
                            }
                        }
                    }
                }
            }
            $(btn).attr('btn-sign', '');
        })
    });

    $(document).on('click', '.prdt-color-select', function () {
        let btn = this;
        let warranty_check = $(btn).attr("warranty-check");
        if (warranty_check == 1) {
            getColorWarrantList(btn);
        } else {
            getColorCartCount(btn, '');
        }
    });

    $(document).on('submit', '#appliedPromoCode', function (e) {
        e.preventDefault();
        let form_data = new FormData(this);
        let url = BASE_URL + "applied_promo_code";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'Success') {
                    $('#applyCouponBtn').attr('disabled', 'disabled');
                    $('#applyCouponBtn').val('Applied');
                    $('.flat-rate').html(data.flat_rate);
                    var sub_total = $('.sub-total').html();
                    var flat_rate = $('.flat-rate').html();
                    var shipping_fee = $('.shipping-fee').html();
                    var tax_val = $('#taxValue').val();
                    var total_cost = (parseFloat(sub_total) - parseFloat(flat_rate)) + parseFloat(shipping_fee) + parseFloat(tax_val);
                    $('.total-cost').html(total_cost.toFixed(2));
                    $('#totalAmount').val(total_cost.toFixed(2));
                    $('#promoCodeId').val(data.promo_code_id);
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('keyup', '.change-promo-code', function (e) {
        $('#applyCouponBtn').removeAttr('disabled', 'disabled');
        $('#applyCouponBtn').val('Apply Coupon');
        $('.flat-rate').html(0);
        var sub_total = $('.sub-total').html();
        var shipping_fee = $('.shipping-fee').html();
        var tax_val = $('#taxValue').val();
        var total_cost = parseFloat(sub_total) + parseFloat(shipping_fee) + parseFloat(tax_val);
        $('.total-cost').html(total_cost.toFixed(2));
        $('#totalAmount').val(total_cost.toFixed(2));
        $('#promoCodeId').val('');
    });

    $(document).on('click', '.select-user-address', function (e) {
        $('.checked-box').hide();
        $('.unchecked-box').show();
        $(this).find('.checked-box').show();
        $(this).find('.unchecked-box').hide();
        var address_id = $(this).attr('addressId');
        $('#selectedAddressId').val(address_id);
        var shipping_fee = $(this).attr('shippingFee') ? $(this).attr('shippingFee') : 0;
        $('.shipping-fee').html(shipping_fee);
        var sub_total = $('.sub-total').html();
        var flat_rate = $('.flat-rate').html();
        var tax_val = $('#taxValue').val();
        var total_cost = (parseFloat(sub_total) - parseFloat(flat_rate)) + parseFloat(shipping_fee) + parseFloat(tax_val);
        $('.total-cost').html(total_cost.toFixed(2))
        $('#totalAmount').val(total_cost.toFixed(2));
    });

    $(document).on('click', '.delete-cart-item', function () {
        var btn = this;
        let uid = getCookie('naiz_web_user_uid');
        let product_uid = $(this).closest('tr').attr('product-uid');
        let vendor_uid = $(this).closest('tr').attr('vendor-uid');
        let product_size_id = $(this).closest('tr').attr('product-size-id');
        let color_id = $(this).closest('tr').attr('color-id');
        let design_id = $(this).closest('tr').attr('design-id');
        let warranty_id = $(this).closest('tr').attr('warranty-id');
        let cart_id = $(this).closest('tr').attr('cart-id');

        Swal.fire({
            title: '<div class="mt-4"><h5>Do you really want to remove this product?</h5></div>',
            showCancelButton: true,
            width: 500,
            padding: 8,
            confirmButtonText: `Confirm`,
            confirmButtonColor: "#5e72e4",
            cancelButtonText: `Close`,
        }).then((result) => {
            if (result.value) {
                let form_data = new FormData();
                form_data.append('uid', uid);
                form_data.append('page', 'cart');
                form_data.append('count', 0);
                form_data.append('product_uid', product_uid);
                form_data.append('vendor_uid', vendor_uid);
                form_data.append('product_size_id', product_size_id);
                form_data.append('color_id', color_id);
                form_data.append('warranty_id', warranty_id);
                form_data.append('design_id', design_id);
                let url = BASE_URL + "add_to_cart";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        if (data.status == 'Success') {
                            getCartCountContent();
                            $('.no-stock-available-td-' + cart_id).remove();
                            $('.cart-tr-' + cart_id).remove();

                            let total_price_cost = 0;
                            let cart_tr = $('.cart-tr');

                            let cart_all_check_length = $(cart_tr).find('.cart-checkbox-input').is(':checked');

                            if (cart_all_check_length) {
                                for (var i = 0; i < cart_tr.length; i++) {
                                    if ($(cart_tr[i]).find('.cart-checkbox-input').is(':checked')) {
                                        let stock_count = $(cart_tr[i]).find('.stock-count').val();
                                        let display_price = $(cart_tr[i]).find('.stock-price').val();
                                        total_price_cost = parseInt(total_price_cost) + parseInt(stock_count * display_price);
                                        $(cart_tr[i]).find('.total-display-price-amount').html(stock_count * display_price);
                                    }
                                }
                            } else {
                                for (var i = 0; i < cart_tr.length; i++) {
                                    let stock_count = $(cart_tr[i]).find('.stock-count').val();
                                    let display_price = $(cart_tr[i]).find('.stock-price').val();
                                    total_price_cost = parseInt(total_price_cost) + parseInt(stock_count * display_price);
                                    $(cart_tr[i]).find('.total-display-price-amount').html(stock_count * display_price);
                                }
                            }

                            $('.total-price-cost').html(total_price_cost);

                            Toast.fire({
                                type: 'success',
                                title: 'Removed Successfully'
                            });
                        } else {
                            Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                        }
                    }
                });
            }
        });
    });

    $(document).on('click', '.clear-cart-btn', function (e) {
        e.preventDefault();
        var btn = this;
        let uid = getCookie('naiz_web_user_uid');
        let vendor_uid = $(this).attr('vendor-uid');
        Swal.fire({
            title: '<div class="mt-4"><h5>Do you really want to clear cart?</h5></div>',
            showCancelButton: true,
            width: 500,
            padding: 8,
            confirmButtonText: `Confirm`,
            confirmButtonColor: "#5e72e4",
            cancelButtonText: `Close`,
        }).then((result) => {
            if (result.value) {
                $(btn).prop('disabled', true);
                $(btn).html('Please Wait');
                let form_data = new FormData();
                form_data.append('uid', uid);
                form_data.append('vendor_uid', vendor_uid);
                let url = BASE_URL + "clear_cart";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        $(btn).prop('disabled', false);
                        $(btn).html('Clear Cart');
                        if (data.status == 'Success') {
                            $('.total-price-cost').html(0);
                            $('.checkout-btn-div').addClass('hidden');
                            $('.cart-table-body-div').remove();
                            $(btn).remove();
                            Toast.fire({
                                type: 'success',
                                title: 'Cleared Successfully'
                            });
                            getCartCountContent();
                        } else {
                            Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                        }
                    }
                });
            }
        });
    });

    $(document).on('click', '.pagination-btn-click', function () {
        let type = $(this).closest('footer').attr('type-id');
        let pageNo = $(this).attr('page-no');
        if (type == 'orderTableContent') {
            getAccountTabContent('#orders', pageNo)
        } else if (type == 'prdtDetailReviewTableContent') {
            getPrdtDetailReviewContent(pageNo)
        } else if (type == 'prdtListItemDiv') {
            getPrdtListItemContent(pageNo)
        } else if (type == 'userAddressCheckoutContent') {
            getUserAddressCheckout(pageNo)
        } else if (type == 'userAddressContent') {
            getAccountTabContent('#address-edit', pageNo)
        } else if (type == 'vendorListId') {
            getVendorListContent(pageNo);
        }
    });

    $(document).on('click', '.description-review-topbar .prdt-review-btn', function () {
        getPrdtDetailReviewContent(1)
        getPrdtDetailAddReviewContent();
    });

    $(document).on('submit', '#addPrdtDetailReviewForm', function (e) {
        e.preventDefault();
        let btn = $('#addPrdtDetailReviewForm .add-prdt-detail-review-btn');
        $(btn).prop('disabled', true);
        $(btn).html('Please Wait');
        let uid = getCookie('naiz_web_user_uid');
        let form_data = new FormData(this);
        form_data.append('uid', uid);
        let url = BASE_URL + "add_product_review";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $(btn).prop('disabled', false);
                $(btn).html('Submit');
                if (data.status == 'Success') {
                    $('.review-val').val('');
                    $('.rating-val').val(1);
                    $('.add-prdt-review-div .your-rating .ti-star').removeClass('star-active');
                    let star_list = $('.add-prdt-review-div .your-rating .ti-star');
                    for (let i = 0; i < star_list.length; i++) {
                        if (i < 1) {
                            $(star_list[i]).addClass('star-active');
                        }
                    }
                    getPrdtDetailReviewContent(1);
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('click', '.add-prdt-review-div .your-rating .ti-star', function () {
        $('.add-prdt-review-div .your-rating .ti-star').removeClass('star-active');
        let star_list = $('.add-prdt-review-div .your-rating .ti-star');
        let rating = $(this).attr('val-id');
        for (let i = 0; i < star_list.length; i++) {
            if (i < rating) {
                $(star_list[i]).addClass('star-active');
            }
        }
        $('#addPrdtDetailReviewForm').find('.rating-val').val(rating);
    });

    $(document).on('click', '.proceed-to-checkout-btn', function () {
        let uid = getCookie('naiz_web_user_uid');
        let vendor_uid = $(this).attr('vendor-uid');
        let form_data = new FormData();
        form_data.append('uid', uid);
        form_data.append('vendor_uid', vendor_uid);
        form_data.append('type', 'checkout');

        let cart_tr = $('.cart-tr');

        let cart_check_length = $(cart_tr).find('.cart-checkbox-input').is(':checked');

        let cart_id_array = [];

        if (cart_check_length) {
            for (var i = 0; i < cart_tr.length; i++) {
                if ($(cart_tr[i]).find('.cart-checkbox-input').is(':checked')) {
                    let stock_count = $(cart_tr[i]).find('.stock-count').val();
                    if (stock_count == '' || stock_count == 0) {
                        Swal.fire({text: 'Atleast 1 is required', confirmButtonColor: "#e97730"});
                        return;
                    }
                    cart_id_array.push($(cart_tr[i]).attr('cart-id'))
                }
            }
        } else {
            for (var i = 0; i < cart_tr.length; i++) {
                let stock_count = $(cart_tr[i]).find('.stock-count').val();
                if (stock_count == '' || stock_count == 0) {
                    Swal.fire({text: 'Atleast 1 is required', confirmButtonColor: "#e97730"});
                    return;
                }
            }
        }

        if (cart_id_array.length > 0) {
            form_data.append('cart_id_array', JSON.stringify(cart_id_array));
        }

        let url = BASE_URL + "get_cart_list";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'Success') {
                    if (data.error_count == 0) {
                        setCookie('cart_id_array', JSON.stringify(cart_id_array));
                        location.href = './checkout';
                    } else {
                        location.href = './cart';
                    }
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    if (document.getElementById('prdtSizeDetailPriceContent')) {
        getPrdtSizeDetail();
    }

    $(document).on('change', '.prdt-detail-size-select', function () {
        getPrdtSizeDetail();
    });

    $(document).on('click', '#storeListModal .content', function () {
        $('.checked-box').removeClass('active');
        $('.unchecked-box').addClass('active');
        $(this).find('.checked-box').addClass('active');
        $(this).find('.unchecked-box').removeClass('active');
    });

    $(document).on('click', '#storeListModal .modal-submit', function () {
        let vendor_uid = $('#storeListModal .checked-box.active').attr('vendor-uid');
        if (typeof vendor_uid != 'undefined') {
            $('#vendorListId .single-store').removeClass('selected-store');
            $('#vendorListId .single-store .fa-check').addClass('hidden');
            $('#vendorListId .store-' + vendor_uid).addClass('selected-store');
            $('#vendorListId .store-' + vendor_uid + ' .fa-check').removeClass('hidden');
            setCookie('naiz_web_vendor_uid', vendor_uid);
            const modal_id = document.querySelector('#modalDivId');
            const modal = bootstrap.Modal.getInstance(modal_id);
            modal.hide();
            // getHomeContent();
            location.reload();
        } else {
            Swal.fire({text: 'Select a Vendor', confirmButtonColor: "#e97730"});
        }
    });

    // if (document.getElementById('homeContent')) {
    //     let naiz_web_vendor_uid = getCookie('naiz_web_vendor_uid');
    //     if (naiz_web_vendor_uid != '') {
    //         getHomeContent();
    //     }
    // }

    if (document.getElementById('landingPage')) {
        var myModal = new bootstrap.Modal(document.getElementById('modalDivId'), {
            backdrop: 'static',
            keyboard: false
        });
        myModal.show();
        modalContent('landing_page', '');
    }

    $(document).on('click', '.on-close-landing-modal-click', function () {
        const modal_id = document.querySelector('#modalDivId');
        const modal = bootstrap.Modal.getInstance(modal_id);
        modal.hide();
        let naiz_web_vendor_uid = getCookie('naiz_web_vendor_uid');
        if (naiz_web_vendor_uid == '') {
            var myModal = new bootstrap.Modal(document.getElementById('modalDivId'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();
            modalContent('store_list', '');
        }
    });

    $(document).on('click', '#vendorListId .single-store', function () {
        let vendor_uid = $(this).attr('vendor-uid');
        $('#vendorListId .single-store').removeClass('selected-store');
        $('#vendorListId .single-store .fa-check').addClass('hidden');
        $(this).addClass('selected-store');
        $(this).find('.fa-check').removeClass('hidden');
        setCookie('naiz_web_vendor_uid', vendor_uid);
        getCartCountContent();
    });

    if (document.getElementById('prdtListItemDiv')) {
        getPrdtListItemContent(1);
    }

    $(document).on('click', '.category-filter-list li', function (e) {
        e.preventDefault();
        $('.category-filter-list li').removeClass('active');
        $(this).addClass('active');
        getPrdtListItemContent(1);
    });

    $(document).on('click', '.tag-filter-list a', function (e) {
        e.preventDefault();
        $('.tag-filter-list a').removeClass('active');
        $(this).addClass('active');
        getPrdtListItemContent(1);
    });

    $(document).on('keyup', '.search-product-input', function (e) {
        e.preventDefault();
        getPrdtListItemContent(1);
    });

    $(document).on('click', '.shop-view-mode a', function (e) {
        e.preventDefault();
        getPrdtListItemContent(1);
    });

    $(document).on('click', '.price-filter button', function (e) {
        e.preventDefault();
        $(this).addClass('active');
        getPrdtListItemContent(1);
    });

    $(document).on('change', '.shop-sorting-select', function (e) {
        e.preventDefault();
        getPrdtListItemContent(1);
    });

    $(document).on('click', '.place-order-btn', function (e) {
        e.preventDefault();
        let btn = $(this);
        let uid = getCookie('naiz_web_user_uid');
        let vendor_uid = $('#vendorUid').val();
        let amount = $('#totalAmount').val();
        let promo_code_id = $('#promoCodeId').val();
        let promo_code = '';
        let flat_rate = 0;
        if (promo_code_id) {
            promo_code = $('#promoCode').val();
            flat_rate = $('.flat-rate').text().trim();
        }
        let tax = $('.tax').text().trim();
        let selected_address_id = $('#selectedAddressId').val();
        let shipping_fee = 0;
        if (selected_address_id) {
            shipping_fee = $('.shipping-fee').text().trim();
        }
        let user_email = $('#userEmail').val();
        let user_mobile = $('#userMobile').val();
        let user_full_name = $('#userFullName').val();
        let cart_id_array = $('#cartIdArray').val();

        if (selected_address_id) {
            btn.prop('disabled', true);
            btn.html('Please Wait');
            let form_data = new FormData();
            form_data.append("uid", uid);
            form_data.append("vendor_uid", vendor_uid);
            form_data.append("amount", amount);
            form_data.append("promo_code_id", promo_code_id);
            form_data.append("promo_code", promo_code);
            form_data.append("flat_rate", flat_rate);
            form_data.append("tax", tax);
            form_data.append("shipping_fee", shipping_fee);
            form_data.append("address_id", selected_address_id);
            form_data.append("order_type", 'online');
            if (cart_id_array != '') {
                form_data.append("cart_id_array", cart_id_array);
            }

            let url = BASE_URL + "create_razorpay_order";
            $.ajax({
                url: url,
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status == 'Success') {
                        let order_id = response.order_id;
                        let amount = response.amount;
                        let order_uid = response.order_uid;

                        var razorpay_options = {
                            currency: 'INR',
                            key: response.key,
                            amount: amount,
                            name: 'Naiz',
                            order_id: response.payment_order_id,
                            prefill: {
                                email: user_email,
                                contact: user_mobile,
                                name: user_full_name
                            },
                            theme: {color: '#e97730'},
                            handler: function (transaction) {
                                let form_data1 = new FormData();
                                let url1 = BASE_URL + "place_order";

                                form_data1.append("uid", uid);
                                form_data1.append("vendor_uid", vendor_uid);
                                form_data1.append("order_id", order_id);
                                form_data1.append("payment_id", transaction.razorpay_payment_id);
                                form_data1.append("payment_order_id", transaction.razorpay_order_id);
                                form_data1.append("signature", transaction.razorpay_signature);
                                form_data1.append("total_amt", amount);
                                if (cart_id_array != '') {
                                    form_data1.append("cart_id_array", cart_id_array);
                                }

                                $.ajax({
                                    url: url1,
                                    type: "POST",
                                    data: form_data1,
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    success: function (response) {
                                        if (response.status == 'Success') {
                                            Toast.fire({
                                                type: 'success',
                                                title: 'Placed Successfully'
                                            });
                                            window.location.href = './order_history?uid=' + order_uid + '&s=1';
                                        } else {
                                            $(btn).prop('disabled', false);
                                            $(btn).html('Online Payment');
                                            Swal.fire({text: response.msg, confirmButtonColor: "#e97730"})
                                        }
                                    }
                                })
                            },
                            "modal": {
                                "ondismiss": function () {
                                    $(btn).prop('disabled', false);
                                    $(btn).html('Online Payment');
                                    Swal.fire({text: 'Payment Cancelled', confirmButtonColor: "#e97730"})
                                }
                            }
                        };
                        var objrzpv1 = new Razorpay(razorpay_options);
                        objrzpv1.on('payment.failed', function (response1) {
                            let form_data3 = new FormData();
                            form_data3.append("uid", uid);
                            form_data3.append("vendor_uid", vendor_uid);
                            form_data3.append("order_id", order_id);

                            let url3 = BASE_URL + "failed_order";
                            $.ajax({
                                url: url3,
                                type: "POST",
                                data: form_data3,
                                contentType: false,
                                cache: false,
                                processData: false,
                                success: function (response) {
                                    $(btn).prop('disabled', false);
                                    $(btn).html('Online Payment');
                                    Swal.fire({text: response.msg, confirmButtonColor: "#e97730"});
                                }
                            })
                        });
                        objrzpv1.open();
                        e.preventDefault();
                    } else {
                        if (response.error_type == 1) {
                            window.location.href = './cart';
                        } else {
                            Swal.fire({text: response.msg, confirmButtonColor: "#e97730"});
                        }
                    }
                }
            });
        } else {
            Swal.fire({text: 'Choose Address', confirmButtonColor: "#e97730"});
        }
    });

    $(document).on('click', '.on-cash-on-delivery-btn', function (e) {
        e.preventDefault();
        let btn = $(this);
        let uid = getCookie('naiz_web_user_uid');
        let vendor_uid = $('#vendorUid').val();
        let amount = $('#totalAmount').val();
        let promo_code_id = $('#promoCodeId').val();
        let promo_code = '';
        let flat_rate = 0;
        if (promo_code_id) {
            promo_code = $('#promoCode').val();
            flat_rate = $('.flat-rate').text().trim();
        }
        let tax = $('.tax').text().trim();
        let selected_address_id = $('#selectedAddressId').val();
        let shipping_fee = 0;
        if (selected_address_id) {
            shipping_fee = $('.shipping-fee').text().trim();
        }
        let cart_id_array = $('#cartIdArray').val();

        if (selected_address_id) {
            Swal.fire({
                title: '<div class="mt-4"><h5>Do you want to place an order?</h5></div>',
                showCancelButton: true,
                width: 500,
                padding: 8,
                confirmButtonText: `Yes`,
                confirmButtonColor: "#e97730",
                cancelButtonText: `No`,
            }).then((result) => {
                if (result.value) {
                    $(btn).prop('disabled', true);
                    $(btn).html('Please Wait');
                    let form_data = new FormData();
                    form_data.append("uid", uid);
                    form_data.append("vendor_uid", vendor_uid);
                    form_data.append("amount", amount);
                    form_data.append("promo_code_id", promo_code_id);
                    form_data.append("promo_code", promo_code);
                    form_data.append("flat_rate", flat_rate);
                    form_data.append("tax", tax);
                    form_data.append("shipping_fee", shipping_fee);
                    form_data.append("address_id", selected_address_id);
                    form_data.append("order_type", 'cod');

                    if (cart_id_array != '') {
                        form_data.append("cart_id_array", cart_id_array);
                    }

                    let url = BASE_URL + "create_razorpay_order";
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: form_data,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (response) {
                            $(btn).prop('disabled', false);
                            $(btn).html('Cash On Delivery');
                            if (response.status == 'Success') {
                                Toast.fire({
                                    type: 'success',
                                    title: 'Placed Successfully'
                                });
                                let order_uid = response.order_uid;
                                window.location.href = './order_history?uid=' + order_uid + '&s=1';
                            } else {
                                if (response.error_type == 1) {
                                    window.location.href = './cart';
                                } else {
                                    Swal.fire({text: response.msg, confirmButtonColor: "#e97730"});
                                }
                            }
                        }
                    });
                }
            });
        } else {
            Swal.fire({text: 'Choose Address', confirmButtonColor: "#e97730"});
        }
    });

    $(document).on('submit', '#forgotPasswordForm', function (e) {
        e.preventDefault();
        let btn = $('#forgotPasswordForm .forgot-password-form');
        btn.prop('disabled', true);
        btn.html('Please Wait');
        let form_data = new FormData(this);
        let url = BASE_URL + "forgot_password";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                btn.prop('disabled', false);
                btn.html('Send');
                if (data.status == 'Success') {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('submit', '#resetPswd', function (e) {
        var btn = $('#resetPswd .reset-pswd');
        e.preventDefault();
        var user_id = $('#userId').val();
        var password = $('#password').val();
        var confirm_pswd = $('#confirmPassword').val();
        $(btn).prop('disabled', true);
        $(btn).html('Please Wait');
        var ajaxurl = 'includes/functions.php',
            data = {
                'action': 'updateUserPassword',
                'user_id': user_id,
                'password': password,
                'confirm_pswd': confirm_pswd,
            };
        $.post(ajaxurl, data, function (response) {
            $(btn).prop('disabled', false);
            $(btn).html('Submit');
            if (response == 1) {
                $('#password').val('');
                $('#confirmPassword').val('');
                $('.reset-password-div').html('<div class="text-center pt-4 color-white">Password Updated Successfully</div>')
            } else {
                Toast.fire({
                    type: 'warning',
                    title: response
                });
            }
        })
    });

    if (document.getElementById('cartCountContent')) {
        getCartCountContent();
    }

    if (document.getElementById('userAddressCheckoutContent')) {
        getUserAddressCheckout(1);
    }

    if (document.getElementById('vendorListId')) {
        getVendorListContent(1);
    }

    $(document).on("click", ".edit-user-address", function (e) {
        e.preventDefault();
        let address_id = $(this).attr("address-id");
        getEditAddressContent(address_id);
    });

    $(document).on('submit', '#updateUserAddressForm', function (e) {
        e.preventDefault();
        let btn = $('#updateUserAddressForm .update-user-address-form');
        btn.prop('disabled', true);
        btn.html('Please Wait');
        let form_data = new FormData(this);
        let url = BASE_URL + "update_user_address";
        $.ajax({
            url: url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                btn.prop('disabled', false);
                btn.html('Update');
                if (data.status == 'Success') {
                    getAccountTabContent('#address-edit', 1);
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                } else {
                    Swal.fire({text: data.msg, confirmButtonColor: "#e97730"});
                }
            }
        });
    });

    $(document).on('change', '.prdt-detail-color-warranty-select', function () {
        let btn = this;
        let design_id = $('.prdt-detail-design-select').val();
        let vendor_id = $('#vendorId').val();
        let user_id = $('#userId').val();
        let product_size_id = $('#prdtSizeId').val();
        let color_id = $('.prdt-color-select.active').attr('color-id');
        let stock = $('.prdt-color-select.active').attr('color-stock');
        let warranty_id = $('.prdt-detail-color-warranty-select').find(':selected').attr('warranty-id');

        var ajaxurl = 'includes/functions.php',
            data = {
                'action': 'getCartCount',
                'user_id': user_id,
                'vendor_id': vendor_id,
                'product_size_id': product_size_id,
                'color_id': color_id,
                'warranty_id': warranty_id,
                'design_id': design_id,
                'type': true,
            };
        $.post(ajaxurl, data, function (response) {
            $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id', response);
            let price = $(btn).find(':selected').attr('price-id');
            let offer_price = $(btn).find(':selected').attr('offer-price-id');
            prdtDetailPriceOfferPrice(price, offer_price);
            prdtDetailAddToCartCountBtn(stock, response);
        })
    });

    $(document).on('change', '.prdt-detail-design-select', function () {
        let design_id = $(this).val();
        let color_check = $('#colorCheck').val();
        let vendor_id = $('#vendorId').val();
        let user_id = $('#userId').val();
        let product_size_id = $('#prdtSizeId').val();

        let stock = 0;
        let warranty_check = 0;
        let color_id = '';
        let warranty_id = '';
        if (color_check == 0) {
            stock = $('#totalStockCount').val();
        } else {
            color_id = $('.prdt-color-select.active').attr('color-id');
            warranty_check = $('.prdt-color-select.active').attr('warranty-check');
            stock = $('.prdt-color-select.active').attr('color-stock');
            if (warranty_check == 1) {
                warranty_id = $('.prdt-detail-color-warranty-select').find(':selected').attr('warranty-id');
            } else {
            }
        }

        var ajaxurl = 'includes/functions.php',
            data = {
                'action': 'getCartCount',
                'user_id': user_id,
                'vendor_id': vendor_id,
                'product_size_id': product_size_id,
                'color_id': color_id,
                'warranty_id': warranty_id,
                'design_id': design_id,
                'type': true,
            };
        $.post(ajaxurl, data, function (response) {
            if (color_check == 0) {
                $('#currentCount').val(response);
            } else {
                if (warranty_check == 1) {
                    $('.prdt-detail-color-warranty-select').find(':selected').attr('count-id', response);
                } else {
                    $('.prdt-color-select.active').attr('color-count', response);
                }
            }

            prdtDetailAddToCartCountBtn(stock, response);
            getColorImage();
        })
    });

    $(document).on('click', '.cart-checkbox-input', function () {
        let total_cost = 0;
        let cart_all_check_length = $('.cart-tr').find('.cart-checkbox-input').is(':checked');

        if (cart_all_check_length) {
            let new_total_price_cost = $('.total-price-total-hidden').val();
            if (new_total_price_cost != 0) {
                total_cost = $('.total-price-cost').html();
            }
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
                let price = $(this).closest('tr').find('.total-display-price-amount').html();
                total_cost = parseFloat(total_cost) + parseFloat(price);
            } else {
                $(this).removeAttr('checked');
                let price = $(this).closest('tr').find('.total-display-price-amount').html();
                total_cost = parseFloat(total_cost) - parseFloat(price);
            }

            $('.total-price-cost').html(total_cost)
            $('.total-price-total-hidden').val(total_cost)
        } else {
            let cart_tr = $('.cart-tr');
            let total_cost = 0;
            for (var i = 0; i < cart_tr.length; i++) {
                let stock_count = $(cart_tr[i]).find('.stock-count').val();
                let display_price = $(cart_tr[i]).find('.stock-price').val();
                total_cost = parseInt(total_cost) + parseInt(stock_count * display_price);
            }

            $('.total-price-cost').html(total_cost)
            $('.total-price-total-hidden').val(0)
        }
    });

    $(document).on("click", ".on-reg-password-eye-click", function () {
        var pswd_type = $('.password-type').attr('type');
        if (pswd_type == 'password') {
            $(this).find(".eye-icon").removeClass('fa fa-eye-slash');
            $(this).find(".eye-icon").addClass('fa fa-eye');
            $('.password-type').attr('type', 'text')
        } else {
            $(this).find(".eye-icon").removeClass('fa fa-eye');
            $(this).find(".eye-icon").addClass('fa fa-eye-slash');
            $('.password-type').attr('type', 'password')
        }
    });

});

function setCookie(cookie, value) {
    let d = new Date();
    d.setTime(d.getTime() + (60 * 60 * 24 * 365));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cookie + "=" + value + ";" + expires + ";path=/";

    let ajaxurl = 'includes/functions.php',
        data = {
            'action': 'setWebCookie',
            'cookie': cookie,
            'value': value
        };
    $.post(ajaxurl, data, function (response) {
    });
}

function getCookie(cookie_name) {
    let name = cookie_name + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function modalContent(modal_type, params) {
    var url = '';
    var data = '';
    if (modal_type == 'store_list') {
        url = "includes/modals/store_list_modal_content.php";
    } else if (modal_type == 'login') {
        url = "includes/modals/login_modal_content.php";
    } else if (modal_type == 'landing_page') {
        url = "includes/modals/landing_modal_content.php";
    }
    $.ajax({
        type: "GET",
        url: url,
        data: data,
        cache: true,
        beforeSend: function () {
            $("#modalDivContent").html('');
        },
        success: function (html) {
            $("#modalDivContent").html(html);
        }
    })
}

function getAccountTabContent(type, page) {
    let temp = 'user_order_content.php';
    let data = '';
    if (type == '#address-edit') {
        temp = 'user_address_content.php';
        data = 'page=' + page;
    } else if (type == '#account-info') {
        temp = 'user_detail_form_content.php';
    } else if (type == '#orders') {
        data = 'page=' + page;
    }
    $.ajax({
        type: "GET",
        url: "includes/templates/" + temp,
        data: data,
        cache: true,
        beforeSend: function () {
            $('#myaccountContent').find(type + ' .tab-content').html('')
        },
        success: function (html) {
            $('#myaccountContent').find(type + ' .tab-content').html(html)
        }
    })
}

function getEditAddressContent(address_id) {
    let data = 'address_id=' + address_id;
    $.ajax({
        type: "GET",
        url: "includes/templates/edit_user_address_content.php",
        data: data,
        cache: true,
        beforeSend: function () {
            $('#myaccountContent').find('#address-edit .tab-content').html('')
        },
        success: function (html) {
            $('#myaccountContent').find('#address-edit .tab-content').html(html)
        }
    })
}

function getPrdtDetailReviewContent(page) {
    let product_uid = $('#productUid').val();
    let data = 'page=' + page + '&product_uid=' + product_uid;
    $.ajax({
        type: "GET",
        url: "includes/templates/prdt_detail_review_content.php",
        data: data,
        cache: true,
        beforeSend: function () {
            $('#prdtDetailReviewTableContent').html('')
        },
        success: function (html) {
            $('#prdtDetailReviewTableContent').html(html)
        }
    })
}

function getPrdtDetailAddReviewContent() {
    let product_uid = $('#productUid').val();
    let data = 'product_uid=' + product_uid;
    $.ajax({
        type: "GET",
        url: "includes/templates/prdt_detail_add_review_content.php",
        data: data,
        cache: true,
        beforeSend: function () {
            $('#prdtDetailAddReviewTableContent').html('')
        },
        success: function (html) {
            $('#prdtDetailAddReviewTableContent').html(html)
        }
    })
}

function getPrdtSizeDetail() {
    let size = $('.prdt-detail-size-select').val();
    let design_id = $('.prdt-detail-design-select').val();
    var ajaxUrl = 'includes/templates/prdt_size_detail_content.php',
        data = {
            size_id: size,
            design_id: design_id,
            product_id: $('#productId').val(),
            vendor_id: $('#vendorId').val(),
            cart_id: $('#cartId').val(),
        };
    $.post(ajaxUrl, data, function (response) {
        $('#prdtSizeDetailPriceContent').html($(response)[0]);
        $('#prdtSizeDetailColorCartContent').html($(response)[2]);
        $('#prdtSizeDetailContent').html($(response)[4]);
        getColorImage();
    })
}

function getPrdtListItemContent(page) {
    let cat_id = $('.category-filter-list li.active').attr('cat-id');
    let tag_id = $('.tag-filter-list a.active').attr('tag-id');
    let search = $('.search-product-input').val();
    let sort_by = $('.shop-sorting-select').val();
    let view_mode = $('.shop-view-mode').find('a.active').attr('view-mode');
    let price_start = '';
    let price_end = '';
    if ($('.price-slider-amount .filter-btn').hasClass('active')) {
        let price = $('.price-filter').find('#amount').val();
        price = price.split('-');
        price_start = price[0].trim().replace('₹', '');
        price_end = price[1].trim().replace('₹', '');
    }
    let data = 'page=' + page + '&cat_id=' + cat_id + '&tag_id=' + tag_id + '&search=' + search + '&view_mode=' + view_mode
        + '&price_start=' + price_start + '&price_end=' + price_end + '&sort_by=' + sort_by;

    $.ajax({
        type: "GET",
        url: "includes/templates/product_list_item.php",
        data: data,
        cache: true,
        beforeSend: function () {
            let loading_html = ' <div class="col-md-12 align-items-center justify-content-center d-flex product-list-main-loader"\n' +
                '                                     style="height: 300px">\n' +
                '                                    <div class="spinner-border loading-color" role="status">\n' +
                '                                        <span class="sr-only">Loading...</span>\n' +
                '                                    </div>\n' +
                '                                </div>'
            $('#prdtListItemDiv').html(loading_html)
            $('#prdtListItemDiv1').html(loading_html)
        },
        success: function (html) {
            if (view_mode == 'shop1') {
                $('#prdtListItemDiv').html(html);
            } else {
                $('#prdtListItemDiv1').html(html);
            }
            let total_count = $('#totalCount').val();
            let limit = $('#limit').val();
            let list_length = $('#listLength').val();
            let start = ((page - 1) * limit + 1);
            let end = (parseInt(start) + parseInt(list_length) - 1);
            let text = 'Showing ' + start + '-' + end + ' of ' + total_count + ' results';

            if (start == end) {
                text = 'Showing ' + start + ' of ' + total_count + ' results';
            }

            if (total_count == 0) {
                text = 'Showing 0 of ' + total_count + ' results';
            }
            if (total_count >= 0) {
                $('.showing-item span').html(text);
            }
        }
    })
}

function getCartCountContent() {
    $.ajax({
        type: "GET",
        url: "includes/templates/cart_count_content.php",
        cache: true,
        success: function (html) {
            $('#cartCountContent').html(html)
        }
    })
}

function getUserAddressCheckout(page) {
    let address_id = $('#selectedAddressId').val();
    let data = 'page=' + page + '&address_id=' + address_id;
    $.ajax({
        type: "GET",
        url: "includes/templates/user_address_checkout_content.php",
        data: data,
        cache: true,
        beforeSend: function () {
            $('#userAddressCheckoutContent').html('')
        },
        success: function (html) {
            $('#userAddressCheckoutContent').html(html)
        }
    })
}

function getVendorListContent(page) {
    let data = 'page=' + page;
    $.ajax({
        type: "GET",
        url: "includes/templates/vendor_content.php",
        data: data,
        cache: true,
        beforeSend: function () {
            $('#vendorListId').html('')
        },
        success: function (html) {
            $('#vendorListId').html(html)
        }
    })
}

function getColorWarrantList(btn) {
    let color_id = $(btn).attr("color-id");
    let prdt_size_color_id = $(btn).attr("prdt-size-color-id");
    let design_id = $('.prdt-detail-design-select').val();
    let product_size_id = $('#prdtSizeId').val();
    let vendor_id = $('#vendorId').val();
    let cart_id = $('#cartId').val();
    var ajaxUrl = 'includes/templates/prdt_detail_color_warranty_select.php',
        data = {
            color_id: color_id,
            product_size_id: product_size_id,
            prdt_size_color_id: prdt_size_color_id,
            design_id: design_id,
            vendor_id: vendor_id,
            cart_id: cart_id,
        };
    $.post(ajaxUrl, data, function (html) {
        $('.color-warranty-select-div').html(html);
        $('.prdt-detail-color-warranty-select').select2();
        let count = $('.color-warranty-select-div .prdt-detail-color-warranty-select').find(':selected').attr('count-id');
        $('.prdt-color-select.active').attr('color-count', count);
        let price = $('.color-warranty-select-div .prdt-detail-color-warranty-select').find(':selected').attr('price-id');
        let offer_price = $('.color-warranty-select-div .prdt-detail-color-warranty-select').find(':selected').attr('offer-price-id');
        let warranty_id = $('.color-warranty-select-div .prdt-detail-color-warranty-select').find(':selected').attr('warranty-id');
        prdtDetailPriceOfferPrice(price, offer_price);
        getColorCartCount(btn, warranty_id);
    })
}

function getColorCartCount(btn, warranty_id) {
    let color_id = $(btn).attr("color-id");
    let stock = $(btn).attr("color-stock");
    let design_id = $('.prdt-detail-design-select').val();
    let vendor_id = $('#vendorId').val();
    let user_id = $('#userId').val();
    let product_size_id = $('#prdtSizeId').val();

    var ajaxurl = 'includes/functions.php',
        data = {
            'action': 'getCartCount',
            'user_id': user_id,
            'vendor_id': vendor_id,
            'product_size_id': product_size_id,
            'color_id': color_id,
            'warranty_id': warranty_id,
            'design_id': design_id,
            'type': true,
        };
    $.post(ajaxurl, data, function (response) {
        prdtDetailAddToCartCountBtn(stock, response);
        getColorImage();
    })
}

function prdtDetailAddToCartCountBtn(stock, count) {
    $('.cart-plus-minus-box-prdt-detail').val(count);
    if ((stock > 0) || (stock == 'unlimited')) {
        if (count > 0) {
            $('.cart-btn').html('Go to Cart');
            $('.cart-btn').attr("type", "go_to_cart");
        } else {
            $('.cart-btn').html('Add to Cart');
            $('.cart-btn').attr("type", "add_to_cart");
            $('.cart-plus-minus-box-prdt-detail').val(1);
        }
        $('.product-detail-cart').removeClass('product-detail-cart-out-of-stock-btn');
        $('.product-detail-cart').addClass('product-detail-cart-btn-color btn-hover');
    } else {
        $('.cart-btn').html('Out of Stock');
        $('.cart-btn').attr("type", "out_of_stock");
        $('.product-detail-cart').removeClass('product-detail-cart-btn-color btn-hover');
        $('.product-detail-cart').addClass('product-detail-cart-out-of-stock-btn');
    }
}

function prdtDetailPriceOfferPrice(price, offer_price) {
    if (offer_price > 0) {
        $('.product-details-price').html('<span class="old-price">₹' + price + '</span><span class="new-price">₹' + offer_price + '</span>')
    } else {
        $('.product-details-price').html('<span class="new-price">₹' + price + '</span>')
    }
}

// function getHomeContent() {
//     $.ajax({
//         type: "GET",
//         url: "includes/templates/home_content.php",
//         cache: true,
// beforeSend: function () {
//     $('#homeContent').html('')
// },
// success: function (html) {
//     $('#homeContent').html(html)
// }
// })
// }

function getColorImage() {
    var color_id = $('.prdt-color-select.active').attr('color-id');
    var design_id = $('.prdt-detail-design-select').val();
    var product_uid = $('#productUid').val();
    let data = 'color_id=' + color_id + '&product_uid=' + product_uid + '&design_id=' + design_id;
    $.ajax({
        type: "GET",
        url: "includes/templates/product_color_image_content.php",
        data: data,
        cache: true,
        success: function (html) {
            $('#prdtImgContent').html(html);
        }
    })
}