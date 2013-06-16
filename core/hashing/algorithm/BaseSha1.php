<?php

namespace Yay\Core\Hashing\Algorithm;

/**
 * Algorithm class for basic sha1 hashing.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Hashing\Algorithm
 */
class BaseSha1 extends Algorithm
{
	/**
	 * Construct. Sets hash length to 40 (sha1 hash length), salt length to 32 (md5 hash length).
	 */
	public function __construct()
	{
		$this->setHashLength(40);
		$this->setSaltLength(32);
	}

	/**
	 * Generates a hash from $input. If $salt is empty, lastSalt() will be used as salt,
	 * this way it can be an empty string, or generated before hashing. You can set the
	 * hash length by calling setHashLength(x).
	 *
	 * @param $input
	 * @param string $salt
	 * @return string
	 */
	public function hash($input, $salt = '')
	{
		return substr(sha1($input . (!$salt ? $this->lastSalt() : $salt)), -$this->hashLength());
	}
}