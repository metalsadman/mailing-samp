<?php
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_WARNING));
require_once 'utils.php';
require_once 'validator.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $street = $_POST["street"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $zip = $_POST["zip"];
    
    try{
        if (empty($street) || empty($city) || empty($state) || empty($zip)) {
            Utils::json_response(['message' => "All fields are required"], 400);
        }else {
            $validator = new Validator();
            $valRes = $validator->validate($street, $city, $state, $zip);
            $db = mysqli_connect("localhost", "root", "", "mailing") 
                or Utils::json_response(["message" => "Server Error: Database config is possibly wrong."], 500);
                // . mysqli_connect_error()
            if(isset($_POST['standardized'])){
                $street = $valRes['street'];
                $city = $valRes['city'];
                $state = $valRes['state'];
                $zip = $valRes['zip'];
            }            

            $query = "INSERT INTO addresses (street, city, state, zip) VALUES ('$street', '$city', '$state', '$zip')";
            $res = mysqli_query($db, $query);
            mysqli_close($db);
            Utils::json_response(['message' => "Address submitted successfully!", 'data' => $valRes], 200);
        }
    }catch(\Exception $e){
        Utils::json_response(['message' => $e->getMessage()], 400);
    }
}else{
    Utils::json_response(['message' => "Invalid Request!"], 400);
}
?>
