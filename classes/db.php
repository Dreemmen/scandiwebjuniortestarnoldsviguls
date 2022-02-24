<?php

class db {
    //put your code here
    public $handle;
    public $dbname;
        
    function __construct($hostname, $user, $password, $database){
        //get url link, parse it, put parts in $url, url dont concludes pages or admin, $path does;
        $this->handle = mysqli_connect($hostname, $user, $password, $database);
        mysqli_set_charset($this->handle, 'UTF8');
        $this->dbname = $database;
        
        if(!$this->handle) die("ERROR ". mysqli_connect_error());
    }
}