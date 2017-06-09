<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ROOT', dirname(__FILE__));
require_once "class/apiFunc.php";
require_once "class/Db.php";

const URL = "https://kudago.com/public-api";
const VERSION = "v1.3";
const ECATEGORY = 'event-categories';

$engine = new apiFunc();
$engine->updateEventCategory();
$engine->updatePlaceCategory();







