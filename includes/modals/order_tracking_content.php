<?php
$orderid = $_GET['key1']; 
$shiprocketorderid = $_GET['key2']; 
require_once("../functions.php");

$pagenum = isset($_GET['page']) ? $_GET['page'] : 1;
$user_uid = isset($_COOKIE['naiz_web_user_uid']) ? $_COOKIE['naiz_web_user_uid'] : '';
$total_pages = 0;
$post = [
    'orderid' => $orderid,
    'shiprocketorderid' => $shiprocketorderid,
    'page' => $pagenum,
];  
$url = BASE_URL ."trackLogisticsOrder";
$response = getApiData($url, $post);  
// print_r($post);
// print_R($response); 
?>

 
<div class="modal-content" style="background-color: #e2d8d2">
<div class="modal-header">
    <h5 class="modal-title"></h5>
    <button type="button" class="close on-close-landing-modal-click" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body modal-scroll">
    <div class="px-4 pb-5" style="display:block;">
        <div class="row">
            <div class="mt-3">
                <h4 class="font-size-30px">Tracking Order #<?php echo $_GET['key2']; ?> </h3>
                

                    <?php 
                   if (!empty($response['msg'])) { $data = $response['msg']; } else { 
                    // Handle decoding failure
                    echo "The shipment is getting ready. Please wait for sometime.";
                } 
                  // Check if decoding was successful
                  if (!empty($data['tracking_data'])) {
                      // Access the "items" array
                      $shipment_track_activities = $data['tracking_data']['shipment_track_activities'];
                      $track_url = $data['tracking_data']['track_url']; 
                        
                      echo "<ul class='text-left list-group'>"; 
                        // Loop through each item and print its details
                        foreach ($shipment_track_activities as $item) { 
              
                          echo "<li class='list-group-item'>";
                          echo "date: " . $item['date'] . " <br> status: " . $item['status'] . "<br>";
                          echo "activity: " . $item['activity'] . "<br> location: " . $item['location'] . "<br>"; 
                          //echo "sr-status: " . $item['sr-status'] . "<br>";
                          echo "</li>" ;
                      } 
                      echo "<li class='list-group-item'><a target='blank' href='".$track_url."'><u>click to know more</u> </a><li>";
                      echo "</ul>";
                  } 
                    ?>
              
            </div>
        </div>
    </div>
</div>
</div>

<style>
button.close {
    padding: 0;
    cursor: pointer;
    background: 0 0;
    border: 0;
    -webkit-appearance: none;
}

.close {
    float: right;
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: .5;
}
</style>
 
