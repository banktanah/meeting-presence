<?php

namespace App\Models\Dto;

/**
 * - no argument means success </br>
 * - 1 argument: data
 * - 2 arguments: status-code & message
 * - 3 arguments: status-code, data, & message
 */
class ApiResponse
{
    const CODE_SUCCESS = 0;
    const CODE_FAILED = 1;
    const CODE_ERROR = 99;

    public $code = self::CODE_SUCCESS;
    public $data = null;
    public $message = 'success';

    public function __construct()
    {
        $numargs = func_num_args();

        // no argument means success
        if($numargs <= 1){
        }
        // 1 argument: data
        if($numargs == 1){
            $this->data = func_get_arg(0);
        }
        
        // 2 arguments: status-code & message
        if($numargs == 2){
            $this->code = func_get_arg(0);
            $this->message = func_get_arg(1);
        }

        // 3 arguments
        if($numargs == 3){
            $this->code = func_get_arg(0);
            $this->data = func_get_arg(1);
            $this->message = func_get_arg(2);
        }
    }
}
