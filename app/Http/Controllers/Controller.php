<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Memscore application API",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="info@memscore.com"
 *         ),
 *     ),
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
