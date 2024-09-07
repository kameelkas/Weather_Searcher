<?php
error_reporting(0);
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
        $error = "Weather could not be found for given city. Please try again."; //result of unsucessful request
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
        $error = "Weather could not be found for given city. Please try again."; //result of unsucessful request
    }
} else {
    $error = "Please enter a city.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Weather Searcher</title>
    <link rel="apple-touch-icon" sizes="180x180" href="./favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon/favicon-16x16.png">
    <link rel="manifest" href="./favicon/site.webmanifest">
    <link href="style.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <div id="container" class="light">
        <button id="lightDarkButton" class="buttons">&#9681;</button>
        <h1>What's the Weather?</h1>
        <form>
            <label for="city">Enter the name of a city <br> (And the 2 Digit ISO Code for cities that share the same name) </label>
            <div id=inputDiv>
                <input type="text" id="city" name="inputCity" placeholder="E.g. Abuja" />
                <input type="text" id="code" name="inputCode" placeholder="E.g. NG" />
            </div>
            <button id="submitButton" class="buttons" type="submit">Search</button>
        </form>
        <div id="weather">
            <?php
            if (!empty($error)) { //showing error message when invalid city is given 
                echo '<p id="error">' . $error . '</p>';
            } else { //showing gathered weather info using values in weather info array
                echo '<div id="success">' .
                    '<img src=' . $weather['image'] . '>' .
                    '<div id="weatherInfo">' .
                    '<span>Weather Report for: ' . $weather['cityName'] . ', ' . $weather['countryCode'] . '</span>' .
                    '<span>Weather: ' . $weather['main'] . '</span>' .
                    '<span>Weather Description: ' . $weather['description'] . '</span>' .
                    '<span>Temperature: ' . $weather['tempC'] . '°C / ' . $weather['tempF'] . '°F </span>' .
                    '<span>Feels Like: ' . $weather['feelsLikeC'] . '°C / ' . $weather['feelsLikeF'] . '°F </span>' .
                    '</div>' .
                    '</div>';
            }
            ?>
        </div>
    </div>

    <script src="javascript.js"></script>
</body>

</html>