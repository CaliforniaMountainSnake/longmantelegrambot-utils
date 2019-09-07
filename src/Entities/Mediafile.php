<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils\Entities;

use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\ParseModeEnum;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\TelegramInputMediaTypesEnum;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\InputMedia;
use Longman\TelegramBot\Entities\InputMedia\InputMedia as LongmanInputMedia;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * Mediafile class represents the files that can be edited with editMessageMedia().
 */
class Mediafile extends InputMedia
{
    /**
     * Mediafile constructor.
     *
     * @param TelegramInputMediaTypesEnum $_type
     * @param string                      $_local_filename
     * @param string|null                 $_caption
     * @param ParseModeEnum|null          $_parse_mode
     */
    public function __construct(
        TelegramInputMediaTypesEnum $_type,
        string $_local_filename,
        ?string $_caption = null,
        ?ParseModeEnum $_parse_mode = null
    ) {
        parent::__construct($_type, $_local_filename, $_caption, $_parse_mode);
    }

    /**
     * @return LongmanInputMedia
     * @throws TelegramException
     */
    public function toLongmanInputMedia(): LongmanInputMedia
    {
        $arr = ['media' => Request::encodeFile($this->mediafile)];
        if ($this->caption !== null) {
            $arr['caption'] = $this->caption;
            $arr['parse_mode'] = $this->parseMode;
        }

        $classname = LongmanInputMedia::class . \ucfirst((string)$this->type);
        return new $classname ($arr);
    }
}
