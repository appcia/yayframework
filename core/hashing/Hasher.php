<?php

namespace Yay\Core\Hashing;

use Yay\Core\Hashing\Algorithm\Algorithm;
use Yay\Core\yComponent;

/**
 * Helper class for hashing.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Hashing
 */
class Hasher extends yComponent
{
	public function __construct()
	{
	}

	/**
	 * Sets the algorithm instance.
	 *
	 * @param \Yay\Core\Hashing\Algorithm\Algorithm $algorithm
	 * @return \Yay\Core\Hashing\Algorithm\Algorithm
	 */
	public function setAlgorithm(Algorithm $algorithm)
	{
		$this->registerComponent('algorithm', $algorithm);
		return $this->algorithm();
	}

	/**
	 * Gets the algorithm instance.
	 *
	 * @return \Yay\Core\Hashing\Algorithm\Algorithm
	 */
	public function algorithm()
	{
		return $this->component('algorithm');
	}

	/**
	 * Generates a password hash. It's length will be the length set in the algorithm instance.
	 * This method always adds a salt to the password. If you want to change the salt length,
	 * use $hasher->algorithm()->setSaltLength(x). You can get the generated salt by calling
	 * $hasher->algorithm()->lastSalt().
	 *
	 * @param $password
	 * @return string
	 */
	public function generatePassword($password)
	{
		if (!$this->algorithm()->lastSalt())
			$this->algorithm()->generateSalt();

		return $this->algorithm()->hash($password);
	}

	/**
	 * Generates a unique token hash. Uses the algorithm's generateUnique() method.
	 *
	 * @param int $length
	 * @return string
	 */
	public function generateUniqueToken($length = 32)
	{
		return $this->algorithm()->generateUnique();
	}
}