<?php

namespace pgddevil\Tools\Harvest;

interface Requester
{
    function getRequest($url);
    function postRequest($url, $postFields);
}