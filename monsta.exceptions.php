<?php 
namespace CookieMonsta;

use \Exception;

class CookieNotAvailable extends Exception {
	protected static string $cookie_name = "";

	public function __construct(string $message = "", bool $append = false) {
		if(empty($message)) {
			$message = "Cookie not available: " . self::$cookie_name;
		}
		
		if($append) {
			$message = "Cookie not available: " . self::$cookie_name . "; $message";
		}

		parent::__construct($message);
	}

	public static function set_cookie(string $cookie_name) {
		self::$cookie_name = $cookie_name;
	}
}

class CookieNotEdible extends Exception {
	protected static string $cookie_name = "";
	
	public function __construct(string $message = "", bool $append = false) {
		if(empty($message)) {
			$message = "Cookie not edible: " . self::$cookie_name;
		}
		
		if($append) {
			$message = "Cookie not edible: " . self::$cookie_name . "; $message";
		}

		parent::__construct($message);
	}

	public static function set_cookie(string $cookie_name) {
		self::$cookie_name = $cookie_name;
	}
}

class BadCookieDough extends Exception {
	protected static string $cookie_name = "";
	
	public function __construct(string $message = "", bool $append = false) {
		if(empty($message)) {
			$message = "Cookie not edible: " . self::$cookie_name;
		}
		
		if($append) {
			$message = "Cookie not edible: " . self::$cookie_name . "; $message";
		}
		
		parent::__construct($message);
	}
	
	public static function set_cookie(string $cookie_name) {
		self::$cookie_name = $cookie_name;
	}
}