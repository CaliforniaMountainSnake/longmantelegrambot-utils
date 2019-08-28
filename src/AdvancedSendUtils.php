<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\SocialNetworksAPI\Telegram\Enums\ParseModeEnum;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

trait AdvancedSendUtils
{
    use SendUtils;
    use ConversationUtils;

    /**
     * Send a text message or edit the existed one.
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
}
