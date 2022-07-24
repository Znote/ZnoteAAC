<?php

class Cache
{
	protected $_file = false;
	protected $_lifespan = 0;
	protected $_content;
	protected $_memory = false;
	protected $_canMemory = false;

	const EXT = '.cache.php';


	/**
	 * @param  string $file
	 * @access public
	 * @return void
	**/
	public function __construct($file) {
		$cfg = config('cache');

		$this->setExpiration($cfg['lifespan']);
		if (function_exists('apcu_fetch')) {
			$this->_canMemory = true;
			$this->_memory = $cfg['memory'];
		}
		$this->_file = $file . self::EXT;

		if (!$this->_memory && $cfg['memory']) die("
			<p><strong>Configuration error!</strong>
			<br>Cannot save cache to memory, but it is configured to do so.
			<br>You need to enable PHP extension APCu to enable memory cache.
			<br>Install it or set \$config['cache']['memory'] to false!
			<br><strong>Ubuntu install:</strong> sudo apt install php-apcu</p>
		");
	}


	/**
	 * Sets the cache expiration limit (IMPORTANT NOTE: seconds, NOT ms!).
	 *
	 * @param  integer $span
	 * @access public
	 * @return void
	**/
	public function setExpiration($span) {
		$this->_lifespan = $span;
	}


	/**
	 * Enable or disable memory RAM storage.
	 *
	 * @param  bool $bool
	 * @access public
	 * @return bool $status
	**/
	public function useMemory($bool) {
		if ($bool and $this->_canMemory) {
			$this->_memory = true;
			return true;
		}
		$this->_memory = false;
		return false;
	}


	/**
	 * Set the content you'd like to cache.
	 *
	 * @param  mixed $content
	 * @access public
	 * @return void
	**/
	public function setContent($content) {
		$this->_content = (!$this->_memory && strtolower(gettype($content)) == 'array') ? json_encode($content) : $content;
	}


	/**
	 * Validates whether it is time to refresh the cache data or not.
	 *
	 * @access public
	 * @return boolean
	**/
	public function hasExpired() {
		if ($this->_memory) {
			return !apcu_exists($this->_file);
		}
		if (is_file($this->_file) && time() < filemtime($this->_file) + $this->_lifespan) {
			return false;
		}
		return true;
	}

	/**
	 * Returns remaining time before scoreboard will update itself.
	 *
	 * @access public
	 * @return integer
	**/
	public function remainingTime() {
		$remaining = 0;
		if ($this->_memory) {
			if (apcu_exists($this->_file)) {
				$meta = apcu_cache_info();
				foreach ($meta['cache_list'] AS $item) {
					if ($item['info'] == $this->_file) {
						$remaining = ($item['creation_time'] + $item['ttl']) - time();
						return ($remaining > 0) ? $remaining : 0;
					}
				}
			}
			return $remaining;
		}
		if (!$this->hasExpired()) {
			$remaining = (filemtime($this->_file) + $this->_lifespan) - time();
		}
		return $remaining;
	}


	/**
	 * Saves the content into its appropriate cache file.
	 *
	 * @access public
	 * @return void
	**/
	public function save() {
		if ($this->_memory) {
			return apcu_store($this->_file, $this->_content, $this->_lifespan);
		}
		$handle = fopen($this->_file, 'w');
		fwrite($handle, $this->_content);
		fclose($handle);
	}


	/**
	 * Loads the content from a specified cache file.
	 *
	 * @access public
	 * @return mixed
	**/
	public function load() {
		if ($this->_memory) {
			return apcu_fetch($this->_file);
		}
		if (!is_file($this->_file)) {
			return false;
		}

		ob_start();
		include_once($this->_file);
		$content = ob_get_clean();

		if (!isset($content) && strlen($content) == 0) {
			return false;
		}

		if ($content = json_decode($content, true)) {
			return (array) $content;
		} else {
			return $content;
		}
	}
}
