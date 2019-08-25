<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\ParseModeEnum;
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

    /**
     * Use this method to send text messages. On success, the sent Message is returned.
     *
     * @param string             $_text
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse                  Ответ Telegram.
     * @throws TelegramException
     */
    protected function sendTextMessage(
        string $_text,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        $data = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)($_parse_mode ?? $this->getDefaultParseMode()),
            'text' => $this->getValidationErrorsString($_errors) . $_text,
        ];

        if ($_reply_markup !== null) {
            $data['reply_markup'] = $_reply_markup;
        }

        return Request::sendMessage($data);
    }

    /**
     * Отослать документ.
     * (Режим парсинга: HTML).
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
        $data = [
            'chat_id' => $_chat_id ?? $this->getChatId(),
            'parse_mode' => (string)($_parse_mode ?? $this->getDefaultParseMode()),
            'caption' => $_caption,
            'document' => Request::encodeFile($_filename),
        ];

        if ($_reply_markup !== null) {
            $data['reply_markup'] = $_reply_markup;
        }

        return Request::sendDocument($data);
    }

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
     * @param string|null $_chat_id
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendVarExportDebugMessage($_object, string $_chat_id = null): ServerResponse
    {
        $chatId = $_chat_id ?? $this->getChatId();
        return $this->sendTextMessage(\var_export($_object, true), null, null, $chatId);
    }

    /**
     * Edit Message Text.
     *
     * The official telegram docs say:
     * Please note, that it is currently only possible to edit messages without reply_markup or with inline keyboards.
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

        if ($_reply_markup !== null) {
            $params['reply_markup'] = $_reply_markup;
        }

        return Request::editMessageText($params);
    }

    /**
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
     * @param array|null $_errors Массив с ошибками, любой размерности.
     *
     * @return string Результирующая строка с ошибками.
     */
    private function getValidationErrorsString(?array $_errors): string
    {
        if ($_errors === null) {
            return '';
        }

        $result = '';
        \array_walk_recursive($_errors, static function ($value, $key) use (&$result) {
            $result .= '<b>' . $value . "</b>\n";
        });

        return $result;
    }
}
