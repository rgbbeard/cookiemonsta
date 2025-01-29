<?php
namespace CookieMonsta;

use \stdClass;
use Maids\JSONMaid;

class Monsta {
	private const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	private Modifiers $modifiers;
	private JSONMaid $jsonMaid;

	private const base_path = "/home/davide/programs/cookiemonsta";

	private const cache_file = self::base_path . "/cached.json";
	private array $cached_files = [];
	# parse always
	private bool $needs_reparsing = true;

	private const target_dir = self::base_path . "/generated";

	# the template name
	private string $cookie_name = "";
	private string $cookie_tag = self::target_dir . "/";
	# the template content
	private string $cookie = "";
	# the variables passed to the template
	private array $cookie_flavour = [];
	# to store the template rendering process
	private array $digestion_process = [];

	private const opening_pattern = "^\{\\\[a-z]+\s";
	private const begin_condition_pattern = self::opening_pattern . ".*\s\!\}$";
	private const end_condition_pattern = "^\{\/[a-z]+\!\}$";
	private const display_pattern = "\{\=\s.*?\s\=\}";
	private const declaration_pattern = "\{\%\s.*\s\%\}";

	public function __construct() {
		$this->modifiers = new Modifiers();

		$this->clean_mouth();

		if(!file_exists(self::cache_file)) {
			$handle = fopen(self::cache_file, "c");
			fwrite($handle, json_encode([]));
			fclose($handle);
		} else {
			try {
				$this->load_cache();
			} catch(Exception $e) {
				throw new Exception("Invalid cache file");
			}
		}
	}
	
	/**
	 * feed a cookie to the monsta
	 *
	 * @throws CookieNotEdible
	 * @throws CookieNotAvailable
	 * @throws BadCookieDough
	 */
	public function feed_on_cookie(string $cookie_name): string {
		$this->cookie_name = $cookie_name;

		if(!$this->is_cached()) {
			$this->cookie_tag .= $this->generate_cookie_tag();
		} elseif(!$this->needs_reparsing) {
			return $this->cookie_tag;
		}

		if($this->is_available()) {
			$this->cookie = file_get_contents($cookie_name);

			if($this->is_edible()) {
				$this->chew();
				$this->digest();
				return $this->cookie_tag;
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
	public function clean_mouth() {
		$this->cookie_name = "";
		$this->cookie_tag = self::target_dir . "/";
		$this->cookie_flavour = [];
		$this->cookie = "";
		$this->digestion_process = [];
		$this->cached_files = [];
	}

	private function is_edible(): bool {
		return !empty($this->cookie);
	}

	private function is_available(): bool {
		return file_exists($this->cookie_name);
	}

	private function is_cached(): bool {
		if(!empty($this->cached_files)) {
			foreach($this->cached_files as $cache) {
				if($cache->template === $this->cookie_name) {
					$this->cookie_tag = $cache->generated_file;
					$this->needs_reparsing = strlen(file_get_contents($this->cookie_name)) !== $cache->template_content_size;
					return true;
				}
			}
		}

		return false;
	}

	private function load_cache() {
		$this->jsonMaid = new JSONMaid(self::cache_file);

		$this->cached_files = $this->jsonMaid->get_records();
	}

	private function cache_cookie() {
		if(!$this->is_cached() || $this->needs_reparsing) {
			$this->cached_files[] = [
				"template" => $this->cookie_name,
				"generated_file" => $this->cookie_tag,
				"template_content_size" => filesize($this->cookie_name)
			];

			$handle = fopen(self::cache_file, "w");
			fwrite($handle, json_encode($this->cached_files));
			fclose($handle);

			$this->load_cache();
		}
	}
	
	/**
	 * reads the cookie content
	 * 
	 * @throws BadCookieDough
	 * @throws WeirdCookieTaste
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
			} elseif(preg_match_all("/" . self::display_pattern . "/", $clean_content, $matches)) {
				$this->transcribe_display($clean_content, $matches[0], $line);
			} elseif(preg_match("/" . self::declaration_pattern . "/", $clean_content, $matches)) {
				$this->transcribe_declaration($clean_content, $line);
			} else {
				$this->digestion_process[] = $clean_content . PHP_EOL;
			}
		}
	}

	private function digest() {
		$handle = fopen($this->cookie_tag, "w+");

		foreach($this->digestion_process as $index => $line) {
			if($index === count($this->digestion_process) - 1) {
				$line = rtrim($line);
			}

			fwrite($handle, $line, strlen($line));
		}

		fclose($handle);

		$this->cache_cookie();
	}

	private function generate_cookie_tag(): string {
		$tag = "";

		for($x = 0;$x <= 20;$x++) {
			$num = mt_rand(0, strlen(self::charset));
			$tag .= substr(self::charset, $num, 1);
		}

		$tag .= ".php";

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
		# remove tags
		$opening = str_replace("{\\", "", $opening);
		$opening = str_replace(" !}", "", $opening);

		preg_match("/^[a-z]+\s/", $opening, $keywords);

		$condition = str_replace($keywords[0], "", $opening);
		$keyword = trim($keywords[0]);

		$this->digestion_process[] = "<?php $keyword($condition) { ?>" . PHP_EOL;
	}

	private function transcribe_closure(
		string $closure, 
		int $line
	) {
		$this->digestion_process[] = "<?php } ?>" . PHP_EOL;
	}

	/**
	 * @throws WeirdCookieTaste
	 */
	private function transcribe_display(
		string $display,
		array $matches,
		int $line
	) {
		$display = str_replace("{= ", "<?php echo ", $display);
		$display = str_replace(" =}", "; ?>", $display);

		$this->digestion_process[] = $display . PHP_EOL;
	}

	private function transcribe_declaration(
		string $declaration,
		int $line
	) {
		# remove tags
		$declaration = str_replace("{% ", "", $declaration);
		$declaration = str_replace(" %}", "", $declaration);

		$this->digestion_process[] = "<?php $declaration; ?>" . PHP_EOL;
	}
}