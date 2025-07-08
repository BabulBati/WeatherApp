<?php
$hostname = "localhost";
$username = "root";
$password = "";

$conn = mysqli_connect($hostname, $username, $password);

$create_database = "CREATE DATABASE IF NOT EXISTS weather";
mysqli_query($conn, $create_database);

mysqli_select_db($conn, 'weather');

$create_table = "CREATE TABLE IF NOT EXISTS weather_data(
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(255),
    weather_main VARCHAR(255),
    weather_condition VARCHAR(255),
    weather_icon VARCHAR(255),
    temp FLOAT,
    humidity FLOAT,
    pressure FLOAT,
    wind_speed FLOAT,
    wind_direction FLOAT,
    timezone INT,
    city_dt BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

mysqli_query($conn, $create_table);

if (isset($_GET['cityName'])) {
    $cityName = $_GET['cityName'];
    $encode_cityName = urlencode($cityName);
    $api_key = "0546137e208a0ac6ba8347aa3d90766f";
    $response = @file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=$encode_cityName&units=metric&appid=$api_key");
    if (!$response) {
        echo json_encode(["message" => "API error !!"]);
        exit;
    }

    $data = json_decode($response, true);

    $city_name = $data['name'];
    $weather_main = $data['weather']['0']['main'];
    $weather_condition = $data['weather']['0']['description'];
    $weather_icon = $data['weather']['0']['icon'];
    $temp = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $pressure = $data['main']['pressure'];
    $wind_speed = $data['wind']['speed'];
    $wind_direction = $data['wind']['deg'];
    $timezone = $data['timezone'];
    $city_dt = $data['dt'];

    $select_query = "SELECT * FROM weather_data WHERE city_name='$cityName'";
    $results = mysqli_query($conn, $select_query);

    if (mysqli_num_rows($results) > 0) {
        $row = mysqli_fetch_assoc($results);
        $old_dt = intval($row['city_dt']);
        $timeDiff = $city_dt - $old_dt;

        if ($timeDiff > 1 * 60 * 60) {
            $update_query = "UPDATE weather_data SET 
                weather_main='$weather_main',
                weather_condition='$weather_condition',
                weather_icon='$weather_icon',
                temp=$temp,
                humidity=$humidity,
                pressure=$pressure,
                wind_speed=$wind_speed,
                wind_direction=$wind_direction,
                timezone=$timezone,
                city_dt=$city_dt,
                created_at=CURRENT_TIMESTAMP
                WHERE city_name='$cityName'";
            mysqli_query($conn, $update_query);
        }
    } else {

        $insert_query = "INSERT INTO weather_data(city_name, weather_main, weather_condition,weather_icon,temp,humidity,pressure,wind_speed,wind_direction,timezone,city_dt) 
    VALUES ('$city_name', '$weather_main', '$weather_condition','$weather_icon',$temp,$humidity,$pressure,$wind_speed,$wind_direction,$timezone,$city_dt);";

        mysqli_query($conn, $insert_query);
    }
    $latest_data = mysqli_query($conn, "SELECT * FROM weather_data WHERE city_name='$cityName'");
    $row = mysqli_fetch_assoc($latest_data);
    echo json_encode($row);
}
?>