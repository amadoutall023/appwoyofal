<?php

namespace App\Core\Abstract;
use App\Core\Session;
abstract class AbstractController extends Session{
 

    protected   $layout = 'base';

    protected $session;
    
    private static AbstractController|null $instance = null;

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->session = Session::getInstance();
    }


    abstract public function index();
    abstract public function create();
    abstract public function store();
    abstract public function edit();
    // abstract public function destroy();
    abstract public function show();

    

    
    public function renderIndex(string $view='', array $data=[]){
        // header( "");
        extract($data);
        ob_start();
        require_once '../template/'.$view;
       
        $contentForLayout = ob_get_clean();
        require_once '../template/layout/'.$this->baseLayout.'.layout.php';
       
    }


}