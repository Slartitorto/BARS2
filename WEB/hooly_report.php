<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN']; }
else { $COD_UTENTE =	0; header("Location: index.php"); }

include "db_connection.php";
$mese = $_POST['mese'];
$anno = $_POST['anno'];
$ora_min = $_POST['ora'];
$ora_max = sprintf("%02d", $ora_min + 3);

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
?>

<head>
  <title>Hooly sensors</title>
  <link href="stile.css" rel="stylesheet" type="text/css" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">
  <script src="scripts/jquery-1.12.3.min.js"></script>
  <script src="scripts/jspdf.min.js"></script>
  <script src="scripts/jspdf.plugin.autotable.js"></script>

  <script type="text/javascript">
  function generate() {
    var doc = new jsPDF({orientation: 'landscape'});
    var pageContent = function (data) {
      // HEADER
      doc.setFontSize(10);
      doc.setTextColor(40);
      doc.setFontStyle('normal');
      doc.text("Report mensile: Mese <?php echo $mese_lit ?> - Anno: <?php echo $anno ?>", data.settings.margin.left + 15, 22);
      // FOOTER
      doc.setFontSize(8);
      doc.text("Report by Hooly", data.settings.margin.left, doc.internal.pageSize.height - 10);
    };
    var res = doc.autoTableHtmlToJson(document.getElementById("hooly_report"));
    doc.autoTable(res.columns, res.data, {
      tableWidth: 'wrap',
      theme: 'grid',
      styles: {cellPadding: 1.8, columnWidth:'wrap', fontSize: 5, halign: 'center'},
      addPageContent: pageContent,
      margin: {top: 30}
    });
    doc.save("hooly_report.pdf");
  }
</script>
</head>

<body>
  <BR>
    <center>
      <TABLE width="800">
        <TR>
          <TD align="left" width="90%">
            <button type="button" onclick="location.href='status.php';"class="imgbtn"><img src="icone/left37.png" width="35"></button>
          </TD>
          <TD align="right">
          </TD>
        </TR>
      </table>
      <BR> <BR> <BR>

        <div id="report">
          <h3>Report mensile Mese: <?php echo $mese_lit ?> - Anno: <?php echo $anno ?> - Ore: <?php echo $ora_min ?>:00</h3>
          <table class="hooly_report" id="hooly_report" border=1>

            <?php
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
            $x=0;
            while($row = $result->fetch_assoc()) {
              $serial[$x]=$row["serial"];
              $device_name[$x]=$row["device_name"];
              $position[$x]=$row["position"];
              ++$x;
            }
            print "<tr>\n";
            print "<th>Hooly</th>\n";
            for($i=1;$i<=$ngiorni;$i++)
            {
              $giorno = sprintf("%02d",$i);
              print "<th align = center>" . $giorno . "</th>\n";
            }
            print "</tr>\n";
            for ($i=0;$i<$x;$i++)
            {
              print "<tr>";
              print "<td width=200><b>" . $device_name[$i] . "<br>" . $position[$i] . "</b><br>(" . $serial[$i] . ")";
              print "<br></td>\n";
              for ($a=1;$a<=$ngiorni;$a++)
              {
                print "<td><b>";
                $giorno = sprintf("%02d",$a);
                $time_ref_min = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_min . ":00'";
                $time_ref_max = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_max . ":00'";

                $query = "select timestamp,temp from rec_data where serial = '$serial[$i]' and timestamp > $time_ref_min and timestamp < $time_ref_max limit 1";
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
          <button class="graybtn" onclick="generate();">PDF SA-04</button>
        </div>
      </body>
