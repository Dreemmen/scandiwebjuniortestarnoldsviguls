<?php
class url {
    public $url = array();
    public $page_url;
    public $page_path;
    public $title = 'Scandiweb Junior Test';
    private $default_page_path = 'store';
    
    function __construct(){
        //get url link, parse it, put parts in $url, url dont concludes pages or admin, $path does;
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url = explode('/', trim($url['path'], '/'));
        if($url[0] == WEBISTE_FOLDER_NAME) unset($url[0]);
        if(empty($url)){
            $this->page_url = ''; 
            $this->page_path = '/' . $this->default_page_path; 
        } else {
            foreach ($url as $u){
                // page_url adreses daļa ar visiem argumentiem, gan faila atrašanās vieta, gan pseido $_GET parametri
                // IZVEIDOT pseido $_GET parametrus!
                $this->page_url .= '/' . $u; 
            }
            // page_path ir adreses daļa līdz pirmajai reālajai mapei, kurā ir index fails
            $this->page_path = $this->page_url;
            foreach ($url as $u){
                if(file_exists(   ($_SERVER['DOCUMENT_ROOT']) . '/' . WEBISTE_FOLDER_NAME . '/p' . $this->page_path . '/index.php')   ) break;
                $this->page_path = rtrim($this->page_path, '/'.$u);
            }                
            if(empty($this->page_path)) $this->page_path = '/' . $this->default_page_path;
            
            $this->title = ucfirst(trim($u, '/'));
        }
    }
    
    public function load_page(){
            include ($_SERVER['DOCUMENT_ROOT']) . '/' . WEBISTE_FOLDER_NAME . '/p' . $this->page_path . '/index.php';      
    }
}
