<?php
namespace Tree;
class DependencyTree {
    public $nodeList = [];
    public $rootId = -1;
    public function __construct($str){
        # Reset current class members
        $infos = array_map(
            function($word_infos){
                return preg_split("/\s+/", $word_infos);
            }, 
                explode("\n", trim($str))
            );
        $strlen = count($infos);
        for($i = 0;$i<$strlen; $i++){
            $this->nodeList[] = [
                "pre_children_ref" => [],
                "post_children_ref" => []
            ];
        }
        foreach($infos as $info){
            $id = intval($info[0]);
            $dependency_id = intval($info[1]);
            $surface = $info[2];
            $dict_form = explode("/", $info[3])[0];
            if(strpos($info[3], "/") !== False){
                $pronunciation = explode("/", $info[3])[1];
            }
            else{
                $pronunciation = null;
            }
            if(strpos($info[4], ":")){
                $pos2 = explode(":", $info[4])[1]; # pos in detail
            }
            else{
                $pos2 = null;
            }
            $pos = explode(":", $info[4])[0];
            $isContent = $info[5]=="1";
            $this->nodeList[$id] = array_merge($this->nodeList[$id], [
                "id"=> $id,
                "dep_id" => $dependency_id, 
                "surface" => $surface, 
                "dict_form" => $dict_form,
                "pronunciation" => $pronunciation,
                "isContent" => $isContent, 
                "pos" => $pos, 
                "pos2"=> $pos2
            ]);
            if($dependency_id >= 0){
                if($id < $dependency_id){
                    $this->nodeList[$dependency_id]["pre_children_ref"][] = $id;
                }
                else{
                    $this->nodeList[$dependency_id]["post_children_ref"][] = $id;
                }
            }
            else{
                if($this->rootId !== -1){
                    echo 'Root appeared more than once!! Skipping... ';
                    return null;
                }
                $this->rootId = $id;
            }
        }
        if($this->rootId == -1){
            echo "Root did't appear!! Skippping...";
            return null;
        }
        return;
    }
    public function getVisualizedDependencyTree($horizontal = false){
        $buffer = [];
        $this->_getVisualizedDependencyTree($this->nodeList[$this->rootId], "", $buffer, $horizontal);
        return $buffer;
    }
    private function _getVisualizedDependencyTree($word_ref, $mark, &$b_ref, $horizontal = false){
        $local_buffer = "";
        $children = [];
        # print pre-children
        foreach($word_ref["pre_children_ref"] as $c){
            $children[] = &$this->nodeList[$c];
        }
        if(count($children) > 0){
            $this->_getVisualizedDependencyTree(array_shift($children), $mark."L", $b_ref, $horizontal);
            foreach($children as $c){
                $this->_getVisualizedDependencyTree($c, $mark . "l", $b_ref, $horizontal);
            }
        }

        # print self
        $markList = [];
        if($mark!==""){
            $markList = str_split($mark);
        }
        for($m = 0; $m < count($markList); $m++) {
            if ($m == count($markList)-1) {
                if ($markList[$m]=="L") {
                    $local_buffer .= '┌';
                }
                else if($markList[$m] == "R"){
                    if($horizontal){
                        $local_buffer .= '┐';
                    }
                    else{
                        $local_buffer .= '└';
                    }
                }
                else{
                    if($horizontal){
                        $local_buffer .= '┬';                   
                    }
                    else{
                        $local_buffer .= '├';
                    }
                }
            }
            else {
                if ($markList[$m] == "l" or $markList[$m] == "r" or
                    ($markList[$m] == "L" and ($markList[$m+1] == "r" or $markList[$m+1] == "R")) or
                    ($markList[$m] == "R" and ($markList[$m+1] == "l" or $markList[$m+1] == "L"))) {
                        if($horizontal){
                            $local_buffer .= '─';
                        }
                        else{
                            $local_buffer .= '│';
                        }
                    }
                else {
                    $local_buffer .= '　';
                }
            }
        }
        $local_buffer .= $word_ref["surface"];
        $b_ref[] = $local_buffer;

        # print post-children
        $children = [];
        foreach($word_ref["post_children_ref"] as $c) {
            $children[] = &$this->nodeList[$c];
        }
        if(count($children)>0){
            $last_child = array_pop($children);
            foreach($children as $c){
                $this->_getVisualizedDependencyTree($c, $mark."r", $b_ref, $horizontal);
            }
            $this->_getVisualizedDependencyTree($last_child, $mark."R", $b_ref, $horizontal);
        }
    }
}
?>
