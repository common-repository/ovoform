<?php

namespace Ovoform\Controllers;

use Ovoform\BackOffice\CoreController;

class Controller extends CoreController
{

    public $viewPath = '';

    public function __construct()
    {
        $this->viewPath = OVOFORM_ROOT . 'views';
    }
}
