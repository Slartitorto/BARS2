<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];}
else { $COD_UTENTE =	0; header("Location: index.php");}
if(isset($_POST["mese"])) { $mese=($_POST["mese"]);}
if(isset($_POST["anno"])) { $anno=($_POST["anno"]);}
if(isset($_GET["mese"])) { $mese=($_GET["mese"]);}
if(isset($_GET["anno"])) { $anno=($_GET["anno"]);}
?>

<head>
  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">
  <SCRIPT type="text/javascript"> function navigator_Go(url) { window.location.assign(url);} </SCRIPT>
  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/jquery-ui.js"></script>

  <script>
  $(document).ready(function(){
    $("#btn1").click(function(){ $("#advanced_preferences").toggle(1000); });
  });
</script>
<script>
$(function() {
  $.datepicker.regional['it'] = {
    closeText: 'Chiudi', // set a close button text
    currentText: 'Oggi', // set today text
    monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',   'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'], // set month names
    monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'], // set short month names
    dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'], // set days names
    dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'], // set short day names
    dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'], // set more short days names
    dateFormat: 'dd/mm/yy' // set format date
  };
  $.datepicker.setDefaults($.datepicker.regional['it']);
  $("#datepicker").datepicker();
  $("#datepicker").datepicker('setDate', new Date());
});
</script>
</head>

<body>
  <BR>
    <center>
      <TABLE width="50%">
        <TR>
          <TD align="left"><A href="javascript:navigator_Go('device_settings.php');"><img src="icone/very-basic-settings-icon.png" width="40"></A></TD>
          <TD align="center"><A href="javascript:navigator_Go('logout.php');"><img src="icone/home_button.png" width="35"></A></TD>
          <TD align="right"><A href="javascript:navigator_Go('index.php');"><img src="icone/refresh57.png" width="30"></A></TD>
        </TR>
      </table>
      <BR> <BR> <BR>
        <table width="50%" class="centered">
          <tr><th>Elimina</th><th>Modifica</th><th>Data</th><th>Impianto</th><th>Posizione</th><th>Non conformità</th><th>Azione correttiva</th></tr>

          <?php
          include "db_connection.php";

          $query = "SELECT nc_id, nc_date, nc_type, nc_ac, serial, device_name, position FROM non_conformita where codUtente = '$COD_UTENTE' and nc_date like '%$mese/$anno'";
          $result = $conn->query($query);
          if(($result->num_rows) == 0)
          { ?>
          </table> nessun record trovato
        <?php } else {

          $x=0;
          while($row = $result->fetch_assoc()) {
            $nc_id[$x]=$row["nc_id"];
            $nc_date[$x]=$row["nc_date"];
            $nc_type[$x]=$row["nc_type"];
            $nc_ac[$x]=$row["nc_ac"];
            $device_name[$x]=$row["device_name"];
            $position[$x]=$row["position"];
            ++$x;
          }

          for($i=0;$i<$x;$i++) {

            ?>
            <TR>
              <TD>
                <form action="hooly_db_actions.php" method="post">
                  <input type="hidden" name="act" value="nc_delete">
                  <input type="hidden" name="nc_id" value=<?php echo $nc_id[$i] ?> >
                  <input type="hidden" name="mese" value=<?php echo $mese ?> >
                  <input type="hidden" name="anno" value=<?php echo $anno ?> >
                  <button type="submit" class="imgbtn"> <img src="icone/trash.png" height="30" width="30"></button>
                </form></td>
                <TD>
                  <form action="hooly_db_actions.php" method="post">
                    <input type="hidden" name="act" value="nc_modify">
                    <input type="hidden" name="nc_id" value=<?php echo $nc_id[$i] ?> >
                    <button type="submit" class="imgbtn"> <img src="icone/edit.png" height="25" width="30"></button>
                  </form></td>
                  <TD><?php echo $nc_date[$i] ?></TD>
                  <TD><?php echo $device_name[$i] ?></TD>
                  <TD><?php echo $position[$i] ?></TD>
                  <TD><?php echo $nc_type[$i] ?></TD>
                  <TD><?php echo $nc_ac[$i] ?></TD>
                </TR>
                <?php
              }
              echo "</TABLE> ";
            }

            $conn->close();
            ?>

          </body>
