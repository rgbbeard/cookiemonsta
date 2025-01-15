<?php
namespace CookieMonsta;

class Monsta {
	# the template name
	private string $cookie_name = "";
	private string $cookie_tag = "";
	# the template content
	private string $cookie = "";
	# the variables passed to the template
	private array $cookie_flavour = [];
	# to store the template rendering process
	private array $digestion_process = [];

	private const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	
	private array $accepted_modifiers = [];

	private const cache_path = "/home/davide/programs/cookiemonsta";
	private const cache_file = self::cache_path . "/cached.json";

	private const opening_pattern = "^\{\\\[a-z]+\s";
	private const begin_condition_pattern = self::opening_pattern . ".*\s\!\}$";
	private const end_condition_pattern = "^\{\/[a-z]+\!\}$";
	private const display_pattern = "\{\=\s.*\s\=\}";

	public function __construct() {
		$this->spit_all_out();

		if(!file_exists(self::cache_file)) {
			$handle = fopen(self::cache_file, "c");
			fclose($handle);
		}
	}
	
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

	private function is_cached(): bool {
		# TODO: write this function
		return false;
	}
	
	/**
	 * reads the cookie content
	 * @throws BadCookieDough
	 */
	private function chew() {
		foreach(explode("\n", $this->cookie) as $line => $content) {
			$line += 1;
			$clean_content = trim($content);

			if(preg_match("/" . self::opening_pattern . "/", $clean_content, $matches)) {
				if(!$this->check_opening($clean_content, $line)) {
					BadCookieDough::set_cookie($cookie_name);
					throw new BadCookieDough();
				}

				$this->transcribe_opening($clean_content, $line);
			} elseif(preg_match("/" . self::end_condition_pattern . "/", $clean_content, $matches)) {
				$this->transcribe_closure($clean_content, $line);
			} elseif(preg_match("/" . self::display_pattern . "/", $clean_content, $matches)) {
				$this->transcribe_display($clean_content, $line);
			} else {
				# plain html
				# TODO: complete this part
			}
		}
	}
	
	# render the chewed content
	private function digest(): string {
		return "";
	}

	private function generate_cookie_tag(): string {
		$tag = "";

		for($x = 0;$x <= 20;$x++) {
			$num = mt_rand(0, strlen(self::charset));
			$tag .= substr(self::charset, $num, 1);
		}
		
		return $tag;
	}

	private function check_opening(
		string $opening, 
		int $line
	) {
		return preg_match("/" . self::begin_condition_pattern . "/", $opening);
	}
	
	private function transcribe_opening(
		string $opening, 
		int $line
	) {
		var_dump($opening);
	}

	private function transcribe_closure(
		string $closure, 
		int $line
	) {
		var_dump($closure, $line);
	}

	/**
	 * @throws NoSuchModifier
	 */
	private function is_modifier(string $modifier): bool {
		return false;
	}

	/**
	 * @throws WeirdCookieTaste
	 */
	private function transcribe_display(
		string $display,
		int $line
	) {
		# remove tags
		$display = str_replace("{= ", "", $display);
		$display = str_replace(" =}", "", $display);

		$modifiers = explode("|", $display);

		if(count($modifiers) > 1) {
			foreach($modifiers as $modifier) {
				# is modifier supported
				if(!$this->is_modifier($modifier)) {

				}

				# is modifier valid?
				if(false) {

				}
			}
		}

		var_dump($modifiers);
	}
}