<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(title="API de base en laravel", version="0.1",description="GabomaPay")
 */

 /**
 * @OA\Server(url="http://oask.ca/api")
 */

 /**
  * @OA\SecurityScheme(bearerFormat="JWT",type="apiKey",securityScheme="bearer")
  */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
