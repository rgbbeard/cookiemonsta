<?php
require_once "./monsta.exceptions.php";
require_once "./monsta.modifiers.php";
require_once "./monsta.php";

use CookieMonsta\Monsta;
use CookieMonsta\CookieNotAvailable;
use CookieMonsta\CookieNotEdible;

$monsta = new Monsta();

try {
	$monsta->feed_on_cookie(
		"./templates/index.cookie",
		[]
	);
} catch (CookieNotAvailable|CookieNotEdible $e) {
	die($e->getMessage());
}