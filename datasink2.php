<?php
// aggiornamento per Hooly 2 (atmega328 con rfm69hw e SHT3x)


include "db_connection.php";

if((isset($_GET['data'])) and (isset($_GET['rssi'])) and (isset($_GET['router']))){
  $data=$_GET['data'];
  $rssi=$_GET['rssi'];
  $router=$_GET['router'];
} else exit();

// echo $data;

list($serial,$counter,$temp,$hum,$battery,$period) = explode(":",$data);

$temp = $temp/100;
$hum = $hum/100;

//  3.3 / 1024 = 0.003222 (v per digit) = 3.222 mV
//  min = 3600/2/3.222 = 558 -> 0%
//  max = 4200/2/3.222 = 651 -> 100%
//  651 - 558 = 93 (range in digit)
//  93/100 = 1.075 (range in %)
$battery = intval(($battery - 558) * 1.075);
if ($battery > 100) $battery = 100;
if ($battery < 1)   $battery = 0;


$query = "DELETE from last_rec_data_2 where serial = '$serial'";
// echo $query . "\n";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO last_rec_data_2 (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";
// echo $query  . "\n";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO rec_data_2 (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";

// echo $query  . "\n";
$result = mysqli_query($conn,$query);

mysqli_close($conn);
?>
