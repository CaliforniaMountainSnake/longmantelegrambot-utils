<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

trait AdvancedConversationUtils
{
    use TelegramUtils;
    use ConversationUtils;
    use SendUtils;

    /**
     * Run the method stored in the "state" conversation's note or the given default method.
     *
     * @param callable $_first_state_method Default method.
     *
     * @return ServerResponse
     * @throws TelegramException
     * @see ConversationUtils::getStateNoteName
     */
    protected function executeConversation(callable $_first_state_method): ServerResponse
    {
        // Conversation start
        $this->startConversation();

        // Call needed state method
        $stateMethod = $this->getNote($this->getStateNoteName());
        if ($stateMethod === null) {
            return $_first_state_method($this->message, $this->text);
        }

        return $this->{$stateMethod} ($this->message, $this->text);
    }
}
