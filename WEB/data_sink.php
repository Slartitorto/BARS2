<?php
// aggiornamento per Hooly 2 (atmega328 con rfm69hw e SHT3x)


include "db_connection.php";

if((isset($_GET['data'])) and (isset($_GET['rssi'])) and (isset($_GET['router']))){
  $data=$_GET['data'];
  $rssi=$_GET['rssi'];
  $router=$_GET['router'];
} else exit();

list($serial, $counter, $temp, $hum, $battery, $period) = explode(":",$data);

$temp = $temp/100;
$hum = $hum/100;

//  3.3 / 1024 = 0.003222 (v per digit) = 3.222 mV
//  min = 3600/2/3.222 = 558 -> 0%
//  verified 575 -> 0%
//  max = 4200/2/3.222 = 651 -> 100%
//  verified 651 -> 100%
//  651 - 575 = 76 (range in digit)
//  100/76 = 1.316 (range in %)
//  so:
$batt_0 = 575;
$batt_corr = 1.316;

$battery = intval(($battery - $batt_0) * $batt_corr);
if ($battery > 100) $battery = 100;
if ($battery < 1)   $battery = 0;

$query = "DELETE from last_rec_data_2 where serial = '$serial'";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO last_rec_data_2 (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO rec_data_2 (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";
$result = mysqli_query($conn,$query);

$query = "SELECT armed, batt_alarmed, alarmed, min_ok, max_ok, device_name, position, tenant from devices where serial = '$serial'";
$result = mysqli_query($conn,$query);
$row = mysqli_fetch_array($result);

$armed = $row[0];
$batt_alarmed = $row[1];
$alarmed = $row[2];
$min_ok = intval($row[3]);
$max_ok = intval($row[4]);
$device_name = $row[5];
$position = $row[6];
$tenant = $row[7];

if ($batt_alarmed == 1) {
  // sensor was off because battery low; now is alive: reset batt_alarmed flag
  $query = "UPDATE devices SET batt_alarmed = 0 WHERE serial = '$serial' ";
  $result = mysqli_query($conn,$query);
}

if (($data < $min_ok) or ($data > $max_ok)) {
  // alarm condition half
  if (($armed == 1) and ($alarmed == 0)) {
    //alarm condition full
    $query = "UPDATE devices SET alarmed = 1 WHERE serial = '$serial'";
    $result = mysqli_query($conn,$query);

    $subject = "Allarme $device_name $position";
    $message = "Temperatura rilevata = $data - out of range (min = $min_ok - max = $max_ok)";
    $headers = "From: root@slartitorto.eu \r\n" .
    "Reply-To: root@slartitorto.eu \r\n";

    $query = "SELECT email FROM utenti WHERE t0 = '$tenant' OR t1 = '$tenant' OR t2 = '$tenant' OR t3 = '$tenant'";
    $result = mysqli_query($conn,$query);
    while (($row = mysqli_fetch_row($result))) {
      $to = $row[0];
      mail($to, $subject, $message, $headers);

      // CURL pushbullett (see https://wiki.onion.io/Tutorials/PHP-PushBullet-Example)
      $authToken = "YOUR_PUSHBULLETT_TOKEN";
      $curl = curl_init('https://api.pushbullet.com/v2/pushes');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $authToken"]);
      curl_setopt($curl, CURLOPT_POSTFIELDS, [
        "type" => "note", "email" => "$to", "title" => "Allarme $device_name $position",
        "body" => "Temperatura rilevata = $data - out of range (min = $min_ok - max = $max_ok)"]
      );
      curl_exec($curl);
      curl_close($curl);

    }
  }
} else {
  // If previously alarmed, reset alarm flag
  if ($alarmed == 1) {
    $query = "UPDATE devices SET alarmed = 0 WHERE serial = '$serial'";
    $result = mysqli_query($conn,$query);
  }
}

mysqli_close($conn);
?>
