<?php
/**
 * Session management
 * @since 1.9.0
 */
if ( !class_exists( 'CV_Session' ) ) {

	class CV_Session {

		static function _enabled() {
			return apply_filters( PT_CV_PREFIX_ . 'session_enable', true );
		}

		static function start() {
			if ( self::_enabled() && !headers_sent() && !session_id() && @is_writable( session_save_path() ) ) {
				@session_start();
			}
		}

		static function get( $key, $default = false ) {
			if ( self::_enabled() && isset( $_SESSION[ $key ] ) ) {
				$val = $_SESSION[ $key ];
			} else {
				$trans	 = get_transient( $key );
				$val	 = $trans ? $trans : $default;
			}

			return $val;
		}

		static function set( $key, $val ) {
			if ( self::_enabled() && self::_valid() ) {
				$_SESSION[ $key ] = $val;
			}

			if ( !self::_enabled() || !isset( $_SESSION[ $key ] ) ) {
				set_transient( $key, $val, HOUR_IN_SECONDS );
			}
		}

		static function _valid() {
			$sid = session_id();
			if ( $sid === "" ) {
				return false;
			} else {
				return preg_match( '/[^-,a-zA-Z0-9]+/', $sid ) === 0;
			}
		}

	}

}