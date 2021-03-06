<?php
require_once("init.php");
require_once("config/config.php");
use Lib\Utility\Sanitizer;
use Lib\Utility\FileUtility;
use Lib\Tree\DependencyTree;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>アライメントチェック</title>
    <!-- <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  </head>
  <body>
<div class="container">
<div class="row">
<div >
<center>
<?php
$dname = isset($_GET["dname"])?$_GET["dname"]:"ja_en";
$sys = isset($_GET["sys"])?intval($_GET["sys"]):0;
$id = isset($_GET["id"])?intval($_GET["id"]):1;
$etreeId = isset($_GET["etreeId"])?intval($_GET["etreeId"]):0;
$data = $files[$dname];
?>
<?php
echo "<h2>$dname({$data['align'][$sys]})</h2>";
?>
<?php foreach($data["align"] as $index=>$name):?>
<a href="align.php?id=<?php echo $id;?>&dname=<?php echo $dname?>&sys=<?php echo $index?>&etreeId=<?php echo $etreeId;?>"><?php echo $name?></a>
<?php endforeach;?>
<?php
# Open files
$fpt = fopen(DATADIR."/$dname/".$data["target"],"r");
$fps = fopen(DATADIR."/$dname/".$data["source"],"r");
$fpa = fopen(DATADIR."/$dname/".$data["align"][$sys],"r");
$fpa2 = fopen(DATADIR."/$dname/".$data["align2"],"r");
if(array_key_exists("source_tree",$data)){
    $fpftree = fopen(DATADIR."/$dname/".$data["source_tree"],"r"); # source tree
    $ftree = new DependencyTree(FileUtility::getChunkByIndex($id, "^#", $fpftree ));
}
else{
    $fpftree = null;
    $ftree = null;
}
if(array_key_exists("target_tree",$data)){
    $fpetree = fopen(DATADIR."/$dname/".$data["target_tree"][$etreeId],"r"); # target tree
    $etree = new DependencyTree(FileUtility::getChunkByIndex($id, "^#", $fpetree ));
}
else{
    $fpetree = null;
    $etree = null;
}



$aligns = [];
$aligns2 = [];
$alignNum1=0.0;
$alignNum2=0.0;
$correct=0.0;
$cnt = 1;
$flag = false;


while(($f = fgets($fps))!==false && ($e = fgets($fpt))!==false && ($a = fgets($fpa))!==false && ($a2 = fgets($fpa2))!==false){
    if(intval($_GET["id"])==$cnt){
        $flag=true;
        break;
    }
    $cnt++;
}
if(!$flag){
    echo "<div class='alert alert-danger' role='alert'>No page found</div>";
    exit();
}

$linkPattern = "/(\d+)-(\d+)(?:\[(.+)\])?/";
foreach(explode(" ",$a) as $value){
    $linkTag = true;
    $matches = [];
    preg_match($linkPattern, $value, $matches);
    if(count($matches) == 4){
        $linkTag = $matches[3];
    }
    $fIndex = $matches[1];
    $eIndex = $matches[2];
    if(!array_key_exists($fIndex, $aligns)){
        $aligns[$fIndex] = [];
    }
    $aligns[$fIndex][$eIndex] = $linkTag;
    $alignNum1++;
}
foreach(explode(" ",$a2) as $value){
    $linkTag = true;
    $matches = [];
    preg_match($linkPattern, $value, $matches);
    if(count($matches) == 4){
        $linkTag = $matches[3];
    }
    $fIndex = $matches[1];
    $eIndex = $matches[2];
    if(!array_key_exists($fIndex, $aligns2)){
        $aligns2[$fIndex] = [];
    }
    $aligns2[$fIndex][$eIndex] = $linkTag;
    $alignNum2++;
}
$fwords = explode(" ", $f);
$ewords = explode(" ", $e);
if($ftree !== null){
    $ftreeBuffer = $ftree->getVisualizedDependencyTree();
}
if($etree !== null){
    $etreeBuffer = $etree->getVisualizedDependencyTree(true);
}
echo "<table border='1' style='border-collapse: collapse; empty-cells: show;'>";
for($fIndex = 0;$fIndex<count($fwords);++$fIndex){
    echo "<tr>";
    echo "<td height='20'>$fIndex</td>";
    if($ftreeBuffer){
        $ftreeBuffer[$fIndex] = Sanitizer::escapeChar($ftreeBuffer[$fIndex]);
        echo "<td height='20' title='".$ftree->nodeList[$fIndex]["pos"]."'>${ftreeBuffer[$fIndex]}</td>";
    }
    else{
        echo "<td height='20'>${fwords[$fIndex]}</td>";
    }
    for($eIndex = 0;$eIndex<count($ewords);++$eIndex){
        $a1_ok = array_key_exists($fIndex,$aligns) && array_key_exists($eIndex,$aligns[$fIndex]);
        $a2_ok = array_key_exists($fIndex,$aligns2) && array_key_exists($eIndex,$aligns2[$fIndex]);
        if($a1_ok && $a2_ok){
            $color = "green";
            $correct++;
        }
        else if($a1_ok){
            $color = "blue";
        }
        else if($a2_ok){
            $color = "yellow";
        }
        else{
            $color = "white";
        }
        echo "<td width='20' height='20' bgcolor=$color>";
        $linkTag1 = ""; 
        $linkTag2 = "";
        $linkTagMap = ["possible"=>"P", "sure"=>"S"];
        if(gettype($aligns[$fIndex][$eIndex]) == "string"){
            $linkTag1 = $aligns[$fIndex][$eIndex];
        }
        if(gettype($aligns2[$fIndex][$eIndex]) == "string"){
            $linkTag2 = $aligns2[$fIndex][$eIndex];            
        }
        if($linkTag1 != "" or $linkTag2 != ""){
            printf("%s/%s", $linkTagMap[$linkTag1], $linkTagMap[$linkTag2]);
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "<tr><td></td>";
?>
<td>
<?php for($index = 0; $index < count($data["target_tree"]); $index++):?>
<a href="align.php?id=<?php echo $_GET["id"]?>&dname=<?php echo $dname?>&sys=<?php echo $sys?>&etreeId=<?php echo $index;?>"><?php echo $data["target_tree"][$index];?></a>
<?php endfor;?>
</td>

<?php
for($eIndex = 0;$eIndex<count($ewords);++$eIndex){
    if($etreeBuffer){
        echo "<td valign='top' title='".$etree->nodeList[$eIndex]["pos"]."'>";
        error_log(Sanitizer::escapeChar($etreeBuffer[$eIndex]));
        for($i = 0; $i<mb_strlen($etreeBuffer[$eIndex]);++$i){
            if(mb_substr($etreeBuffer[$eIndex],$i,1, 'UTF-8')==""){ # I don't know there are empty characters at the end
                break; 
            }
            echo Sanitizer::escapeChar(mb_substr($etreeBuffer[$eIndex],$i,1, 'UTF-8'))."<br>";
        }
    }
    else{
        echo "<td valign='top'>"; 
        for($i = 0; $i<mb_strlen($ewords[$eIndex]);++$i){
            if(mb_substr($ewords[$eIndex],$i,1, 'UTF-8')==""){ # I don't know there are empty characters at the end
                break; 
            }
            echo Sanitizer::escapeChar(mb_substr($ewords[$eIndex],$i,1, 'UTF-8'))."<br>";
        }

    }
    echo "</td>";
}
echo "</tr>";
echo "<tr><td height='20' width='20'></td><td></td>";
for($eIndex = 0;$eIndex<count($ewords);++$eIndex){
    echo "<td>$eIndex</td>";
}
echo "</tr>";
echo "</table>";
$precision = $correct/$alignNum1;
$recall = $correct/$alignNum2;
$fmeasure = $precision*$recall*2/($precision+$recall);
echo "Fmeasure: $fmeasure<br>";
echo "precision: $precision<br>";
echo "recall: $recall<br>";
?>
<?php foreach($data["align"] as $index=>$name):?>
<a href="align.php?id=<?php echo $_GET["id"]?>&dname=<?php echo $dname?>&sys=<?php echo $index?>&etreeId=<?php $etreeId;?>"><?php echo $name?></a>
<?php endforeach;?>
<br>
  <a href="align.php?id=<?php echo $_GET["id"]-1?>&dname=<?php echo $dname?>&sys=<?php echo $sys?>&etreeId=<?php echo $etreeId;?>">Back</a>
<a href="index.php?dname=<?php echo $dname ?>&sys=<?php echo $sys?>&etreeId=<?php echo $etreeId;?>">Home</a>
<a href="align.php?id=<?php echo $_GET["id"]+1?>&dname=<?php echo $dname?>&sys=<?php echo $sys?>&etreeId=<?php echo $etreeId;?>">Next</a>
</center>
</div>
</div>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>

