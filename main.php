<?php
require_once "./json_maid.php";
require_once "./monsta.exceptions.php";
require_once "./monsta.modifiers.php";
require_once "./monsta.php";

use CookieMonsta\Monsta;

$monsta = new Monsta();

include $monsta->feed_on_cookie("./templates/index.cookie");