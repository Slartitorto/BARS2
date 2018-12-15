<?php
include "dbactions/db_connection.php";
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE = $_COOKIE['LOGIN']; }
else { $COD_UTENTE = 0; header("Location: index.php"); }
?>

<html lang="en-US">
<head>
  <title>Sensor details</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="apple-touch-icon" href="/icone/app_icon128.png">

  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/info_popup_modal.css" rel="stylesheet" type="text/css" />


  <?php
  $PDR_tooltip_txt = "Intervallo di tempo tra un rilevamento e l'altro";
  $ULA_tooltip_txt = "Tempo trascorso dall'ultimo rilevamento";
  $QDC_tooltip_txt = "Percentuale delle trasmissioni ricevute rispetto alle ultime 10 trasmissioni attese";
  $SER_tooltip_txt = "Forza del segnale radio rilevata dal ricevutore durante l'ultima trasmissione";

  if(isset($_POST["serial"])) { $serial=($_POST["serial"]);}
  if(isset($_POST["last"])) { $last=($_POST["last"]);}
  if(isset($_POST["graph"])) { $graph=$_POST['graph'];}

  if(isset($_GET["serial"])) { $serial=($_GET["serial"]);}
  if(isset($_GET["last"])) { $last=($_GET["last"]);}
  if(isset($_GET["graph"])) { $graph=$_GET['graph'];}

  $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $idUtente = $row["idUtente"];
    $tenant0 = $row["t0"];
    $tenant1 = $row["t1"];
    $tenant2 = $row["t2"];
    $tenant3 = $row["t3"];
  }

  function format_time($t,$f=':') // t = seconds, f = separator
  {
    return sprintf("%3d%s%02d", ($t/60) , $f, $t%60);
  }

  $query = "SELECT serial, device_name, position FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3)";
  $result = $conn->query($query);
  $serial_qty=0;
  while($row = $result->fetch_assoc()) {
    $serial_array[$serial_qty]=$row["serial"];
    $device_name_array[$serial_qty]=$row["device_name"];
    $position_array[$serial_qty]=$row["position"];
    ++$serial_qty;
  }

  $sql = "SELECT device_name, position, min_ok, max_ok, batt_type FROM devices where serial = '$serial'" ;
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $device_name = $row["device_name"];
      $position = $row["position"];
      $batt_type = $row["batt_type"];
      $min_ok=$row["min_ok"];
      $max_ok=$row["max_ok"];
    }
  }

  // SELECT last record
  $sql = "SELECT timestamp, temp, hum, battery, period, rssi, timestampdiff(second,timestamp,now()) as sec_delay FROM last_rec_data where serial = '$serial' order by timestamp desc limit 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $last_temp = $row["temp"];
      $last_hum = $row["hum"];  //not yet used
      $last_bat = $row["battery"];
      $last_period = $row["period"];
      $last_rssi = $row["rssi"];
      $min_period = format_time($last_period);
      $sec_delay=$row["sec_delay"];
      $min_delay=format_time($sec_delay);
    }

    // SELECT last counter
    $query = "select counter from last_rec_data where serial = '$serial' order by timestamp desc limit 1";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $link_qlt0=$row["counter"];
    }
    // SELECT last counter -10
    $query = "select counter from rec_data where serial = '$serial' order by timestamp desc limit 10,1";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $link_qlt1=$row["counter"];
    }
    // Link quality depends by how packets lost between last 10 received and how many not received from last one to now
    $link_qlt = intval(1000/($link_qlt0 - $link_qlt1)-($sec_delay/$last_period)*10);
    if ($link_qlt < 0) $link_qlt = 0;

    if ($graph == "temp") {
      $query = "SELECT unix_timestamp(timestamp) as timestamp, temp as data FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $sql_csv = "SELECT timestamp, counter, temp FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $header_csv = "temperatura";
      $Yaxis_name = "Temperature (C)";
    } else if ($graph == "batt") {
      $query = "SELECT unix_timestamp(timestamp) as timestamp, battery as data FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $sql_csv = "SELECT timestamp, counter, battery FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $header_csv = "batteria";
      $Yaxis_name = "Livello batteria %";
      $min_ok = $last_bat -5; if ($min_ok < 0) $min_ok = 0.2;
      $max_ok = $last_bat +5; if ($max_ok > 100) $max_ok = 100.2;
    } else if ($graph == "hum") {
      $query = "SELECT unix_timestamp(timestamp) as timestamp, hum as data FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $sql_csv = "SELECT timestamp, counter, hum FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $header_csv = "umidita";
      $Yaxis_name = "Umidita (%)";
      $min_ok=0;
      $max_ok=100;
    } else if ($graph == "rssi") {
      $query = "SELECT unix_timestamp(timestamp) as timestamp, rssi as data FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $sql_csv = "SELECT timestamp, counter, rssi FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
      $header_csv = "segnale";
      $Yaxis_name = "Forza del segnale";
      $min_ok=0;
      $max_ok=100;
    }

    $result = $conn->query($query);
    while ($row = $result->fetch_array()) {
      $timestamp = $row['timestamp'];
      $timestamp *=1000;
      $data = $row['data'];

      $data1[] = "[$timestamp, $data]";
      $data2[] = "[$timestamp, $min_ok]";
      $data3[] = "[$timestamp, $max_ok]";
    }
    ?>
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/highcharts.js"></script>
    <script>
    $(function () {
      Highcharts.setOptions({
        global: {
          useUTC: false
        }
      });
      $('#container1').highcharts({
        chart: {
          margingLeft: 50,
          margingRight: 50,
          margingTop: 50,
          marginBottom: 50,
          type: 'line'
        },
        title: {
          text: ''
        },
        legend: {
          enabled: false
        },
        xAxis: {
          type: 'datetime',
        },
        yAxis: {
          title: {
            text: '<?php echo $Yaxis_name; ?>'
          },
        },
        series: [{
          data: [<?php echo join($data1, ',') ;?>]
        },{
          color:'#<?php if ($graph == "batt") echo "ffffff"; else echo "ff0000" ;?>',
          enableMouseTracking: false,
          data: [<?php echo join($data2, ',') ;?>]
        },{
          color:'#<?php if ($graph == "batt") echo "ffffff"; else echo "ff0000" ;?>',
          enableMouseTracking: false,
          data: [<?php echo join($data3, ',') ;?>]
        }]
      });
    });
    </script>

  </head>
  <body>
    <?php include 'includes/headerTableMenu.php'; ?>
    <BR>
      <center>
        <div class="modal-content" style="width:900px;">
          <table class="padded centered device_details">
            <tr>
              <th  style="vertical-align:middle" width="50%"><button id="myBtn" style="padding:5px 5px"; class="hide-print"><img src="icone/info_icon.png" height="20" width="20"></button><?php echo $device_name?></th>
              <th  style="vertical-align:middle" width="50%"><?php echo $position ?></th></tr>

              <tr>
              <th colspan=2><font style="font-size:150%">Temp: <?php echo round($last_temp,2)?> &deg C</font></th>
            </tr>
          </table>
          <br><br>


          <div id="myModal" class="info_pupup_modal">
            <div class="info_pupup_modal-content">
              <span class="close">&times;</span>
              <table width="100%">
                <tr>
                  <TD>
                    Numero seriale del sensore: <B><?php echo $serial ?></B>
                  </TD>
                  <TD>
                    Batteria: <B><?php  if ($last_bat < 5) {echo "<font color=\"red\">";} echo $last_bat ?>%</B>
                    </TD>
                  </tr>
                  <tr>
                    <TD>
                      <div class="tooltip">Periodo di rilevamento (min.): <span class="tooltiptext"><?php echo $PDR_tooltip_txt ?></span></div><B><?php echo $min_period ?></B>
                    </td>
                    <TD>
                      <div class="tooltip">Ultimo aggiornamento (min.): <span class="tooltiptext"><?php echo $ULA_tooltip_txt ?></span></div><B><?php echo $min_delay ?></B>
                    </td>
                  </TR>
                  <TR>
                    <TD>
                      <div class="tooltip">Qualità del collegamento: <span class="tooltiptext"><?php echo $QDC_tooltip_txt ?></span></div><B><?php echo $link_qlt ?>%</B>
                    </td>
                    <TD>
                      <div class="tooltip">Segnale radio (ultimo ricevuto): <span class="tooltiptext"><?php echo $SER_tooltip_txt ?></span>
                      </div><B><?php if ($last_rssi < 5) {echo "<font color=\"red\">";} echo $last_rssi ?>%</B>
                      </td>
                    </TR>
                  </table>
                </div>
              </div>

              <script>
              var modal = document.getElementById('myModal');
              var btn = document.getElementById("myBtn");
              var span = document.getElementsByClassName("close")[0];
              btn.onclick = function() {
                modal.style.display = "block";
              }
              span.onclick = function() {
                modal.style.display = "none";
              }
              window.onclick = function(event) {
                if (event.target == modal) {
                  modal.style.display = "none";
                }
              }
              </script>


              <table width=100% class="hide-print">
                <tr>
                  <td width="33%" align="left">
                    <form action = "<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                      <select name="graph" onchange="this.form.submit()">
                        <option value= "temp" <?php if ($graph == "temp") { echo " selected";} ?>> Temperatura</option>
                        <option value= "hum" <?php if ($graph == "hum") { echo " selected";} ?>> Umidità</option>
                        <option value= "rssi" <?php if ($graph == "rssi") { echo " selected";} ?>> Forza del segnale</option>
                        <option value= "batt" <?php if ($graph == "batt") { echo " selected";} ?>> Livello batteria</option>
                      </select>
                      <input type="hidden" name="serial" value="<?php echo $serial; ?>">
                      <input type=hidden name=last value="<?php echo $last; ?>">
                    </form>
                  </td>

                  <td width="34%" align="center">
                    <form action = "<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                      <select name="last" onchange="this.form.submit()">
                        <option value= "1" <?php if ($last == 1) { echo " selected";} ?>> Ultime 24 ore</option>
                        <option value= "2" <?php if ($last == 2) { echo " selected";} ?>> Ultime 48 ore</option>
                        <option value= "7" <?php if ($last == 7) { echo " selected";} ?>> Ultima settimana</option>
                        <option value= "30" <?php if ($last == 30) { echo " selected";} ?>> Ultimo mese</option>
                      </select>
                      <input type="hidden" name="serial" value="<?php echo $serial?>">
                      <input type="hidden" name="graph" value="<?php echo $graph?>">
                    </form>
                  </td>

                  <td width="33%" align="right">
                    <form action = "<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                      <select name="serial" onchange="this.form.submit()">
                        <?php
                        for($i=0;$i<$serial_qty;$i++) {
                          echo "<option value= \"$serial_array[$i]\"";
                          if ($serial_array[$i] == $serial) { echo " selected";}
                          echo ">$device_name_array[$i] - $position_array[$i]</option>\n";
                        }
                        ?>
                      </select>
                      <input type=hidden name=last value="<?php echo $last; ?>" >
                      <input type=hidden name=graph value="<?php echo $graph ?>" >
                    </form>
                  </td>
                </tr>
              </table>
              <div id="container1" style="width:100%; height:380px;"></div>
              <div class="hide-print">
                <script>
                var data = [ <?php
                // https://code-maven.com/create-and-download-csv-with-javascript

                $result = $conn->query($sql_csv);
                while ($row = $result->fetch_array()) {
                  echo "['" . $row[0] . "','" . $row[1] . "','" . $row[2] . "'],";
                }
                ?> ];

                function download_csv() {
                  var csv = 'Timestamp,Count,<?php echo $header_csv; ?>\n';
                  data.forEach(function(row) {
                    csv += row.join(',');
                    csv += "\n";
                  });

                  console.log(csv);
                  var hiddenElement = document.createElement('a');
                  hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                  hiddenElement.download = 'data.csv';
                  hiddenElement.click();
                }
                </script>

                <button class=graybtn onclick="download_csv()">Download CSV</button>
                <button class=graybtn onClick="window.print()">Stampa</button>
              </div>

            <?php } else { ?>
              <div class="modal-content animate">
                <h2>Qualcosa non va</h2>
                <br><br>
                Nessun record trovato. Probabilmente il tuo Hooly non è stato ancora acceso.
                <br><br>
                Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
                <button type="button" onclick="location.href='status.php';" class="greenbtn centeredbtn">Torna indietro</button>
              </div>

            <?php }  $conn->close();  ?>
          </div>
        </center>
      </body>
      </html>
