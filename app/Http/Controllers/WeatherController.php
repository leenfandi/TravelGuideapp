<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WeatherController extends Controller
{

 public function getWeatherData($city) {


    $apiKey = '5a6ea0b5c8333fb264b66c8aeef6ad1b';
    $url = "http://api.openweathermap.org/data/2.5/weather?q=".$city."&appid=".$apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch , CURLOPT_HEADER,0 );
    curl_setopt($ch , CURLOPT_FOLLOWLOCATION,1 );
    curl_setopt($ch , CURLOPT_VERBOSE,0 );


    $result = curl_exec($ch);

    curl_close($ch);

    return response()->json(json_decode($result));
}
}
