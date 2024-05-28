<?php
namespace CookieMonsta;

class Monsta {
	# the template name
	private string $cookie_name = "";
	# the template content
	private string $cookie = "";
	# the variables passed to the template
	private array $cookie_flavour = [];
	# born to store the template rendering process
	private array $digestion_process = [];

	public function __construct() {
		$this->spit_all_out();
	}
	
	/**
	 * feed a cookie to the monsta
	 *
	 * @throws CookieNotEdible
	 * @throws CookieNotAvailable
	 */
	public function feed_a_cookie(
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
			if(preg_match("/^\{\! begin .* \!\}$/", trim($content))) {
				$this->detect_opening($content, $line);
			} elseif(preg_match("/^\{\! end .* \!\}$/", trim($content))) {
				$this->detect_closure($content, $line);
			}
		}
	}
	
	# render the chewed content
	private function digest(): string {
		$result = "";
		
		foreach($this->digestion_process as $process) {
			$result .= $this->parse_content($process["startline"], $process["endline"], $this->cookie_flavour);
		}
		
		return $result;
	}
	
	private function detect_opening(string $opening, int $line) {
		$eval = $opening;
		preg_match("/^(\w+)/", str_replace("{! begin ", "", $opening), $matches);
		
		if(!empty($matches)) {
			$opening = $matches[0];
		}
		
		$data = [
			"startline" => $line,
			"endline" => 0,
			"cookie_name" => $this->cookie_name,
			"content" => ""
		];
		
		switch($opening) {
			case "if":
				$data["type"] = "if";
				break;
			case "else":
				$data["type"] = "else";
				break;
			case "foreach":
				$data["type"] = "foreach";
				break;
		}
		
		$eval = str_replace(
			"{! begin $opening",
			"",
			preg_replace(
				"/\!\}$/",
				"",
				$eval
			)
		);
		
		$data["condition"] = $eval;
		
		$this->digestion_iter[] = $data;
	}
	
	/**
	 * @throws BadCookieDough
	 */
	private function detect_closure(string $closure, int $line) {
		$last_opening = end($this->digestion_iter);
		preg_match("/^(\w+)/", str_replace("{! end ", "", $closure), $matches);
		
		if(!empty($matches)) {
			$closure = $matches[0];
		} else {
			throw new BadCookieDough("Closure malformed for '{$last_opening->type}' at line $line, in {$last_opening->cookie_name}");
		}
		
		# to not make it throw an error when it finds an "else" condition
		if($last_opening->type === $closure
			|| ($last_opening->type === "if" && $closure === "else")) {
			$last_opening["endline"] = $line;
			array_pop($this->digestion_process);
			$this->digestion_process[] = $last_opening;
		} else {
			throw new BadCookieDough("Closure mismatched for '{$last_opening->type}' at line {$last_opening->startline}, in {$last_opening->cookie_name}");
		}
	}
	
	private function parse_content(int $startline, int $endline, array $params) {
		$lines = explode("\n", $this->cookie);
		$result = "";
		
		for($x = $startline;$x <= $endline;$x++) {
			$line = $lines[$x];
			foreach($params as $name => $value) {
				$result .= preg_replace("/\{\{\$" . $name . "\}\}/", $value, $line);
			}
		}
		
		return $result;
	}
}