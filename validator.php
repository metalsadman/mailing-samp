<?php
require_once('validation/ClientBuilder.php');                                       
require_once('validation/us_street/Lookup.php');                                    
require_once('validation/StaticCredentials.php');                                   
use SmartyStreets\PhpSdk\Exceptions\SmartyException;                                                                    
use SmartyStreets\PhpSdk\StaticCredentials;                                                                             
use SmartyStreets\PhpSdk\ClientBuilder;                                                                                 
use SmartyStreets\PhpSdk\US_Street\Lookup; 

class Validator {
    public function validate(string $add1, string $add2, string $city, string $state, string $zip) {                                                                                             
        $authID = "76dcfe78-8452-2077-26e2-7daf57e4b55d";                                                               
        $authToken = "fpvRbmj4rQFkKkmPAqbP";                                                                            
                                                                                                                        
        $staticCredentials = new StaticCredentials($authID, $authToken);                                                
        $client = (new ClientBuilder($staticCredentials))->buildUsStreetApiClient();                                    
                                                                                                                        
        $lookup = new Lookup();                                                                                         
        $lookup->setStreet($add1);                                                                               
        $lookup->setStreet2($add2);                                                                               
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
            // Candidate api doesn't have getAddress1() and getAddress2() methods anymore :/
            // see validation\US_Street\Candidate.php file
            // they only offer getDeliveryLine1() and getDeliveryLine2() now
            // but getDeliveryLine1 returns both address1 and address2 on the same string
            // I couldn't find anything from their new api to get each seperately
            // so I had to improvise to get address 1 and address 2 fields
            $addressLine = explode('ST ', strtoupper($firstCandidate->getDeliveryLine1()));
            return [
                'address1' => trim($addressLine[0]),
                'address2' => trim($addressLine[1]),
                'city' => strtoupper($firstCandidate->getComponents()->getCityName()),
                'state' => strtoupper($firstCandidate->getComponents()->getStateAbbreviation()),
                'zip' => strtoupper($firstCandidate->getComponents()->getZipcode()),
            ];
            // return [
            //     'address1' => strtoupper($firstCandidate->getDeliveryLine1()),
            //     'address2' => $firstCandidate->getDeliveryLine2(),
            //     'city' => strtoupper($firstCandidate->getComponents()->getCityName()),
            //     'state' => strtoupper($firstCandidate->getComponents()->getStateAbbreviation()),
            //     'zip' => strtoupper($firstCandidate->getComponents()->getZipcode()),
            // ];
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}
