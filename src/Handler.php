<?php

namespace Zumba\CQRS;

interface Handler {

	/**
	 * Handle a DTO
	 */
	public function handle(DTO $dto) : Response;
}
