<?php
        
interface FileIterator {
    public function setObject($obj);
    public function setTags(...$tags);
    public function run();
}
        
class RemoveTags implements FileIterator {
    private $file;
    private $tags;
    private $num_tags;
    public function setObject($obj) {
        $this -> file = $obj;
    }
    private function error() {
        echo "Can't open the file!";
    }
    public function setTags (...$tags) {
        $this -> tags = $tags;
        $this -> num_tags = count($tags);
    }
    public function run() {
        $flag = false;
        $counter = 0;
        if (!file_exists($this -> file)) {
            $this -> error();
            return;
        } 
        $stream = fopen($this -> file, "r");  
        $array = file($this -> file);
        fclose($stream);
        $temp_stream = fopen("temp.txt", "w+");
        foreach ($array as $str) {
            if ($str == "</head>")
                $flag = true;
            if ($flag)
                file_put_contents("temp.txt", $str, FILE_APPEND);
            else {
                foreach ($this -> tags as $t) {
                    if (mb_strpos($str, $t))
                        $counter ++;    
                }
                if ($counter !== $this -> num_tags) 
                    file_put_contents("temp.txt", $str, FILE_APPEND);
                $counter = 0;
            }
        }
        fclose($temp_stream); 
        unlink($this -> file);
        rename('temp.txt', $this -> file);
    }   
}

$del_meta = new RemoveTags();
$del_meta -> setObject('example.html');
$del_meta -> setTags("meta name", "description");
$del_meta -> run();
$del_meta -> setTags("meta name", "keywords");
$del_meta -> run();