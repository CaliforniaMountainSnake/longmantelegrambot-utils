<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils\Enums;

use MyCLabs\Enum\Enum;

/**
 * The chat types, in which bot can work.
 */
class TelegramChatTypeEnum extends Enum
{
    public const PRIVATE_CHAT    = 'private';
    public const GROUP_CHAT      = 'group';
    public const SUPERGROUP_CHAT = 'supergroup';
    public const CHANNEL         = 'channel';

    //--------------------------------------------------------------------------
    // These methods are just for IDE autocomplete and not are mandatory.
    //--------------------------------------------------------------------------
    public static function PRIVATE_CHAT(): self
    {
        return new static(static::PRIVATE_CHAT);
    }

    public static function GROUP_CHAT(): self
    {
        return new static(static::GROUP_CHAT);
    }

    public static function SUPERGROUP_CHAT(): self
    {
        return new static(static::SUPERGROUP_CHAT);
    }

    public static function CHANNEL(): self
    {
        return new static(static::CHANNEL);
    }
}
