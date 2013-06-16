<?php

namespace Yay\Core\Encryption;

use Yay\Core\Encryption\Algorithm\Algorithm;
use Yay\Core\yComponent;

/**
 * Helper class for encryption.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Encryption
 */
class Encrypter extends yComponent
{
	public function __construct()
	{
	}

	/**
	 * Sets the encryption algorithm and returns it.
	 *
	 * @param \Yay\Core\Encryption\Algorithm\Algorithm $algorithm
	 * @return \Yay\Core\Encryption\Algorithm\Algorithm
	 */
	public function setAlgorithm(Algorithm $algorithm)
	{
		$this->registerComponent('algorithm', $algorithm);
		return $this->algorithm();
	}

	/**
	 * Gets the algorithm instance.
	 *
	 * @return \Yay\Core\Encryption\Algorithm\Algorithm
	 */
	public function algorithm()
	{
		return $this->component('algorithm');
	}
}