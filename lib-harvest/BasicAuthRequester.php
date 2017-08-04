<?php
/*
             DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.
 */

namespace pgddevil\Tools\Harvest;

class BasicAuthRequester implements Requester
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getRequest($url)
    {
        $curl = $this->initCurl($url);

        $headers = array("Content-Type:application/json", "Accept:application/json");
        $headers[] = "Authorization: Basic " . base64_encode($this->username . ":" . $this->password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTPGET, true);

        $response = $this->executeCurl($curl);

        curl_close($curl);

        return $response;

    }

    public function postRequest($url, $postFields)
    {
        $curl = $this->initCurl($url);

        $headers = array("Content-Type:application/json", "Accept:application/json");
        $headers[] = "Authorization: Basic " . base64_encode($this->username . ":" . $this->password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        $response = $this->executeCurl($curl);
        curl_close($curl);

        return $response;
    }

    private function initCurl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $curl;
    }

    private function executeCurl($curl)
    {
        $response = curl_exec($curl);

        $curl_errno = curl_errno($curl);
        if ($curl_errno) {
            throw new \Exception("Curl Error.  No=" . $curl_errno . ", Msg=" . curl_error($curl));
        }

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            throw new \Exception("Http Error.  No=" . $httpCode . ", Msg=" . $body);
        }

        return $body;
    }}