<?php

namespace Ovoform\BackOffice;

class Abort extends CoreController{

    public $code;
    public $message;
    public $viewPath;

    private $errors = [
        400=>[
            'title'=>'Bad Request',
            'message' => '400 Bad Request'
        ],
        401=>[
            'title'=>'Unauthorized',
            'message' => '401 Unauthorized'
        ],
        402=>[
            'title'=>'Payment Required',
            'message' => '402 Payment Required'
        ],
        403=>[
            'title'=>'Forbidden',
            'message' => '403 Forbidden'
        ],
        404=>[
            'title'=>'Not Found',
            'message' => '404 Page Not Found'
        ],
        405=>[
            'title'=>'Method Not Allowed',
            'message' => '405 Method Not Allowed'
        ],
    ];

    public function __construct($code,$message = null)
    {
        $this->viewPath = OVOFORM_ROOT.'views';
        $this->code = $code;
        $this->message = $message;
    }

    public function abort()
    {
        if (!array_key_exists($this->code,$this->errors)) {
            throw new \Exception("Error code is not available in abort class");
        }
        $error = $this->errors[$this->code];
        $message = $this->message ? $this->message : $error['message'];
        if (file_exists($this->viewPath.'/errors/'.$this->code.'.php')) {
            status_header($this->code);
            nocache_headers();
            $pageTitle = $error['title'];
            ovoform_include('errors/'.$this->code, compact('pageTitle','message'));
            exit;
        }else{
            status_header($this->code);
            nocache_headers();
            wp_die($message, $error['title'], $this->code);
            exit;
        }
    }
}