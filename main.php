<?php
require_once "./monsta.exceptions.php";
require_once "./monsta.php";

use CookieMonsta\Monsta;
use CookieMonsta\CookieNotAvailable;
use CookieMonsta\CookieNotEdible;

$monsta = new Monsta();

try {
	echo $monsta->feed_on_cookie(
		"./index.cookie",
		[]
	);
} catch (CookieNotAvailable|CookieNotEdible $e) {
	die($e->getMessage());
}