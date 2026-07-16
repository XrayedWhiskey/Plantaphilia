<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 06-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\GeoIp2\Exception;

/**
 * This class represents an error returned by MaxMind's GeoIP2
 * web service.
 */
class InvalidRequestException extends HttpException
{
    /**
     * The code returned by the MaxMind web service.
     *
     * @var string
     */
    public $error;

    public function __construct(
        string $message,
        string $error,
        int $httpStatus,
        string $uri,
        \Exception $previous = null
    ) {
        $this->error = $error;
        parent::__construct($message, $httpStatus, $uri, $previous);
    }
}
