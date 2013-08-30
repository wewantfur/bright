<?php
namespace bright\core\utils;

/**
 * Password hashing with PBKDF2.
 * Author: havoc AT defuse.ca
 * www: https://defuse.ca/php-pbkdf2.htm
 */
class PasswordUtils {
	// These constants may be changed without breaking existing hashes.
	const PBKDF2_HASH_ALGORITHM = "sha256";
	const PBKDF2_ITERATIONS = 1000;
	const PBKDF2_SALT_BYTE_SIZE = 24;
	const PBKDF2_HASH_BYTE_SIZE = 24;

	const HASH_SECTIONS = 4;
	const HASH_ALGORITHM_INDEX = 0;
	const HASH_ITERATION_INDEX = 1;
	const HASH_SALT_INDEX = 2;
	const HASH_PBKDF2_INDEX = 3;

	public static function create_hash($password) {
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv(PasswordUtils::PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
		return PasswordUtils::PBKDF2_HASH_ALGORITHM . ":" . PasswordUtils::PBKDF2_ITERATIONS . ":" .  $salt . ":" .
				base64_encode(PasswordUtils::_pbkdf2(
						PasswordUtils::PBKDF2_HASH_ALGORITHM,
						$password,
						$salt,
						PasswordUtils::PBKDF2_ITERATIONS,
						PasswordUtils::PBKDF2_HASH_BYTE_SIZE,
						true
				));
	}

	public static function validate_password($password, $correct_hash) {
		$params = explode(":", $correct_hash);
		if(count($params) < PasswordUtils::HASH_SECTIONS)
			return false;
		$pbkdf2 = base64_decode($params[PasswordUtils::HASH_PBKDF2_INDEX]);
		return PasswordUtils::_slow_equals(
				$pbkdf2,
				PasswordUtils::_pbkdf2(
						$params[PasswordUtils::HASH_ALGORITHM_INDEX],
						$password,
						$params[PasswordUtils::HASH_SALT_INDEX],
						(int)$params[PasswordUtils::HASH_ITERATION_INDEX],
						strlen($pbkdf2),
						true
				)
		);
	}

	/**
	 * Compares two strings $a and $b in length-constant time.
	 * @param String $a
	 * @param String $b
	 * @return boolean
	 */
	private static function _slow_equals($a, $b) {
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}

	/**
	 *  PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 * @param string $algorithm The hash algorithm to use. Recommended: SHA256
	 * @param string $password The password
	 * @param string $salt A salt that is unique to the password
	 * @param int $count Iteration count. Higher is better, but slower. Recommended: At least 1000
	 * @param int $key_length The length of the derived key in bytes
	 * @param boolean $raw_output If true, the key is returned in raw binary format. Hex encoded otherwise
	 * @return String A $key_length-byte key derived from the password and salt
	 */
	private static function _pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)	{
		$algorithm = strtolower($algorithm);
		if(!in_array($algorithm, hash_algos(), true))
			die('PBKDF2 ERROR: Invalid hash algorithm.');
		if($count <= 0 || $key_length <= 0)
			die('PBKDF2 ERROR: Invalid parameters.');

		$hash_length = strlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);

		$output = "";
		for($i = 1; $i <= $block_count; $i++) {
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
			}
			$output .= $xorsum;
		}

		if($raw_output)
			return substr($output, 0, $key_length);
		else
			return bin2hex(substr($output, 0, $key_length));
	}
}