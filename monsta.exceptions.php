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

class WeirdCookieTaste extends Exception {
	protected static string $cookie_name = "";
	
	public function __construct(string $message = "", bool $append = false) {
		if(empty($message)) {
			$message = "The cookie: " . self::$cookie_name . " has a weird taste";
		}
		
		if($append) {
			$message = "The cookie: " . self::$cookie_name . " has a weird taste; $message";
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

class NoSuchModifier extends Exception {
	protected static string $cookie_name = "";
	
	public function __construct(
		string $modifier,
		string $message = "", 
		bool $append = false
	) {
		if(empty($message)) {
			$message = "No such modifier: $modifier in " . self::$cookie_name;
		}
		
		if($append) {
			$message = "No such modifier: $modifier in " . self::$cookie_name . "; $message";
		}
		
		parent::__construct($message);
	}
	
	public static function set_cookie(string $cookie_name) {
		self::$cookie_name = $cookie_name;
	}
}