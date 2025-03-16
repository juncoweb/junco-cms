<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email\Transport;

use Email;

interface TransportInterface
{
	/**
	 * Send
	 * 
	 * @param Email  $Email
	 * 
	 * @return bool
	 */
	public function send(Email $Email): bool;
}
