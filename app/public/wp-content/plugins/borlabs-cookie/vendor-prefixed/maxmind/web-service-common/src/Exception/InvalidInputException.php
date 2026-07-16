<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 06-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\MaxMind\Exception;

/**
 * This class represents an error in creating the request to be sent to the
 * web service. For example, if the array cannot be encoded as JSON or if there
 * is a missing or invalid field.
 */
class InvalidInputException extends WebServiceException
{
}
