<?php

namespace Projects\WellmedGateway\Controllers;

use App\Http\Controllers\Controller as MainController;
use Projects\WellmedGateway\Concerns\HasUser;

abstract class Controller extends MainController
{
    use HasUser;
}
