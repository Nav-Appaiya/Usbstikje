<?php

/**
 * A GoogleUrlShortener Library in PHP
 */

class GoogleUrlShortener {

    private $curl;

    private $endpoint;

    function __construct($apiKey = null) {

        if($apiKey != null) {
            $this->endpoint = "https://www.googleapis.com/urlshortener/v1/url?key=".$apiKey."&";
        }else{
            $this->endpoint = "https://www.googleapis.com/urlshortener/v1/url?";
        }

        $this->curl = $this->_getCurl($this->endpoint);

    }

    public function shorten($data){

        if( is_array($data) ) {

            $shortLinks = array();

                foreach( $data as $link ) {

                    $shortLinks[] = $this->shorten($link);

                }

            return $shortLinks;

        }else{

            $postFields = json_encode( array( 'longUrl' => $data ) );

            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postFields);

            return json_decode(curl_exec($this->curl));

        }

    }

    public function expand($data , $projection = "FULL"){

        if( is_array($data) ) {

            $longLinks = array();

                foreach ( $data as $link ) {

                    $longLinks[] = $this->expand($link);

                }

            return $longLinks;

        }else{

           curl_setopt($this->curl, CURLOPT_URL, $this->endpoint.'shortUrl='.$data."&projection=".$projection);

           return json_decode(curl_exec($this->curl));

        }

    }

    private function _getCurl($curlOptUrl){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlOptUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        return $curl;

    }

}