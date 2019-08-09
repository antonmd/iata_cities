<?php

$servername = "localhost";
$database = "your_base";
$username = "user_name";
$password = "password";
$table = "table_name";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

//Get IATA cities code list from travelpayouts.com
$cities = file_get_contents("http://api.travelpayouts.com/data/ru/cities.json");
$cities = json_decode($cities,true);

//Create table
$sql = "CREATE TABLE IF NOT EXISTS `iata_cities` (
            `id_city` int(11) NOT NULL AUTO_INCREMENT,
            `city_name` varchar(80) NOT NULL,
            `city_code` varchar(3) NOT NULL,
            `city_country` varchar(2) NOT NULL,
            `city_lon` float(15) NOT NULL,
            `city_lat` float(15) NOT NULL,
            `city_english` varchar(80) NOT NULL,
            `city_vi` varchar(80) NOT NULL,
            `city_tv` varchar(80) NOT NULL,
            `city_ro` varchar(80) NOT NULL,
            `city_pr` varchar(80) NOT NULL,
            `city_da` varchar(80) NOT NULL,
            PRIMARY KEY (`id_city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1 ;";

if (mysqli_query($conn, $sql)) {
    echo "Table $table created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

//Add IATA cities code to table
foreach ($cities as $city) {
    $city_name = $city['name'];
    $city_code = $city['code'];
    $city_country = $city['country_code'];
    $city_lon = $city['coordinates']['lon'];
    $city_lat = $city['coordinates']['lat'];
    $city_english = $city['name_translations']['en'];

    if ($city['cases']['vi']) {
        $city_vi = $city['cases']['vi'];
    } else $city_vi = $city_name;
    if ($city['cases']['tv']) {
        $city_tv = $city['cases']['tv'];
    } else $city_tv = $city_name;
    if ($city['cases']['ro']) {
        $city_ro = $city['cases']['ro'];
    } else $city_ro = $city_name;
    if ($city['cases']['pr']) {
        $city_pr = $city['cases']['pr'];
    } else $city_pr = $city_name;
    if ($city['cases']['da']) {
        $city_da = $city['cases']['da'];
    } else $city_da = $city_name;

    $sql = "INSERT INTO $table (city_name, city_code, city_country, city_lon, city_lat, city_english, city_vi, city_tv, city_ro, city_pr, city_da) VALUES ('$city_name', '$city_code', '$city_country', '$city_lon', '$city_lat', '$city_english', '$city_vi', '$city_tv', '$city_ro', '$city_pr', '$city_da')";

    if (mysqli_query($conn, $sql)) {
        echo "City $city_name created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
};

mysqli_close($conn);