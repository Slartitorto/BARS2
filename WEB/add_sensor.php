<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN']; }
else { $COD_UTENTE =	0; header("Location: index.php"); }
include "db_connection.php";
?>

<head><title>Add Sensor</title>
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="/icone/app_icon128.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="modal-content animate">

    <?php
    $idUtente = $_POST['idUtente'];
    $tenant = $_POST['tenant'];
    $serial = $_POST['serial'];
    $pin = $_POST['pin'];

    $query = "SELECT pin FROM new_devices WHERE serial='$serial'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $pin_trovato = $row["pin"];
      }
      if ($pin == $pin_trovato) {

        $query = "delete from new_devices WHERE serial='$serial'";
        $result = $conn->query($query);
        $query = "insert into devices (serial,device_name,position,armed,batt_alarmed,alarmed,min_ok,max_ok,batt_type,tenant,code_period) values ('$serial','new_device_$serial','position',0,0,0,10,30,'litio','$tenant',6) ";
        $result = $conn->query($query);
        echo "Operazione effettuata con successo<br><br>";
      }
      else
      { ?>
        Hoops, c'e' stato un errore:<br><br>
        Pin errato<br>
      <?php }
    }
    else
    { ?>
      Hoops, c'e' stato un errore:<br><br>
      Il seriale non esiste<br>
    <?php } ?>

    <button type="button" onclick="location.href='device_settings.php';" class="greenbtn centeredbtn">Torna indietro</button>
  </div>
