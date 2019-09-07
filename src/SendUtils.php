<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\LongmanTelegrambotUtils\Entities\Mediafile;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\ParseModeEnum;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\TelegramInputMediaTypesEnum;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * Утилиты для удобной отправки сообщений в чат Telegram.
 */
trait SendUtils
{
    /**
     * @return string
     */
    abstract protected function getChatId(): string;

    /**
     * @return Conversation|null
     */
    abstract protected function getConversation(): ?Conversation;

    /**
     * @return ParseModeEnum
     */
    protected function getDefaultParseMode(): ParseModeEnum
    {
        return ParseModeEnum::HTML();
    }

    /**
     * @return ServerResponse
     */
    protected function emptyResponse(): ServerResponse
    {
        return Request::emptyResponse();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Use this method to send text messages. On success, the sent Message is returned.
     *
     * @param string             $_text
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendTextMessage(
        string $_text,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        $params = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)($_parse_mode ?? $this->getDefaultParseMode()),
            'text' => $this->getValidationErrorsString($_errors) . $_text,
        ];
        $_reply_markup !== null && $params['reply_markup'] = $_reply_markup;

        return Request::sendMessage($params);
    }

    /**
     * Send document.
     *
     * @deprecated Use sendMediafile().
     *
     * @param string             $_caption
     * @param string             $_filename
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendDocument(
        string $_caption,
        string $_filename,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        return $this->sendMediafile(new Mediafile(TelegramInputMediaTypesEnum::DOCUMENT(), $_filename, $_caption,
            $_parse_mode), null, $_reply_markup, $_chat_id);
    }

    /**
     * Use this method to send a various types of media files.
     *
     * @param Mediafile     $_mediafile
     * @param array|null    $_errors
     * @param Keyboard|null $_reply_markup
     * @param string|null   $_chat_id
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendMediafile(
        Mediafile $_mediafile,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null
    ): ServerResponse {
        if ($_errors !== null) {
            $_mediafile->setCaption($this->getValidationErrorsString($_errors) . $_mediafile->getCaption() ?? '');
        }

        $type = (string)$_mediafile->getType();
        $params = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)$_mediafile->getParseMode(),
            $type => Request::encodeFile($_mediafile->getMediafile()),
        ];

        $_mediafile->getCaption() !== null && $params['caption'] = $_mediafile->getCaption();
        $_reply_markup !== null && $params['reply_markup'] = $_reply_markup;

        return Request::send('send' . \ucfirst($type), $params);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Use this method to edit text and game messages.
     * On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned.
     *
     * @param string             $_message_id
     * @param string             $_text
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     */
    protected function editMessageText(
        string $_message_id,
        string $_text,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        $params = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)($_parse_mode ?? $this->getDefaultParseMode()),
            'message_id' => $_message_id,
            'text' => $this->getValidationErrorsString($_errors) . $_text,
        ];
        $_reply_markup !== null && $params['reply_markup'] = $_reply_markup;

        return Request::editMessageText($params);
    }

    /**
     * Use this method to edit animation, audio, document, photo, or video messages.
     * If a message is a part of a message album, then it can be edited only to a photo or a video.
     * Otherwise, message type can be changed arbitrarily. When inline message is edited, new file can't be uploaded.
     * Use previously uploaded file via its file_id or specify a URL.
     * On success, if the edited message was sent by the bot, the edited Message is returned, otherwise True is returned.
     *
     * @param string        $_message_id
     * @param Mediafile     $_mediafile
     * @param array|null    $_errors
     * @param Keyboard|null $_reply_markup
     * @param string|null   $_chat_id
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function editMessageMedia(
        string $_message_id,
        Mediafile $_mediafile,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null
    ): ServerResponse {
        if ($_errors !== null) {
            $_mediafile->setCaption($this->getValidationErrorsString($_errors) . $_mediafile->getCaption() ?? '');
        }

        $params = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'message_id' => $_message_id,
            'media' => $_mediafile->toLongmanInputMedia(),
        ];
        $_reply_markup !== null && $params['reply_markup'] = $_reply_markup;

        return Request::editMessageMedia($params);
    }

    /**
     * Use this method to delete a message, including service messages, with the following limitations:
     * - A message can only be deleted if it was sent less than 48 hours ago.
     * - Bots can delete outgoing messages in private chats, groups, and supergroups.
     * - Bots can delete incoming messages in private chats.
     * - Bots granted can_post_messages permissions can delete outgoing messages in channels.
     * - If the bot is an administrator of a group, it can delete any message there.
     * - If the bot has can_delete_messages permission in a supergroup or a channel, it can delete any message there.
     * Returns True on success.
     *
     * @param string      $_message_id
     * @param string|null $_chat_id
     *
     * @return ServerResponse
     */
    protected function deleteMessage(string $_message_id, ?string $_chat_id = null): ServerResponse
    {
        return Request::deleteMessage([
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'message_id' => $_message_id,
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Remove the keyboard.
     *
     * @param string             $_text
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function removeKeyboard(
        string $_text,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        $params = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)($_parse_mode ?? $this->getDefaultParseMode()),
            'reply_markup' => Keyboard::remove(['selective' => true]),
            'text' => $_text
        ];

        return Request::sendMessage($params);
    }

    /**
     * @param string|null $_chat_id
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function forceRemoveKeyboard(?string $_chat_id = null): ServerResponse
    {
        $chatId = $_chat_id ?? $this->getChatId();
        $response = $this->removeKeyboard('Delete keyboard', $chatId);
        return $this->deleteMessage($response->getResult()->getMessageId(), $chatId);
    }

    /**
     * @param             $_object
     * @param string|null $_description
     * @param string|null $_chat_id
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendVarExportDebugMessage(
        $_object,
        ?string $_description = null,
        ?string $_chat_id = null
    ): ServerResponse {
        $chatId = $_chat_id ?? $this->getChatId();
        return $this->sendTextMessage(($_description ?? '')
            . \var_export($_object, true), null, null, $chatId);
    }

    /**
     * @param string|null $_chat_id
     *
     * @return ServerResponse
     */
    protected function sendTypingAction(?string $_chat_id = null): ServerResponse
    {
        // Send typing action.
        return Request::sendChatAction([
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'action' => 'typing',
        ]);
    }

    /**
     * Получить объект клавиатуры, пригодный для отправки Telegram.
     *
     * @param array $_keyboard Массив клавиатуры.
     *
     * @return Keyboard Объект клавиатуры.
     */
    protected function getKeyboardObject(array $_keyboard): Keyboard
    {
        $keyboard = new Keyboard (...$_keyboard);
        $keyboard->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(false);
        return $keyboard;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get errors string from array of errors.
     *
     * @param array|null $_errors Multidimensional errors array.
     *
     * @return string Result string with errors.
     */
    protected function getValidationErrorsString(?array $_errors): string
    {
        if ($_errors === null) {
            return '';
        }

        $result = '';
        \array_walk_recursive($_errors, static function ($value) use (&$result) {
            $result .= '<b>' . $value . "</b>\n";
        });

        return $result;
    }
}
