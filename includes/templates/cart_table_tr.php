<?php if ($i == 0 && $row['is_warranty']) { ?>
    <tr class="cart-top-tr cart-top-tr-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>">
        <?php $colspan = $row['is_warranty'] > 0 ? (count($row['warranty']) * 2) + 1 : 1; ?>
        <td rowspan="<?php echo $colspan; ?>" class="cart-prdt-size-rowspan rowspan-count product-thumbnail">
            <a href="#">
                <img src="<?php echo $row['image']; ?>" alt=""/>
            </a>
        </td>
        <td rowspan="<?php echo $colspan; ?>" class="cart-prdt-size-rowspan product-name">
            <h6>
                <a href="#">
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
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
<?php } ?>
<tr class="cart-tr
    cart-tr-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>
    cart-tr-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>-<?php echo $row['warranty_id']; ?>"
    style="position: relative"
    is-warranty="<?php echo $row['is_warranty']; ?>"
    product-uid="<?php echo $row['product_uid']; ?>"
    vendor-uid="<?php echo $vendor_uid; ?>"
    warranty-id="<?php echo $row['warranty_id']; ?>"
    product-size-id="<?php echo $row['product_size_id']; ?>"
    color-id="<?php echo $row['color_id']; ?>"
    count="<?php echo $row['count']; ?>"
    display-price="<?php echo $row['display_price']; ?>">
    <?php if ($i == 0 && !$row['is_warranty']) { ?>
        <td class="product-thumbnail">
            <a href="#">
                <img src="<?php echo $row['image']; ?>" alt=""/>
            </a>
        </td>
        <td class="product-name">
            <h6>
                <a href="#">
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
    <?php } ?>
    <td>
        <input type="hidden" value="<?php echo $row['count']; ?>"
               class="stock-count">
        <input type="hidden" value="<?php echo $row['display_price']; ?>"
               class="stock-price">
        <?php echo $row['is_warranty'] ? 'Warranty: ' . $wrnty['warranty'] : ''; ?>
    </td>
    <td class="product-cart-price">
        <span class="amount">₹<?php echo $row['display_price']; ?></span>
    </td>
    <td class="cart-quality">
        <div class="product-quality">
            <input class="cart-plus-minus-box input-text qty text"
                   name="qtybutton"
                   is-warranty="<?php echo $row['is_warranty']; ?>"
                   product-uid="<?php echo $row['product_uid']; ?>"
                   vendor-uid="<?php echo $vendor_uid; ?>"
                   product-size-id="<?php echo $row['product_size_id']; ?>"
                   color-id="<?php echo $row['color_id']; ?>"
                   warranty-id="<?php echo $row['warranty_id']; ?>"
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
    </td>
</tr>
<?php if ($row['is_warranty']) {
    if ($wrnty['error']) { ?>
        <tr style="height: 20px"
            class="no-stock-available-tr no-stock-available-warranty-td cart-err-tr-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>-<?php echo $row['warranty_id']; ?>">
            <td colspan="5" style="padding: 8px 5px 5px 14px">
                <h6 class="d-flex flex-row align-items-center color-red">
                    <?php echo $wrnty['error']; ?>
                </h6>
            </td>
        </tr>
    <?php } else { ?>
        <tr style="height: 0"
            class="cart-err-tr-<?php echo $row['product_size_id']; ?>-<?php echo $row['color_id']; ?>-<?php echo $row['warranty_id']; ?>"></tr>
    <?php } ?>
<?php } ?>

