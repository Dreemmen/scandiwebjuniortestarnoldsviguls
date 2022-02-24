<?php define('WEBISTE_FOLDER_NAME', 'ScandiwebJuniorTest');?>
<!DOCTYPE html>
<!--
To do. 
1) Page url rewrite. Pages in local folders
2) connect to database class
3) Class for product database manage
-->
<?php
require_once 'classes/db.php'; $_db = new db('localhost','arnolds1','123456','arnolds');
require_once 'classes/url.php'; $_url = new url();
require_once 'classes/shop.php'; $_shop = new shop($_db->handle); //db handle;
//$_filter = new filter();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?=$_url->title;?></title>
        <link rel="stylesheet" href="style.css">
        <style>
            .flex-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: flex-start;
                margin-right: 4px;
            }
            .flex-box {
                border: 1px solid black;
                padding: 5px;
                width: 180px;
                height: 180px;
                margin: auto 0 auto 4px;
            }
            .h0-title {
                display: inline-block;
            }
            nav  a, nav a:hover, nav a:focus, nav a:active {
                text-decoration: none;
                color: inherit;
            }
            nav.top-nav {
                float: right;
            }
            nav.top-nav a {
                padding: 5px;
                margin-right: 2px;
                border: 1px solid black;
                display: inline-block;
            }
            .hidden {
                display: none!important;
            }
            .visible {
                display: block;
            }
            .visible_ib {
                display: inline-block;
            }
            .warning_msg {
                display: inline-block;
                color: red;
            }
        </style>
    </head>
    <body>
        <main class="wrapper">
            <?php
                //var_dump($_SERVER);
                if(isset($_GET['_error'])){
                    echo "<p><pre>". var_dump($_GET['_error']) . "</pre></p>";
                }else{
                    $_url->load_page();
                };
            ?>
        </main>
    </body>
</html>
