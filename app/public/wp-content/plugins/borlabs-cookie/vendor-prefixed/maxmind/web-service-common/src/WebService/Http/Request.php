<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 06-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\MaxMind\WebService\Http;

/**
 * Interface Request.
 *
 * @internal
 */
interface Request
{
    public function __construct(string $url, array $options);

    public function post(string $body): array;

    public function get(): array;
}
