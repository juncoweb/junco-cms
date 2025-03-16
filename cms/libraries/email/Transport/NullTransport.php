<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email\Transport;

class NullTransport extends TransportAbstract
{
	/**
	 * Send
	 * 
	 * @param Email  $Email
	 * 
	 * @return bool
	 */
	public function send($Email): bool
	{
		if ($this->debug) {
			$this->debug_log[] = '-subject: ' . $Email->getSubject();
			foreach (['from', 'return_path', 'reply_to', 'to', 'cc', 'bcc'] as $batch) {
				$addresses = $Email->getBatch($batch);
				if ($addresses) {
					$this->debug_log[] = '-' . $batch . ': ' . $addresses;
				}
			}
			$this->debug_log[] = '';
			$this->debug_log[] = $Email->getHeader() . $Email->getBody();
		}

		return true;
	}
}
