<?php
require_once("../functions.php");

$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$total_pages = 0;
$post = [
    'uid' => $user_uid,
    'page' => $pagenum,
];
$url = BASE_URL . "get_order_list";
$result = getApiData($url, $post);
if ($result['status'] == 'Success') {
    $total_pages = $result['total_pages'];
//    print_r($result['orders']);
}
?>

<div class="myaccount-table table-responsive text-center">
    <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th>Order</th>
            <th>Shop</th>
            <th>Status</th>
            <th>Type</th>
            <th>Date</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = (($pagenum - 1) * 15) + 1;
        foreach ($result['orders'] as $row) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['place']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php echo $row['order_type']; ?>
                </td>
                <td>
                    <?php
                    $time = new DateTime($row['timestamp'], new DateTimeZone('UTC'));
                    $time->setTimezone(new DateTimezone('Asia/Kolkata'));
                    echo $time->format('d M, y');
                    ?>
                </td>
                <td>₹<?php echo $row['total_cost']; ?></td>
                <td><a href="order_history?uid=<?php echo $row['uid']; ?>" class="check-btn sqr-btn" target="_blank">View</a>
                </td>
            </tr>
            <?php $i++;
        } ?>
        </tbody>
    </table>
</div>
<footer class="pagination-footer" type-id="orderTableContent" scroll-id="orderTableContent">
    <?php include 'pagination.php'; ?>
</footer>