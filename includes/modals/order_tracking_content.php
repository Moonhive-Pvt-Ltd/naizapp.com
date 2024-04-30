<?php
$order_id = $_GET['key1']; 

const SHIPROCKET_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjQ2MjYwMTIsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzE0OTI2NjAyLCJqdGkiOiJQOFh0dmhUQ2NmNHhWaTV5IiwiaWF0IjoxNzE0MDYyNjAyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTcxNDA2MjYwMiwiY2lkIjo0NDUwOTM0LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.mZsesR3wTWDyR4ZWVqWDyoIIPQd4RVL2W2Q1zCv2Q-g';

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
                <h3 class="font-size-30px">Tracking Order #<?php echo $_GET['key2']; ?> </h3>
                

                    <?php
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/track?order_id='.$order_id.'&channel_id=',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                      CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.SHIPROCKET_TOKEN
                      ),
                    ));
                    
                    $response = curl_exec($curl);
                    
                    curl_close($curl);
                    $json_response = '{
                        "tracking_data": {
                          "track_status": 1,
                          "shipment_status": 42,
                          "shipment_track": [
                            {
                              "id": 185584215,
                              "awb_code": "1091188857722",
                              "courier_company_id": 10,
                              "shipment_id": 168347943,
                              "order_id": 168807908,
                              "pickup_date": null,
                              "delivered_date": null,
                              "weight": "0.10",
                              "packages": 1,
                              "current_status": "PICKED UP",
                              "delivered_to": "Mumbai",
                              "destination": "Mumbai",
                              "consignee_name": "Musarrat",
                              "origin": "PALWAL",
                              "courier_agent_details": null,
                              "edd": "2021-12-27 23:23:18"
                            }
                          ],
                          "shipment_track_activities": [
                            {
                              "date": "2021-12-23 14:23:18",
                              "status": "X-PPOM",
                              "activity": "In Transit - Shipment picked up",
                              "location": "Palwal_NewColony_D (Haryana)",
                              "sr-status": "42"
                            },
                            {
                              "date": "2021-12-23 14:19:37",
                              "status": "FMPUR-101",
                              "activity": "Manifested - Pickup scheduled",
                              "location": "Palwal_NewColony_D (Haryana)",
                              "sr-status": "NA"
                            },
                            {
                              "date": "2021-12-23 14:19:34",
                              "status": "X-UCI",
                              "activity": "Manifested - Consignment Manifested",
                              "location": "Palwal_NewColony_D (Haryana)",
                              "sr-status": "5"
                            }
                          ],
                          "track_url": "https://shiprocket.co//tracking/1091188857722",
                          "etd": "2021-12-28 10:19:35"
                        }
                      }'; 
              
                  curl_close($curl);
                  // Decode the JSON response
                  $data = json_decode($response, true);
                      //print_R($data);
                  // Check if decoding was successful
                  if (!empty($data)) {
                      // Access the "items" array
                      $shipment_track = $data['tracking_data']['shipment_track'];
                      $shipment_track_activities = $data['tracking_data']['shipment_track_activities']; 
                       
              
                      echo "<b>Tracking Activities</b>"."<br><ul class='text-left list-group'>"; 
                        // Loop through each item and print its details
                        foreach ($shipment_track_activities as $item) { 
              
                          echo "<li class='list-group-item'>";
                          echo "date: " . $item['date'] . " <br> status: " . $item['status'] . "<br>";
                          echo "activity: " . $item['activity'] . "<br> location: " . $item['location'] . "<br>"; 
                          echo "sr-status: " . $item['sr-status'] . "<br>";
                          echo "</li>" ;
                      } 
                      echo "</ul>";
                      echo "<br><b>Shipment Details</b><br><ul class='text-left list-group'>"; 
              
                      // Loop through each item and print its details
                      foreach ($shipment_track as $item) {
                          //echo "<li class='list-group-item'>ID: " . $item['id'] . " | Order_id: " . $item['order_id'] . " | ";
                          //echo "awb_code: " . $item['awb_code'] . " | shipment_id: " . $item['shipment_id'] . "</li>";
                          echo "<li class='list-group-item'>pickup date: " . $item['pickup_date'] . " |  delivered date: " . $item['delivered_date'] . " | ";
                          echo "weight: " . $item['weight'] . " |  packages: " . $item['packages'] . "</li>";
                          echo "<li class='list-group-item'>current status: " . $item['current_status'] . " |  delivered to: " . $item['delivered_to'] . "</li>"; 
                          echo "<li class='list-group-item'>destination: " . $item['destination'] . " |  consignee name: " . $item['consignee_name'] . "</li>"; 
                          echo "<li class='list-group-item'>origin: " . $item['origin'] . " |  courier_agent_details: " . $item['courier_agent_details'] . "</li>";
                         // echo "<li class='list-group-item'>edd: " . $item['edd'] . "</li>"; 
                      }
                  } else {
                      // Handle decoding failure
                      echo "Order is Getting Ready to Ship.";
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
 
