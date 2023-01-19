<?php
require_once 'utils.php';
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    Utils::json_response(['message' => "Invalid Request!"], 400);
}

$add1 = $_POST["address1"];
$add2 = $_POST["address2"];
$city = $_POST["city"];
$state = $_POST["state"];
$zip = $_POST["zip"];

$db = mysqli_connect("localhost", "root", "", "mailing") 
            or Utils::json_response(["message" => "Server Error: Database config is possibly wrong."], 500);
$query = "INSERT INTO addresses (address1, address2, city, state, zip) VALUES ('$add1', '$add2', '$city', '$state', '$zip')";

$res = mysqli_query($db, $query);
mysqli_close($db);

Utils::json_response(['message' => "Address saved successfully!"], 200);