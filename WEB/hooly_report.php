<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN']; }
else { $COD_UTENTE =	0; header("Location: index.php"); }

include "db_connection.php";
$mese = $_POST['mese'];
$anno = $_POST['anno'];
$serial = $_POST['serial'];
$ora_min[1] = $_POST['ora1'];
$ora_max[1] = sprintf("%02d", $ora_min[1] + 1);
$ora_min[2] = $_POST['ora2'];
$ora_max[2] = sprintf("%02d", $ora_min[2] + 1);
$ora_min[3] = $_POST['ora3'];
$ora_max[3] = sprintf("%02d", $ora_min[3] + 1);

if ($mese == "01") { $mese_lit = "Gennaio"; $ngiorni = 31;}
elseif ($mese == "02") { $mese_lit = "Febbraio"; $ngiorni = 28;}
elseif ($mese == "03") { $mese_lit = "Marzo"; $ngiorni = 31;}
elseif ($mese == "04") { $mese_lit = "Aprile"; $ngiorni = 30;}
elseif ($mese == "05") { $mese_lit = "Maggio"; $ngiorni = 31;}
elseif ($mese == "06") { $mese_lit = "Giugno"; $ngiorni = 30;}
elseif ($mese == "07") { $mese_lit = "Luglio"; $ngiorni = 31;}
elseif ($mese == "08") { $mese_lit = "Agosto"; $ngiorni = 31;}
elseif ($mese == "09") { $mese_lit = "Settembre"; $ngiorni = 30;}
elseif ($mese == "10") { $mese_lit = "Ottobre"; $ngiorni = 31;}
elseif ($mese == "11") { $mese_lit = "Novembre"; $ngiorni = 30;}
elseif ($mese == "12") { $mese_lit = "Dicembre"; $ngiorni = 31;}

$query = "SELECT device_name, position FROM devices where serial = '$serial'";
$result = $conn->query($query);
while($row = $result->fetch_assoc()) {
  $device_name=$row["device_name"];
  $position=$row["position"];
}

$query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
$result = $conn->query($query);
while($row = $result->fetch_assoc()) {
  $idUtente = $row["idUtente"];
  $tenant0 = $row["t0"];
  $tenant1 = $row["t1"];
  $tenant2 = $row["t2"];
  $tenant3 = $row["t3"];
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
?>

<head>
  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/dropDownMenu.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/report.css" rel="stylesheet" type="text/css" />

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">
  <script src="scripts/jquery-1.12.3.min.js"></script>
  <script src="scripts/jspdf.min.js"></script>
  <script src="scripts/jspdf.plugin.autotable.js"></script>
</head>

<body>
  <BR>
      <?php include 'includes/headerTableMenu.php'; ?>
      <BR> <BR>
        <div id="report">
          <table class="hooly_report" id="header_table" border=1>
            <tr>
              <td width="40%" rowspan=2>Mese di <b><?php echo $mese_lit ?></b><br>Anno <b><?php echo $anno ?></b>
                <br>Impianto: <b><?php echo $device_name . "  " . $position . " (seriale " . $serial . ")" ?></b></td>
                <td><center><b>Allegato Modello SA-04</b></center></td></tr>
                <tr>
                  <td>MODULO DI REGISTRAZIONE DELLE TEMPERATURE NEGATIVE<br>(MANTENIMENTO A TEMPERATURA CONTROLLATA)</td></tr>
                </table>
                <br><br>
                <table class="hooly_report" id="hooly_reportTemp" border=1>
                  <tr>
                    <th>Orario</th>
                    <?php
                    for($i=1;$i<=$ngiorni;$i++)
                    {
                      $giorno = sprintf("%02d",$i);
                      print "<th align = center>" . $giorno . "</th>\n";
                    }
                    print "</tr>\n";
                    for ($item = 1;$item < 4;$item ++) {

                      print "<tr>";
                      print "<td width=200><b>" . $ora_min[$item] . ":00<br>" . $ora_max[$item] . ":00</b><br>";
                      print "<br></td>\n";
                      for ($a=1;$a<=$ngiorni;$a++)
                      {
                        print "<td><b>";
                        $giorno = sprintf("%02d",$a);
                        $time_ref_min = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_min[$item] . ":00'";
                        $time_ref_max = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_max[$item] . ":00'";

                        $query = "select timestamp,temp from rec_data where serial = '$serial' and timestamp > $time_ref_min and timestamp < $time_ref_max limit 1";
                        $result = $conn->query($query);
                        while($row = $result->fetch_assoc()) {
                          $temperatura = number_format($row["temp"], 1);
                          echo $temperatura;
                          echo "°";
                          print "</b><br>";
                          $time= preg_split('/[ :]/',$row["timestamp"]);
                          echo($time[1]);
                          echo ":";
                          echo($time[2]);
                        }
                        print "</td>\n";
                      }
                      print "</tr>\n";
                    }
                    ?>
                  </table>

                  <br><br>
                  <table class="hooly_report" id="hooly_reportNC" border=1>
                    <tr><th></th>
                      <?php
                      for($i=1;$i<=15;$i++)
                      {
                        print "<th align = center>" . $i . "</th>\n";
                      }
                      ?>
                    </tr>

                    <?php
                    $query = "SELECT nc_date, nc_type, nc_ac FROM non_conformita where codUtente = '$COD_UTENTE' and serial = '$serial' and nc_date like '%$mese/$anno' order by nc_date";
                    $result = $conn->query($query);
                    $x=0;
                    while($row = $result->fetch_assoc()) {
                      $nc_date[$x]=$row["nc_date"];
                      $nc_type[$x]=$row["nc_type"];
                      $nc_ac[$x]=$row["nc_ac"];
                      ++$x;
                    }
                    for ($i=$x;$i<15;$i++) {
                      $nc_date[$i]="";
                      $nc_type[$i]="";
                      $nc_ac[$i]="";
                    }
                    echo "<tr><td>Data</td>"; for($i=0;$i<15;$i++)  { echo "<td>" . $nc_date[$i] . "</td>"; } echo "</tr>";
                    echo "<tr><td>Non conformità</td>"; for($i=0;$i<15;$i++)  { echo "<td>" . $nc_type[$i] . "</td>"; } echo "</tr>";
                    echo "<tr><td>Azione correttiva</td>"; for($i=0;$i<15;$i++)  { echo "<td>" . $nc_ac[$i] . "</td>"; } echo "</tr>";
                    ?>
                  </table>
                  <br>
                  <table class="hooly_report" id="hooly_reportNC" border=1>
                    <tr><td width="50%">
                      <b>Legenda Non Conformità</b>
                      <br>
                      A. Temperatura dell’apparecchio fuori limite ma temperatura degli alimenti entro i limiti
                      <br>
                      B. Temperatura dell’apparecchio e degli alimenti fuori limite
                    </td>
                    <td width="50%">
                      <b>Legenda Azioni correttive</b>
                      <br>
                      C. Trasferimento degli alimenti in altro apparecchio di riserva e riparazione dell’impianto
                      <br>
                      D. Eliminazione degli alimenti con temperatura superiore ai limiti e riparazione dell’impianto
                      <br>
                      E. Immediato impiego dei prodotti e riparazione dell’impianto</td></tr>
                    </table>
                    <br><br>
                    <table class="hooly_report hide-print">
                      <tr>
                        <td width="33%"></td>
                        <td width="34%" align="center"><button class="graybtn" onClick="window.print()">Stampa</button> </td>

                        <td width="33%" align="right">
                          <form action=hooly_report.php method="POST">
                            Passa a:  <select name="serial" onchange="this.form.submit()">
                              <?php for($i=0;$i<$serial_qty;$i++) {
                                echo "<option value= \"$serial_array[$i]\"";
                                if ($serial_array[$i] == $serial) { echo " selected";}
                                echo ">$device_name_array[$i] - $position_array[$i]</option>\n";
                              }
                              echo "</select>";
                              echo "<input type=hidden name=mese value=" . $mese . ">";
                              echo "<input type=hidden name=anno value=" . $anno . ">";
                              echo "<input type=hidden name=ora1 value=" . $ora_min[1] . ">";
                              echo "<input type=hidden name=ora2 value=" . $ora_min[2] . ">";
                              echo "<input type=hidden name=ora3 value=" . $ora_min[3] . ">";
                              ?>
                            </form>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </body>
