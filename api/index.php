 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
// initiaiting variables that will be used later on
$apiKey = '********************************';
$city = '';
$countryCode = '';
$longitude = '';
$latitude = '';
$weather = array();
$error = '';


if (array_key_exists('inputCity', $_GET)) { //checking to see if a city was entered 
    $city = urlencode($_GET['inputCity']);

    if (isset($_GET['inputCode']) && !empty($_GET['inputCode'])) { //checking to see if country code was also added and building url accordingly
        $upper = strtoupper($_GET['inputCode']);
        $countryCode = urlencode($upper);
        $longLatUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$city,$countryCode&limit=1&appid=$apiKey";
    } else {
        $longLatUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$city&limit=1&appid=$apiKey";
    }

    try { // sending request
        $longLatResponse = file_get_contents($longLatUrl);
        if (!$longLatResponse) {
            throw new Exception;
        } else { //successful request, getting longitude & latitude values and adding city and country code values to weather info array
            $longLatDecode = json_decode($longLatResponse, true);
            $longLatValues = $longLatDecode[0];
            $weather['cityName'] = $longLatValues['name'];
            $weather['countryCode'] = $longLatValues['country'];
            $latitude = $longLatValues['lat'];
            $longitude = $longLatValues['lon'];
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Weather could not be found for given city. Please try again."]); //result of unsucessful request
        exit;
    }

    //building url to get weather info for requested city using acquired longitude & latitude values
    $getWeatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$apiKey&units=metric";

    try {
        $weatherResponse = file_get_contents($getWeatherUrl);
        if (!$weatherResponse) {
            throw new Exception;
        } else { //successful request, getting rest of info needed that will show on the screen, and adding them to weather info array
            $weatherDecode = json_decode($weatherResponse, true);
            $icon = $weatherDecode['weather'][0]['icon'];
            $weather['image'] = "https://openweathermap.org/img/wn/$icon@2x.png";
            $weather['main'] = $weatherDecode['weather'][0]['main'];
            $weather['description'] = $weatherDecode['weather'][0]['description'];
            $tempC = $weatherDecode['main']['temp'];
            $tempF = ($tempC * (9 / 5)) + 32;
            $weather['tempC'] = $tempC;
            $weather['tempF'] = round($tempF, 2);
            $feelsLikeC = $weatherDecode['main']['feels_like'];
            $feelsLikeF = ($feelsLikeC * (9 / 5)) + 32;
            $weather['feelsLikeC'] = $feelsLikeC;
            $weather['feelsLikeF'] = round($feelsLikeF, 2);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Weather could not be found for given city. Please try again."]); //result of unsucessful request
        exit;
    }

    echo json_encode($weather);
} else {
    echo json_encode(["error" => "Please enter a city."]);
}
?>
