<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client;

use Junco\Http\Message\Response as Response;

/**
 * Extends the response for easier processing.
 */
class ClientResponse extends Response implements ClientResponseInterface
{
	use ClientResponseTrait;
}
