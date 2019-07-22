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
    abstract protected function getChatId(): int;

    abstract protected function getConversation(): ?Conversation;

    /**
     * Отослать текстовое сообщение.
     * (Режим парсинга: HTML).
     *
     * @param string $_msg Текст сообщения.
     * @param array|null $_errors
     * @param Keyboard|null $_reply_markup Клавиатура. Не обязательно.
     *
     * @return ServerResponse                  Ответ Telegram.
     * @throws TelegramException
     */
    protected function sendTextMessage(
        string $_msg,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null
    ): ServerResponse {
        $data = [
            'chat_id' => $this->getChatId(),
            'parse_mode' => (string)ParseModeEnum::HTML(),
            'text' => $this->getValidationErrorsString($_errors) . $_msg,
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
     * @param string $_caption
     * @param string $_filename
     * @param Keyboard|null $_reply_markup
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendDocument(
        string $_caption,
        string $_filename,
        ?Keyboard $_reply_markup = null
    ): ServerResponse {
        $data = [
            'chat_id' => $this->getChatId(),
            'parse_mode' => (string)ParseModeEnum::HTML(),
            'caption' => $_caption,
            'document' => Request::encodeFile($_filename),
        ];

        if ($_reply_markup !== null) {
            $data['reply_markup'] = $_reply_markup;
        }

        return Request::sendDocument($data);
    }


    /**
     * Получить объект клавиатуры, пригодный для отправки Telegram.
     *
     * @param   array $_keyboard Массив клавиатуры.
     *
     * @return  Keyboard                Объект клавиатуры.
     */
    protected function getKeyboardObject(array $_keyboard): Keyboard
    {
        $keyboard = new Keyboard (...$_keyboard);
        $keyboard->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(false);
        return $keyboard;
    }

    /**
     * Remove the keyboard.
     *
     * @param string $_text
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function removeKeyboard(string $_text): ServerResponse
    {
        $params = [
            'chat_id' => $this->getChatId(),
            'parse_mode' => (string)ParseModeEnum::HTML(),
            'reply_markup' => Keyboard::remove(['selective' => true]),
            'text' => $_text
        ];

        return Request::sendMessage($params);
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function forceRemoveKeyboard(): ServerResponse
    {
        $response = $this->removeKeyboard('Delete keyboard');
        return $this->deleteMessage($response->getResult()->getMessageId());
    }

    /**
     * @param $_object
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function sendVarExportDebugMessage($_object): ServerResponse
    {
        return $this->sendTextMessage(\var_export($_object, true));
    }

    /**
     * Edit Message Text.
     *
     * The official telegram docs say:
     * Please note, that it is currently only possible to edit messages without reply_markup or with inline keyboards.
     *
     * @param string $_message_id
     * @param string $_text
     * @param Keyboard|null $_reply_markup
     * @return ServerResponse
     */
    protected function editMessageText(
        string $_message_id,
        string $_text,
        ?Keyboard $_reply_markup = null
    ): ServerResponse {
        $params = [
            'chat_id' => $this->getChatId(),
            'parse_mode' => (string)ParseModeEnum::HTML(),
            'message_id' => $_message_id,
            'text' => $_text
        ];

        if ($_reply_markup !== null) {
            $params['reply_markup'] = $_reply_markup;
        }

        return Request::editMessageText($params);
    }

    /**
     * @param string $_message_id
     * @return ServerResponse
     */
    protected function deleteMessage(string $_message_id): ServerResponse
    {
        return Request::deleteMessage([
            'chat_id' => $this->getChatId(),
            'message_id' => $_message_id,
        ]);
    }

    /**
     * @return ServerResponse
     */
    protected function sendTypingAction(): ServerResponse
    {
        // Send typing action.
        return Request::sendChatAction([
            'chat_id' => $this->getChatId(),
            'action' => 'typing',
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param array|null $_errors Массив с ошибками, любой размерности.
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
