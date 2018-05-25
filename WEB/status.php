<?php

if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];}
else { $COD_UTENTE =	0; header("Location: index.php");}
?>

<head>
  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/dropDownMenu.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <link rel="icon" href="/icone/temp_icon.png">

  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/jquery-ui.js"></script>
  <script src="scripts/datePickerLocalized.js"></script>

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
</head>

<body>
  <?php include 'includes/headerTableMenu.php'; ?>

  <BR>
    <div class="modal-content animate status">
      <br><br>
      <table class="centered status">
        <tr><th>Dettagli</th><th>Termometro</th><th>Posizione</th><th>Temp</th><th>Stato</th></tr>

        <?php
        include "dbactions/db_connection.php";

        $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
        $result = $conn->query($query);
        while($row = $result->fetch_assoc()) {
          $idUtente = $row["idUtente"];
          $tenant0 = $row["t0"];
          $tenant1 = $row["t1"];
          $tenant2 = $row["t2"];
          $tenant3 = $row["t3"];
        }

        $query = "SELECT serial, device_name, position, batt_type, min_ok, max_ok FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3)";
        $result = $conn->query($query);
        $x=0;
        while($row = $result->fetch_assoc()) {
          $serial[$x]=$row["serial"];
          $device_name[$x]=$row["device_name"];
          $position[$x]=$row["position"];
          $batt_type[$x]=$row["batt_type"];
          $min_ok[$x]=$row["min_ok"];
          $max_ok[$x]=$row["max_ok"];
          ++$x;
        }

        $count=count($serial);
        for($i=0;$i<$count;$i++) {
          $query = "select temp,hum,counter,battery,period,rssi,timestampdiff(second,timestamp,now()) as sec_delay from last_rec_data where serial = '$serial[$i]' order by timestamp desc limit 1";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $last_temp[$i]=$row["temp"];
            $last_hum[$i]=$row["hum"];
            $last_rssi[$i]=$row["rssi"];
            $sec_delay[$i]=$row["sec_delay"];
            $last_battery[$i]=$row["battery"];
            $period[$i]=$row["period"];
            $link_qlt0[$i]=$row["counter"];
          }

          if ($last_battery[$i] < 10) {
            $warn[$i] = "battery_low";
            $tooltipText[$i] = "Attenzione! Batteria scarica";
          }
          else if ($sec_delay[$i] > 5 * $period[$i]) {
            $warn[$i] = "link";
            $tooltipText[$i] = "Attenzione! Problemi di collegamento: segnale non rilevato da più di " . 5 * $period[$i] . " secondi";
          }
          else if ($last_rssi[$i] < 10) {
            $warn[$i] = "link";
            $tooltipText[$i] = "Attenzione! Segnale radio basso";
          }
          else if ($last_temp[$i] < $min_ok[$i] or $last_temp[$i] > $max_ok[$i]) {
            $warn[$i] = "red";
            $tooltipText[$i] = "Allarme: temperatura fuori range";
          }
          else {
            $warn[$i] = "green";
            $tooltipText[$i] = "Sensore e temperatura OK";
          }

          echo "<TR>";
          echo "<form action=device_details.php method=post>";
          echo "<TD>";

          echo "<button type=submit class=imgbtn> <img src=icone/grafico.png height=\"30\" width=\"30\"></button>";
          echo "<input type=hidden name=serial value=$serial[$i] > ";
          echo "<input type=hidden name=last value=1 > ";
          echo "<input type=hidden name=graph value=temp > ";

          echo "</form>";
          echo "</TD><TD>" . $device_name[$i] . "</TD>";
          echo "</TD><TD>" . $position[$i] . "</TD>";
          echo "<TD>" . round($last_temp[$i],1) . "</TD>";
          echo "<TD><div class=\"tooltip\"><img src=\"icone/" . $warn[$i] . "_signal.png\" width=\"25\"> <span class=\"tooltiptext\">" . $tooltipText[$i] . "</span></TD>";
          echo "</TR>\n";
        }

        $conn->close();
        ?>

      </table>
      <br><br>
    </div>
  </body>
