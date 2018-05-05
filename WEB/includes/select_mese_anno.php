<?php
$yearNow = date("Y");
$monthNow = date("m");
?>

<select name="mese">
  <option value="01" <?php if($monthNow == "01") echo "selected"; ?>>Gennaio</option>
  <option value="02" <?php if($monthNow == "02") echo "selected"; ?>>Febbraio</option>
  <option value="03" <?php if($monthNow == "03") echo "selected"; ?>>Marzo</option>
  <option value="04" <?php if($monthNow == "04") echo "selected"; ?>>Aprile</option>
  <option value="05" <?php if($monthNow == "05") echo "selected"; ?>>Maggio</option>
  <option value="06" <?php if($monthNow == "06") echo "selected"; ?>>Giugno</option>
  <option value="07" <?php if($monthNow == "07") echo "selected"; ?>>Luglio</option>
  <option value="08" <?php if($monthNow == "08") echo "selected"; ?>>Agosto</option>
  <option value="09" <?php if($monthNow == "09") echo "selected"; ?>>Settembre</option>
  <option value="10" <?php if($monthNow == "10") echo "selected"; ?>>Ottobre</option>
  <option value="11" <?php if($monthNow == "11") echo "selected"; ?>>Novembre</option>
  <option value="12" <?php if($monthNow == "12") echo "selected"; ?>>Dicembre</option>
</select>

<select name="anno">
  <option value="2017" <?php if($yearNow == "2017") echo "selected"; ?>>2017</option>
  <option value="2018" <?php if($yearNow == "2018") echo "selected"; ?>>2018</option>
  <option value="2019" <?php if($yearNow == "2019") echo "selected"; ?>>2019</option>
  <option value="2020" <?php if($yearNow == "2020") echo "selected"; ?>>2020</option>
  <option value="2021" <?php if($yearNow == "2021") echo "selected"; ?>>2021</option>
</select>
