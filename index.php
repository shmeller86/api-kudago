<?php
//http://ipinfodb.com/ip_location_api.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ROOT', dirname(__FILE__));
require_once "class/apiFunc.php";
require_once "class/CalcDistance.php";
require_once "class/Db.php";

const URL = "https://kudago.com/public-api";
const VERSION = "v1.3";
const ECATEGORY = 'event-categories';

$engine = new apiFunc();
$calc = new CalcDistance();
//$engine->updateEventCategory();
//$engine->updatePlaceCategory();
//$engine->getPlaceFromRadiys('59.911556','30.267808');

$engine->getPlaceMeta();

//echo $calc->calculateTheDistance('59.9341625','30.334574','59.93476347','30.33078969');










