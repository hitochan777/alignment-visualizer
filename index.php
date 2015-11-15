<?php
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>あらいめんとちぇっく</title>
	<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
<div class="container">
<?php
$dname = isset($_GET["dname"])?$_GET["dname"]:"ja_zh";
$data = $files[$dname];
// if(!verify($data)){
// 	echo "some of the files are missing!";
// }
// else{
// 	echo "Verification OK"."</br>";
// }
$fpt = fopen(DATADIR."/$dname/".$data["target"],"r");
$fps = fopen(DATADIR."/$dname/".$data["source"],"r");
?>
<table class="table table-bordered">
<tr>
	<th>ID</th>
	<th>Source Sentence</th>
	<th>Target Sentece</th>
</tr>
<?php
$cnt = 1;
while(($f = fgets($fps))!==false && ($e = fgets($fpt))!==false){
	echo "<tr><td><a href='align.php?id=$cnt&dname=$dname'>$cnt</a></td><td>$f</td><td>$e</dt></tr>";
	$cnt++;
}
?>
</table>

</div>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="bower_components/jquery/dist/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>

