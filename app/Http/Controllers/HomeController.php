<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GoogleClientWrapper;
use \Exception;

class HomeController extends Controller
{
    public function index()
    {
        return view("dashboard");
    }
}