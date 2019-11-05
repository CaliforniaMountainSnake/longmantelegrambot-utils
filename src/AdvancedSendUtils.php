<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\LongmanTelegrambotUtils\Entities\Mediafile;
use CaliforniaMountainSnake\LongmanTelegrambotUtils\Enums\TelegramMessageTypeEnum;
use CaliforniaMountainSnake\LongmanTelegrambotUtils\Logger\TelegrambotUtilsLogger;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\ParseModeEnum;
use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\TelegramResponse;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

trait AdvancedSendUtils
{
    use TelegrambotUtilsLogger;
    use SendUtils;
    use ConversationUtils;

    /**
     * @param ServerResponse $_response
     *
     * @return TelegramResponse
     */
    protected function convertServerResponseToTelegramResponse(ServerResponse $_response): TelegramResponse
    {
        return new TelegramResponse($_response->getRawData());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Send a text message or edit the existed one.
     * !!!Warning!!! Ensure you don't try update the message to the equaled one.
     *
     * @param string             $_unique_conversation_note
     * @param string             $_text
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     * @throws TelegramException
     * @deprecated Use showAnyMessage().
     *
     */
    protected function showTextMessage(
        string $_unique_conversation_note,
        string $_text,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        $messageId = $this->getNote($_unique_conversation_note);

        // Send message.
        if ($messageId === null) {
            $msg = $this->sendTextMessage($_text, $_errors, $_reply_markup, $_chat_id, $_parse_mode);
            $this->setConversationNotes([$_unique_conversation_note => $msg->getResult()->getMessageId()]);
            return $msg;
        }

        // Edit message.
        return $this->editMessageText($messageId, $_text, $_errors, $_reply_markup, $_chat_id, $_parse_mode);
    }

    /**
     * Use this method to show the ANY types of messages.
     * Send a message or edit the existed one.
     *
     * @param string             $_unique_msg_token
     * @param string|null        $_text
     * @param Mediafile|null     $_mediafile
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     * @param bool               $_is_force_del_and_send Always just delete old message and send a new one.
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function showAnyMessage(
        string $_unique_msg_token,
        ?string $_text,
        ?Mediafile $_mediafile = null,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null,
        bool $_is_force_del_and_send = false
    ): ServerResponse {
        $this->assertMessageParamsAreCompatible($_text, $_mediafile);
        $this->assertConversationIsStarted();
        $_mediafile !== null && $_parse_mode !== null && $_mediafile->setParseMode($_parse_mode);
        [$previousMsgId, $previousMsgType] = $this->getPrevMsgData($_unique_msg_token);

        // Удаляем сообщение, если задан флаг жесткого переудаления.
        if ($previousMsgId !== null && $_is_force_del_and_send) {
            $this->deleteMessage($previousMsgId, $_chat_id);
        }

        // Если последнего сообщения не существует или задан флаг жесткого переудаления, просто отправляем.
        if ($previousMsgId === null || $_is_force_del_and_send) {
            // Отправляем текст.
            if ($_mediafile === null) {
                return $this->updatePrevMsgData($_unique_msg_token,
                    $this->sendTextMessage($_text, $_errors, $_reply_markup, $_chat_id, $_parse_mode));
            }

            // Отправляем медиафайл.
            return $this->updatePrevMsgData($_unique_msg_token,
                $this->sendMediafile($_mediafile, $_errors, $_reply_markup, $_chat_id));
        }

        // Сообщение существует, редактируем.
        // Предыдущее сообщение было текстовым.
        if ($previousMsgType === TelegramMessageTypeEnum::TEXT) {
            // Текущее сообщение текстовое.
            if ($_mediafile === null) {
                return $this->updatePrevMsgData($_unique_msg_token,
                    $this->editMessageText($previousMsgId, $_text, $_errors, $_reply_markup, $_chat_id, $_parse_mode));
            }

            // Текущее сообщение с медиафайлом - необходимо удалить и послать заново.
            $this->deleteMessage($previousMsgId, $_chat_id);
            return $this->updatePrevMsgData($_unique_msg_token,
                $this->sendMediafile($_mediafile, $_errors, $_reply_markup, $_chat_id));
        }

        // Предыдущее сообщение было с медиафайлом.
        // Текущее сообщение текстовое - необходимо удалить и послать заново.
        if ($_mediafile === null) {
            $this->deleteMessage($previousMsgId, $_chat_id);
            return $this->updatePrevMsgData($_unique_msg_token,
                $this->sendTextMessage($_text, $_errors, $_reply_markup, $_chat_id, $_parse_mode));
        }

        // Текущее сообщение с медиафайлом.
        return $this->updatePrevMsgData($_unique_msg_token,
            $this->editMessageMedia($previousMsgId, $_mediafile, $_errors, $_reply_markup, $_chat_id));
    }

    /**
     * Delete a previous message and send a new one.
     *
     * @param string             $_unique_msg_token
     * @param string|null        $_text
     * @param Mediafile|null     $_mediafile
     * @param array|null         $_errors
     * @param Keyboard|null      $_reply_markup
     * @param string|null        $_chat_id
     * @param ParseModeEnum|null $_parse_mode
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    protected function forceShowAnyMessage(
        string $_unique_msg_token,
        ?string $_text,
        ?Mediafile $_mediafile = null,
        ?array $_errors = null,
        ?Keyboard $_reply_markup = null,
        ?string $_chat_id = null,
        ?ParseModeEnum $_parse_mode = null
    ): ServerResponse {
        return $this->showAnyMessage($_unique_msg_token, $_text, $_mediafile, $_errors, $_reply_markup, $_chat_id,
            $_parse_mode, true);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param string $_unique_msg_token
     *
     * @return string[]|null[] [msg_id, msg_type]
     */
    protected function getPrevMsgData(string $_unique_msg_token): array
    {
        $notePreviousMsgId = $_unique_msg_token . '_prev_msg_id';
        $notePreviousMsgType = $_unique_msg_token . '_prev_msg_type';
        return [
            $this->getNote($notePreviousMsgId),
            $this->getNote($notePreviousMsgType),
        ];
    }

    /**
     * @param string $_unique_msg_token
     *
     * @throws TelegramException
     */
    protected function deletePrevMsgData(string $_unique_msg_token): void
    {
        $notePreviousMsgId = $_unique_msg_token . '_prev_msg_id';
        $notePreviousMsgType = $_unique_msg_token . '_prev_msg_type';
        $this->setConversationNotes([
            $notePreviousMsgId => null,
            $notePreviousMsgType => null,
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param string         $_unique_msg_token
     * @param ServerResponse $_send_response
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    private function updatePrevMsgData(string $_unique_msg_token, ServerResponse $_send_response): ServerResponse
    {
        if ($this->isEqualMessageEditError($_send_response)) {
            return $this->emptyResponse();
        }

        /** @var Message $result */
        $result = $_send_response->getResult();
        $notePreviousMsgId = $_unique_msg_token . '_prev_msg_id';
        $notePreviousMsgType = $_unique_msg_token . '_prev_msg_type';

        $this->setConversationNotes([
            $notePreviousMsgId => $result->getMessageId(),
            $notePreviousMsgType => $result->getType(),
        ]);

        return $_send_response;
    }

    /**
     * @param string|null    $_text
     * @param Mediafile|null $_mediafile
     *
     * @throws \RuntimeException
     */
    private function assertMessageParamsAreCompatible(?string $_text, ?Mediafile $_mediafile): void
    {
        if ($_text === null && $_mediafile === null) {
            throw new \RuntimeException('You must specify at least a text or a mediafile!');
        }
    }

    /**
     * Check if the telegram response contains the error
     * that appears in case of editing the message to the exactly the same.
     *
     * (Bad Request: message is not modified: specified new message content and reply markup
     * are exactly the same as a current content and reply markup of the message).
     *
     * @param ServerResponse $_response
     *
     * @return bool
     */
    private function isEqualMessageEditError(ServerResponse $_response): bool
    {
        if ($_response->isOk()) {
            return false;
        }

        return \stripos($_response->getDescription(), 'are exactly the same') !== false;
    }
}
