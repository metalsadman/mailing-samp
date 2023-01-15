<?php
require_once('validation/ClientBuilder.php');                                       
require_once('validation/us_street/Lookup.php');                                    
require_once('validation/StaticCredentials.php');                                   
use SmartyStreets\PhpSdk\Exceptions\SmartyException;                                                                    
use SmartyStreets\PhpSdk\StaticCredentials;                                                                             
use SmartyStreets\PhpSdk\ClientBuilder;                                                                                 
use SmartyStreets\PhpSdk\US_Street\Lookup; 

class Validator {
    public function validate(string $street, string $city, string $state, string $zip) {                                                                                             
        $authID = "MockId";                                                               
        $authToken = "MockToken";                                                                            
                                                                                                                        
        $staticCredentials = new StaticCredentials($authID, $authToken);                                                
        $client = (new ClientBuilder($staticCredentials))->buildUsStreetApiClient();                                    
                                                                                                                        
        $lookup = new Lookup();                                                                                         
        $lookup->setStreet($street);                                                                               
        $lookup->setCity($city);
        $lookup->setState($state);
        $lookup->setZipcode($zip);
        $lookup->setMaxCandidates(3);
        $lookup->setMatchStrategy(Lookup::STRICT);      
                                                                                                                        
        try {                                                                                                           
            $client->sendLookup($lookup);     
            return $this->displayResults($lookup);    
        }                                                                                                               
        catch (SmartyException $ex) {                                                                                   
            throw new \Exception($ex->getMessage());
        }                                                                                                               
        catch (\Exception $ex) {                                                                                        
            throw new \Exception($ex->getMessage());
        }                                                                                                                  
    }

    public function displayResults(Lookup $lookup) {
        $results = $lookup->getResult();

        if (count($results) < 1) {
            throw new \Exception('Address is invalid.');
        }

        try{
            $firstCandidate = $results[0];
            return [
                'street' => $firstCandidate->getDeliveryLine1(),
                'city' => $firstCandidate->getComponents()->getCityName(),
                'state' => $firstCandidate->getComponents()->getStateAbbreviation(),
                'zip' => $firstCandidate->getComponents()->getZipcode(),
            ];
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}
