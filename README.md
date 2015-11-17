# alignment-visualizer
Visualization suite for machine translation

## Requirements
- [Bower](http://bower.io/)

## Install

    bower install

## Usage
- Modify `config.php` as follows, 

```
$files = [
    "ja_zh" => [
        "align2"=>"/path/to/gold",
        "align"=>["/path/to/model/alignment","/path/to/anther/alignement",
        "target"=>"/path/to/tokenized/target/sentence/file",
        "source"=>"/path/to/tokenized/source/sentence/file",
        "target_tree"=>"/path/to/tokenized/source/dependency/tree",
        "source_tree"=>"/path/to/tokenized/target/dependency/tree"
    ],
    "ja_en" => [
    ...
    ],
    ...
];
```

- Create `data/ja_zh` and put the specified files under the directory, do the same thing for other language pair
