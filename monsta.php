<?php
namespace CookieMonsta;

class Monsta {
	# the template name
	private string $cookie_name = "";
	# the template content
	private string $cookie = "";
	# the variables passed to the template
	private array $cookie_flavour = [];
	# to store the template rendering process
	private array $digestion_process = [];

	public function __construct() {
		$this->spit_all_out();
	}

	private const opening_pattern = "^\{\! \\.* \!\}$";
	private const closure_pattern = "^\{\! \/.* \!\}$";
	
	/**
	 * feed a cookie to the monsta
	 *
	 * @throws CookieNotEdible
	 * @throws CookieNotAvailable
	 */
	public function feed_on_cookie(
		string $cookie_name,
		array $params = []
	): string {
		$this->cookie_name = $cookie_name;
		$this->cookie_flavour = $params;

		if($this->is_available()) {
			$this->cookie = file_get_contents($cookie_name);

			if($this->is_edible()) {
				$this->chew();
				return $this->digest();
			} else {
				CookieNotEdible::set_cookie($cookie_name);
				throw new CookieNotEdible();
			}
		} else {
			CookieNotAvailable::set_cookie($cookie_name);
			throw new CookieNotAvailable();
		}
	}

	# reset the rendering process
	public function spit_all_out() {
		$this->cookie_name = "";
		$this->cookie_flavour = [];
		$this->cookie = "";
		$this->digestion_process = [];
	}

	private function is_edible(): bool {
		return !empty($this->cookie);
	}

	private function is_available(): bool {
		return file_exists($this->cookie_name);
	}
	
	# read the cookie content
	private function chew() {
		foreach(explode("\n", $this->cookie) as $line => $content) {
			$line += 1;
			if(preg_match("/" . self::opening_pattern . "/", trim($content))) {
				$this->detect_opening($content, $line);
			} elseif(preg_match("/" . self::closure_pattern . "/", trim($content))) {
				$this->detect_closure($content, $line);
			}
		}
	}
	
	# render the chewed content
	private function digest(): string {
		return "";
	}
	
	/**
	 * @throws BadCookieDough
	 */
	private function detect_opening(
		string $opening, 
		int $line
	) {
		var_dump($opening);
	}
	
	/**
	 * @throws BadCookieDough
	 */
	private function detect_closure(
		string $closure, 
		int $line
	) {
		var_dump($closure, $line);
	}
	
	private function parse_content() {
		
	}
}