<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class PermittedPages
{
    public static $pages = ['landingpage', 'landingpage-announcement', 'landingpage-profile'];
}
