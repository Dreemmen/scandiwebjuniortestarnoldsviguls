<?php
class shop {
    private $db;
    private $first_time = false;
    public $params = [];
    function __construct($mysqli_connection){
        $this->db = $mysqli_connection;
        // ###
        if($this->first_time){
            //izveido tabulu
            $this->db->query("
            CREATE TABLE IF NOT EXISTS `preces` (
            `sku` int(11) NOT NULL,
            `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
            `price` decimal(10,0) NOT NULL,
            `productType` ENUM('DVD', 'Furniture', 'Book') NOT NULL,
            PRIMARY KEY (`sku`)
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            
            CREATE TABLE `preces_parametri` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `p_id` int(11) NOT NULL,
            `name` varchar(30) CHARACTER SET utf8 NOT NULL,
            `value` varchar(254) COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            ");
        }
    }
    
    // insert product function?
    // validate function?
    
    function get_products($filter = 0){
        $db = $this->db;
        
        //filtrs, kategorijas nav vajadzigas 
        $WHERE = "";
        if($filter>0){
            $WHERE .= "WHERE ";
        }
        $querry = $db->prepare("SELECT * FROM `preces` ". $WHERE .";");
        //$querry->bind_param("ssds", $nosaukums, $modelis, $cena, $attels);
        $querry->execute();
        $result = $querry->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // data jāizvelk tā, lai produkta id ir masīva indekss
        $querry->close();
        
        //parametrus izņem atsevišķi
        $querry = $db->prepare("SELECT * FROM `preces_parametri`;");
        //$querry->bind_param("ssds", $nosaukums, $modelis, $cena, $attels);
        $querry->execute();
        $result = $querry->get_result();
        $raw_params = $result->fetch_all(MYSQLI_ASSOC);
        $querry->close();
        
        //pievieno $data[product_id][parametres][] = $par;
        // ###
        foreach($raw_params as $par){
            $this->params[$par['p_id']][] = $par;
        }
            
        return $data;
    }
}
