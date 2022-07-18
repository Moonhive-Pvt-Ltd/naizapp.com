<?php
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$order_uid = isset($_GET['uid']) ? $_GET['uid'] : '';

$order_rlt = mysqli_query($mysqli, "SELECT * FROM orders
                                          WHERE uid = '$order_uid'");

if (!mysqli_num_rows($order_rlt)) {
    echo("<script>location.href = './account';</script>");
    return;
}

$post = [
    'uid' => $user_uid,
    'order_uid' => $order_uid,
];
$url = BASE_URL . "get_order_item_list";
$result = getApiData($url, $post);

if ($result['status'] == 'Success') { ?>
    <div class="myaccount-table table-responsive text-center">
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Size</th>
                <th>Count</th>
                <th>Color</th>
                <th>Warranty</th>
                <th>Price</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach ($result['order_item'] as $row) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['size']; ?></td>
                    <td><?php echo $row['stock_count']; ?></td>
                    <td class="d-flex justify-content-center">
                        <?php if (isset($row['color_code']) && $row['color_code'] != '') { ?>
                            <div class="color-code-div"
                                 style="background-color: <?php echo $row['color_code']; ?>"></div>
                        <?php } else { ?>
                            <?php echo '-';
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($row['warranty']) && $row['warranty'] != '') {
                            echo $row['warranty'];
                        } else { ?>
                            <?php echo '-';
                        } ?>
                    </td>
                    <td>₹<?php echo $row['display_price']; ?></td>
                    <td><?php echo $row['status'] ? $row['status'] : '-'; ?></td>
                    <td>
                        <?php
                        $time = new DateTime($row['timestamp'], new DateTimeZone('UTC'));
                        $time->setTimezone(new DateTimezone('Asia/Kolkata'));
                        echo $time->format('d M, y');
                        ?>
                    </td>
                </tr>
                <?php $i++;
            } ?>
            </tbody>
        </table>
    </div>
<?php } ?>