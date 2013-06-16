<?php

namespace Yay\Core\Response;

/**
 * Interface for response classes.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Response
 */
interface iResponse
{
	/**
	 * Gets the serialized response data.
	 *
	 * @return mixed
	 */
	function data();

	/**
	 * Sends the response. If $exitAfter is true, it calls exit() after data sending.
	 *
	 * @param bool $exitAfter
	 */
	function send($exitAfter = false);
}