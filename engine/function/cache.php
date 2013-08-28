<?php

	class Cache
	{
		protected $_file = false;
		protected $_lifespan = 0;
		protected $_content;

		const EXT = '.cache.php';


		/**
		 * @param  string $file
		 * @access public
		 * @return void
		**/
		public function __construct($file) {
			$this->_file = $file . self::EXT;
			$this->setExpiration(config('cache_lifespan'));
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
		 * Set the content you'd like to cache.
		 *
		 * @param  mixed $content
		 * @access public
		 * @return void
		**/
		public function setContent($content) {
			switch (strtolower(gettype($content))) {
				case 'array':
					$this->_content = json_encode($content);
					break;

				default:
					$this->_content = $content;
					break;
			}
		}


		/**
		 * Validates whether it is time to refresh the cache data or not.
		 *
		 * @access public
		 * @return boolean
		**/
		public function hasExpired() {
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
