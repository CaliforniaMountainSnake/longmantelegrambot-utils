<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\LongmanTelegrambotUtils\Enums\TelegramMessageTypeEnum;
use CaliforniaMountainSnake\LongmanTelegrambotUtils\Logger\TelegrambotUtilsLogger;
use Longman\TelegramBot\Entities\File;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * This trait let you to download files from Telegram.
 */
trait TelegramFileUtils
{
    use TelegrambotUtilsLogger;

    /**
     * Download the file from message into the bot files directory.
     *
     * @param Message $_message           Telegram message.
     * @param string  $_bot_download_path Download path.
     *
     * @return string|null The path to the file or null if message does not contain a file with supported type.
     * @throws TelegramException
     * @throws \LogicException
     */
    protected function downloadTelegramFile(Message $_message, string $_bot_download_path): ?string
    {
        $fileId = $this->getFileIdFromMessage($_message);
        if ($fileId === null) {
            return null;
        }

        $file = Request::getFile(['file_id' => $fileId]);
        /** @var File $fileResult */
        $fileResult = $file->getResult();
        if ($file->isOk() && Request::downloadFile($fileResult)) {

            return $_bot_download_path . '/' . $fileResult->getFilePath();
        }

        throw new \LogicException('Error while downloading the file from Telegram: '
            . $file->getDescription() . ' Code: ' . $file->getErrorCode(), $file->getErrorCode());
    }

    /**
     * Get file id from Message.
     *
     * @param Message $_message Telegram message.
     *
     * @return string|null File ID or null if message type does not support.
     */
    protected function getFileIdFromMessage(Message $_message): ?string
    {
        $msgType = $_message->getType();
        switch ($msgType) {
            case TelegramMessageTypeEnum::STICKER:
            case TelegramMessageTypeEnum::AUDIO:
            case TelegramMessageTypeEnum::VOICE:
            case TelegramMessageTypeEnum::VIDEO:
            case TelegramMessageTypeEnum::VIDEO_NOTE:
            case TelegramMessageTypeEnum::ANIMATION:
            case TelegramMessageTypeEnum::DOCUMENT:
                return $_message->{'get' . \ucfirst($msgType)}()->getFileId();

            case TelegramMessageTypeEnum::PHOTO:
                $photos = $_message->getPhoto();
                $photo = \end($photos);
                return $photo->getFileId();

            case TelegramMessageTypeEnum::TEXT:
                return null;

            default:
                return null;
        }
    }
}
