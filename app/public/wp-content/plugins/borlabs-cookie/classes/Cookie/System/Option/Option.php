<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\Option;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\OptionDto;
use LogicException;

/**
 * Class Option.
 *
 * The **Option** class contains a collection of methods that allows to get and set options in WordPress. The purpose
 * of this class is to obtain a typed object ({@see \Borlabs\Cookie\Dto\System\OptionDto}) that can be passed to
 * methods instead of mixed data. This methods of this class rely on methods of the adapter class
 * {@see \Borlabs\Cookie\Adapter\WpFunction}.
 *
 * @see \Borlabs\Cookie\System\Option\Option::__construct
 * @see \Borlabs\Cookie\System\Option\Option::get
 * @see \Borlabs\Cookie\System\Option\Option::set
 * @see \Borlabs\Cookie\System\Option\Option::delete
 * @see \Borlabs\Cookie\System\Option\Option::deleteGlobal
 * @see \Borlabs\Cookie\System\Option\Option::getAll
 * @see \Borlabs\Cookie\System\Option\Option::getGlobal
 * @see \Borlabs\Cookie\System\Option\Option::getThirdPartyOption
 * @see \Borlabs\Cookie\System\Option\Option::setGlobal
 * @see \Borlabs\Cookie\System\Option\Option::setThirdPartyOption
 * @see \Borlabs\Cookie\System\Option\Option::ensureValidOptionName
 * @see \Borlabs\Cookie\Dto\System\OptionDto
 */
final class Option
{
    public const OPTION_PREFIX = 'BorlabsCookie';

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(WpDb $wpdb, WpFunction $wpFunction)
    {
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    /**
     * This method deletes previously stored data of the current website.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param null|string $languageCode {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function delete(string $name, ?string $languageCode = null): bool
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return $this->wpFunction->deleteOption($wpOptionName);
    }

    /**
     * This method deletes previously stored data independently of the current website.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param null|string $languageCode {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function deleteGlobal(string $name, ?string $languageCode = null): bool
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return $this->wpFunction->deleteGlobalOption($wpOptionName);
    }

    /**
     *  This method returns previously stored data of the current website.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param mixed       $default      optional; Default: `false`; Default value if the option does not exist
     * @param null|string $languageCode {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function get(string $name, $default = false, ?string $languageCode = null): OptionDto
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return new OptionDto($name, $this->wpFunction->getOption($wpOptionName, $default), false, $languageCode);
    }

    /**
     * This method returns previously stored data of all languages of the current website.
     *
     * @param string $name The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     *
     * @return array<OptionDto>
     */
    public function getAll(string $name): array
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name;

        $optionDtoList = [];
        $options = $this->wpdb->get_results(
            '
                SELECT
                    `option_name`,
                    `option_value`
                FROM
                    `' . $this->wpdb->options . '`
                WHERE
                    `option_name` LIKE \'' . $wpOptionName . '%\'
            ',
        );

        if (!is_array($options)) {
            return $optionDtoList;
        }

        foreach ($options as $option) {
            $language = null;
            $languageMatch = [];

            if (preg_match('/^(.*)\_(([a-z]{2,3})((-|_)[a-zA-Z]{2,})?)$/', $option->option_name, $languageMatch)) {
                $language = $languageMatch[3];
            }
            $optionDtoList[] = new OptionDto(
                $name,
                $option->option_value,
                false,
                $language,
            );
        }

        return $optionDtoList;
    }

    /**
     * This method returns previously stored data independently of the current website. The data must be set beforehand
     * via {@see \Borlabs\Cookie\System\Option\Option::setGlobal}.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param mixed       $default      optional; Default: `false`; Default value if the option does not exist
     * @param null|string $languageCode {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function getGlobal(string $name, $default = false, ?string $languageCode = null): OptionDto
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return new OptionDto($name, $this->wpFunction->getSiteOption($wpOptionName, $default), false, $languageCode);
    }

    /**
     * This method return previously stored data of the current website through third-party plugins.
     *
     * @param string $name    the name of the option
     * @param mixed  $default optional; Default: `false`; Default value if the option does not exist
     */
    public function getThirdPartyOption(string $name, $default = false): OptionDto
    {
        return new OptionDto($name, $this->wpFunction->getOption($name, $default));
    }

    /**
     * This method stores all serializable data in the database of the current website.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param mixed       $value        {@see \Borlabs\Cookie\Dto\System\OptionDto::$value}
     * @param bool        $autoload     optional; Default: `false`; `true`: The option is loaded when WordPress starts up
     * @param null|string $languageCode {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function set(string $name, $value, bool $autoload = false, ?string $languageCode = null): bool
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return $this->wpFunction->updateOption($wpOptionName, $value, $autoload);
    }

    /**
     * This method stores all serializable data in the database independently of the current website. This is useful
     * when the information is needed across all instances of a multisite network.
     *
     * @param string      $name         The name must match `[A-Z]+[a-zA-Z]+`. {@see \Borlabs\Cookie\Dto\System\OptionDto::$name}
     * @param mixed       $value        {@see \Borlabs\Cookie\Dto\System\OptionDto::$value}
     * @param null|string $languageCode $languageCode  {@see \Borlabs\Cookie\Dto\System\OptionDto::$language}
     */
    public function setGlobal(string $name, $value, ?string $languageCode = null): bool
    {
        $this->ensureValidOptionName($name);
        $wpOptionName = self::OPTION_PREFIX . $name . (is_string($languageCode) ? '_' . $languageCode : '');

        return $this->wpFunction->updateSiteOption($wpOptionName, $value);
    }

    /**
     * This method stores all serializable data from third-party plugins in the database of the current website.
     * The difference between this method and {@see \Borlabs\Cookie\System\Option\Option::set} is that in this method
     * the option prefix of Borlabs Cookie is not prepended to the option name.
     *
     * @param string $name     the name of the option
     * @param mixed  $value    {@see \Borlabs\Cookie\Dto\System\OptionDto::$value}
     * @param bool   $autoload optional; Default: `false`; `true`: The option is loaded when WordPress starts up
     */
    public function setThirdPartyOption(string $name, $value, bool $autoload = false): bool
    {
        return $this->wpFunction->updateOption($name, $value, $autoload);
    }

    /**
     * This method ensures that an option name matches `[A-Z]+[a-zA-Z]+`.
     */
    private function ensureValidOptionName(string $name): void
    {
        if (preg_match('/[A-Z]+[a-zA-Z]+/', $name) === false) {
            throw new LogicException('The option name is not valid. A valid option name must match `[A-Z]+[a-zA-Z]+`.', E_USER_ERROR);
        }
    }
}
