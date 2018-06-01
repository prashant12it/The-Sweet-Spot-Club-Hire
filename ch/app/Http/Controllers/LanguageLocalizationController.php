<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 26-09-2017
 * Time: 11:58 AM
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
class LanguageLocalizationController extends Controller
{
    /*public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }
        return Redirect::back();
    }*/

    public function index(Request $request){
        if($request->lang <> ''){
            app()->setLocale($request->lang);
        }
//        echo trans('alert.success');
    }
}