<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Excel;
use Yangqi\Htmldom\Htmldom;

/**
 * Class ParseCurlController
 * @package App\Http\Controllers
 */
class AnalyticsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('analytics.index');
    } 

}
