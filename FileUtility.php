<?php
namespace Utility;

class FileUtility{
    public static function getChunkByIndex($index, $delimiter, $fp){
        $cnt = 0;
        $chunk = "";
        $flag = false;
        while(($line=fgets($fp))!==false){
            $line = trim($line);
            if($cnt>$index){
                break; 
            }
            if(ereg($delimiter, $line)){
                $cnt++;
            }
            else if($index === $cnt and $line!==""){
               $chunk .= $line."\n";
            }
        }
        return trim($chunk); # remove the last newline
    }
}
?>
