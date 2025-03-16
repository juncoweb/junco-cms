<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client\Adapter;

use Junco\Http\Client\Builder\ResponseBuilder;
use Junco\Http\Client\Factory;

abstract class AdapterAbstract implements AdapterInterface
{
	/**
	 * Create Response Builder
	 *
	 * @return ResponseBuilder
	 */
	public function createResponseBuilder(): ResponseBuilder
	{
		$factory = new Factory();
		$stream =  $factory->createStreamFromResource(fopen('php://temp', 'w+b'));
		$response = $factory->createResponse()->withBody($stream);

		return new ResponseBuilder($response);
	}
}
