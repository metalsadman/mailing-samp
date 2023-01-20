<?php
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_WARNING));
require_once 'utils.php';

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
        $apiKey = '391SELFE4369';
        $xml = '<AddressValidateRequest USERID="' . $apiKey . '">';
        $xml .= '<Address ID="0">';
        $xml .= '<Address1>' . $add1 . '</Address1>';
        $xml .= '<Address2>' . $add2 . '</Address2>';
        $xml .= '<City>' . $city . '</City>';
        $xml .= '<State>' . $state . '</State>';
        $xml .= '<Zip5>' . $zip . '</Zip5>';
        $xml .= '<Zip4>' . '' . '</Zip4>';
        $xml .= '</Address>';
        $xml .= '</AddressValidateRequest>';

        $url = 'https://secure.shippingapis.com/ShippingAPI.dll?API=Verify&XML=' . urlencode($xml);
        $response = file_get_contents($url);

        $xml = simplexml_load_string($response) or Utils::json_response(["message" => "USPS Response error."], 500);
        $validatedAddress = array(
            'address1' => (string)$xml->Address[0]->Address1,
            'address2' => (string)$xml->Address[0]->Address2,
            'city' => (string)$xml->Address[0]->City,
            'state' => (string)$xml->Address[0]->State,
            'zip' => (string)$xml->Address[0]->Zip5, // . '-' . $xml->Address[0]->Zip4
        );
        if($validatedAddress['address1'] == '' || $validatedAddress['city' == '' || $validatedAddress['zip'] == '']){
            return Utils::json_response(["message" => "Invalid address!"], 400);
        }
        return Utils::json_response(['data' => $validatedAddress]); 
    }
}catch(\Exception $e){
    Utils::json_response(['message' => $e->getMessage()], 400);
}
?>
