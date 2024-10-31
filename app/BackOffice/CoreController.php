<?php

namespace Ovoform\BackOffice;

class CoreController{
    public function view($view,$data = [])
    {
        ob_start();
        extract($data);
        include $this->viewPath.'/'.$view.'.php';
    }

}