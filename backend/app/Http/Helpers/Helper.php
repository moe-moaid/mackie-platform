<?php

use App\Models\Genre;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;

if (!function_exists('checkVoteCaste')) {
    function checkVoteCaste($video_id)
    {
        if (session()->get('visitordata')) {
             $visitordata=session()->get('visitordata');
            $user_id = $visitordata['visitor_id'];
            $checkvote = Vote::where('user_id', '=', $user_id)->where('video_id', '=', $video_id)->first();
            
			if ($checkvote == null) {
                return $user_id;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
if(!function_exists('getCountryFlag')){
	
	function getCountryFlag($country_name){
		
		return "https://flagcdn.com/$country_name.svg";
	}
}

if(!function_exists('getAllCountries')){
	function getAllCountries(){
		$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://flagcdn.com/en/codes.json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
return $response;
	}
}
if(!function_exists('getPhoneCode')){
	function getPhoneCode()
    {
        $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://country.io/phone.json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
return $response;
    }
}
if (!function_exists(('getAllgenres'))) {
    function getAllGenres()
    {
        $genres = Genre::all();

        return $genres;
    }
}
