<?php
namespace Lib\Utility;

class Sanitizer{
    public static function escapeChar($str) {
        $str = str_replace("&", "&amp;", $str);
        $str = str_replace(" ", "&nbsp;", $str);
        $str = str_replace("　", "&nbsp;&nbsp;", $str);
        $str = str_replace("<", "&lt;", $str);
        $str = str_replace(">", "&gt;", $str);
        $str = str_replace("\"", "&quot;", $str);
        return $str;
    }
}
?>
