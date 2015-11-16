<?php
define("DATADIR","data");
$files = [
    "ja_zh" => [
        "align2"=>"test.a.s",
        "align"=>["200best.a","1best.a","1best.a.bin","hoge","cons-2link","1best.3link","1best.4link","1best.5link"],
        "target"=>"test.e",
        "source"=>"test.f",
        "target_tree"=>"hoge",
        "source_tree"=>"fuga"
    ],
    "ja_en"=> [
        "align2"=>"test.a.s",
        "align"=>["forest_aligner.1best","forest_aligner.200best", "nile.2link", "nile.all", "forest_aligner.1best.bin"],
        "target"=>"test.e",
        "source"=>"test.f",
        "target_tree"=>"hoge",
        "source_tree"=>"fuga"
    ],
    "ja_en_train"=> [
        "align2"=>"train.a.s",
        "align"=>["train.a.s"],
        "target"=>"train.e",
        "source"=>"train.f",
        "target_tree"=>"train.e.dep",
        "source_tree"=>"train.f.dep"
    ]

];
?>
