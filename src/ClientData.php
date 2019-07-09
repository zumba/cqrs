<?php declare(strict_types = 1);

namespace Zumba\CQRS;

interface ClientData {

	/**
	 * Locale of the client issuing the DTO
	 */
	public function locale() : string;

	/**
	 * IP of the client issuing the DTO
	 */
	public function ip() : string;

	/**
	 * UserAgent of the client issuing the DTO
	 */
	public function userAgent() : string;

	/**
	 * MembershipType of the client issuing the DTO
	 */
	public function membershipType() : string;

	/**
	 * Properties for the DTO other than locale, ip, userAgent, or membershipType.
	 *
	 * @return array
	 */
	public function properties() : array;
}
