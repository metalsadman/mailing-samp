<?php
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_WARNING));
require_once 'utils.php';
require_once 'validator.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    Utils::json_response(['message' => "Invalid Request!"], 400);
}

$add1 = $_POST["address1"];
$add2 = $_POST["address2"];
$city = $_POST["city"];
$state = $_POST["state"];
$zip = $_POST["zip"];

try{
    if (empty($add1) || empty($add2) || empty($city) || empty($state) || empty($zip)) {
        Utils::json_response(['message' => "All fields are required"], 400);
    } else {
        $validator = new Validator();
        return Utils::json_response([
                'data' => $validator->validate($add1, $add2, $city, $state, $zip)
            ], 
            200
        ); 
    }
}catch(\Exception $e){
    Utils::json_response(['message' => $e->getMessage()], 400);
}
?>
