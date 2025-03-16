<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Emitter;

use Psr\Http\Message\ResponseInterface;

/**
 * Emitter
 */
interface EmitterInterface
{
	/**
	 * Emit a response.
	 */
	public function emit(ResponseInterface $response);
}
