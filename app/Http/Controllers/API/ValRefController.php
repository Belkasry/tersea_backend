<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sector;

class ValRefController extends Controller
{
    //

    public function sectors()
    {
        return Sector::all();

    }
}
