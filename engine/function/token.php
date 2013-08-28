<?php
// List of characters: $, {}, []
	class Token {
		public static function generate() {
			$token = sha1(uniqid(time(), true));
			
			$_SESSION['token'] = $token;
		}
		/**
		 * Displays a random token to prevent CSRF attacks.
		 *
		 * @access public
		 * @static true
		 * @return void
		**/
		public static function create() {
			echo '<input type="hidden" name="token" value="' . self::get() . '" />';
		}


		/**
		 * Returns the active token, if there is one.
		 *
		 * @access public
		 * @static true
		 * @return mixed
		**/
		public static function get() {
			return isset($_SESSION['token']) ? $_SESSION['token'] : false;
		}


		/**
		 * Validates whether the active token is valid or not.
		 *
		 * @param  string $post
		 * @access public
		 * @static true
		 * @return boolean
		**/
		public static function isValid($post) {
			if (config('use_token')) {
				// Token doesn't exist yet, return false.
				if (!self::get()) {
					return false;
				}

				// Token was invalid, return false.
				if ($post == $_SESSION['old_token'] || $post == $_SESSION['token']) {
					//self::_reset();
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}


		/**
		 * Destroys the active token.
		 *
		 * @access protected
		 * @static true
		 * @return void
		**/
		protected static function _reset() {
			unset($_SESSION['token']);
		}


		/**
		 * Displays information on both the post token and the session token.
		 *
		 * @param  string $post
		 * @access public
		 * @static true
		 * @return void
		**/
		public static function debug($post) {
			echo '<pre>', var_dump(array(
				'post' => $post, 
				'old_token' => $_SESSION['old_token'],
				'token' => self::get()
			)), '</pre>';
		}
	}
?>