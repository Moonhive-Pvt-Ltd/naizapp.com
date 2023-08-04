<tr class="cart-tr
    cart-tr-<?php echo $row['cart_id']; ?>"
    style="position: relative"
    cart-id="<?php echo $row['cart_id']; ?>"
    is-design="<?php echo $row['is_design']; ?>"
    is-warranty="<?php echo $row['is_warranty']; ?>"
    product-uid="<?php echo $row['product_uid']; ?>"
    vendor-uid="<?php echo $vendor_uid; ?>"
    design-id="<?php echo $row['product_design_id']; ?>"
    warranty-id="<?php echo $row['warranty_id']; ?>"
    product-size-id="<?php echo $row['product_size_id']; ?>"
    color-id="<?php echo $row['color_id']; ?>"
    count="<?php echo $row['count']; ?>"
    display-price="<?php echo $row['display_price']; ?>">
    <td class="product-thumbnail cart-checkbox-image">
        <div class="cart-checkbox-div">
            <input class="cart-checkbox-input cursor-pointer" type="checkbox">
        </div>
        <a>
            <img src="<?php echo $row['image']; ?>" alt=""/>
        </a>
    </td>
    <td class="product-name">
        <h6>
            <a>
                <?php echo $row['name']; ?>
            </a>
            <div class="d-flex align-items-center">
                <span class="cart-size-span"><?php echo $row['size']; ?></span>
                <?php if ($row['color_code']) { ?>
                    <div class="cart-color-div"
                         style="background-color: <?php echo $row['color_code']; ?>"></div>
                <?php } ?>
            </div>
        </h6>
    </td>
    <td>
        <input type="hidden" value="<?php echo $row['count']; ?>"
               class="stock-count">
        <input type="hidden" value="<?php echo $row['display_price']; ?>"
               class="stock-price">
        <input type="hidden" value="<?php echo $user_id; ?>"
               id="userId">
        <input type="hidden" value="<?php echo $vendor_id; ?>"
               id="vendorId">
        <input type="hidden" value="<?php echo $vendor_uid; ?>"
               id="vendorUid">
        <?php echo $row['is_design'] ? 'Design: ' . $row['design_name'] : ''; ?><br/>
        <?php echo $row['is_warranty'] ? 'Warranty: ' . $row['warranty'] : ''; ?>
    </td>
    <td class="product-cart-price">
        <span class="amount">₹<?php echo $row['display_price']; ?></span>
    </td>
    <td class="cart-quality">
        <div class="product-quality">
            <input class="cart-plus-minus-box input-text qty text"
                   name="qtybutton"
                   is-warranty="<?php echo $row['is_warranty']; ?>"
                   is-design="<?php echo $row['is_design']; ?>"
                   product-uid="<?php echo $row['product_uid']; ?>"
                   vendor-uid="<?php echo $vendor_uid; ?>"
                   product-size-id="<?php echo $row['product_size_id']; ?>"
                   color-id="<?php echo $row['color_id']; ?>"
                   warranty-id="<?php echo $row['warranty_id']; ?>"
                   design-id="<?php echo $row['product_design_id']; ?>"
                   stock="<?php echo $row['stock']; ?>"
                   current-val="<?php echo $row['count']; ?>"
                   value="<?php echo $row['count']; ?>">
        </div>
    </td>
    <td class="product-total">
        ₹<span class="total-display-price-amount"><?php echo $row['total_display_price']; ?></span>
    </td>
    <td class="product-remove">
        <i class="ti-trash delete-cart-item cursor-pointer"></i>
        <?php if ($row['error'] == '') { ?>
            <a href="product_details?id=<?php echo $row['product_uid'] ?>&cart_id=<?php echo $row['cart_id']; ?>">
                <i class="ti-eye view-cart-item cursor-pointer p-3"></i>
            </a>
        <?php } ?>
    </td>
</tr>

