<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN']; }
else { $COD_UTENTE =	0; header("Location: index.php"); }

include "db_connection.php";
$mese = $_POST['mese'];
$anno = $_POST['anno'];
$ora_min = $_POST['ora'];
$ora_max = sprintf("%02d", $ora_min + 2);

if ($mese == "01") { $ngiorni = 31;}
elseif ($mese == "02") { $ngiorni = 28;}
elseif ($mese == "03") { $ngiorni = 31;}
elseif ($mese == "04") { $ngiorni = 30;}
elseif ($mese == "05") { $ngiorni = 31;}
elseif ($mese == "06") { $ngiorni = 30;}
elseif ($mese == "07") { $ngiorni = 31;}
elseif ($mese == "08") { $ngiorni = 31;}
elseif ($mese == "09") { $ngiorni = 30;}
elseif ($mese == "10") { $ngiorni = 31;}
elseif ($mese == "11") { $ngiorni = 30;}
elseif ($mese == "12") { $ngiorni = 31;}

?>
<head>
  <title>Hooly sensors</title>
  <link href="stile.css" rel="stylesheet" type="text/css" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">

</head>
<body>

  <script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>

  <button onclick="javascript:downloadPDF();">PDF</button>

  <script type="text/javascript">
  function downloadPDF() {
    var pdf = new jsPDF('p', 'pt', 'a4');
    source = $('#report')[0];

    specialElementHandlers = {
      '#bypassme': function (element, renderer) {
        return true
      }
    };
    margins = {
      top: 80,
      bottom: 60,
      left: 10,
      width: 700
    };
    pdf.fromHTML(
      source,
      margins.left,
      margins.top, {
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
      },

      function (dispose) {
        pdf.save('Hooly_report.pdf');
      }, margins);
    }
    </script>


    <div id="report">
      <div class="table-responsive">


        <?php
        print " <h3> <center> Report mensile Mese: " . $mese . " - Anno: " . $anno . " - Ore: " . $ora_min . ":00</h3>\n";
        print "<center>\n";
        print "<table id=\"hooly_report\">\n";

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
        print "<th>giorno</th>\n";
        for($i=0;$i<$x;$i++)
        { print "<th align = center>" . $serial[$i] . "<br>" . $device_name[$i] . "<br>" . $position[$i] . "</th>\n"; }
        print "</tr>\n";
        for ($i=1;$i<=$ngiorni;$i++)
        {
          $giorno = sprintf("%02d",$i);
          print "<tr>";
          print "<td align=center><b>";
          echo $giorno;
          print "</b></td>\n";
          $time_ref_min = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_min . ":00'";
          $time_ref_max = "'" . $anno . "-" . $mese . "-" . $giorno . " 23:59:00'";
          for ($a=0;$a<$x;$a++)
          {
            print "<td align=center width=100><b>";
            $query = "select timestamp,data from rec_data where serial = '$serial[$a]' and timestamp > $time_ref_min and timestamp < $time_ref_max limit 1";
            $result = $conn->query($query);
            while($row = $result->fetch_assoc()) {
              $temperatura = number_format($row["data"], 1);
              echo $temperatura;
              echo "°";
              print "</b><br>";
              $time= explode(" ",$row["timestamp"]);
              echo $time[1];
            }
            print "</td>\n";
          }
          print "</tr>\n";
        }
        ?>
      </table>
    </div>
  </div>
  <a href=status.php>Indietro</a>
