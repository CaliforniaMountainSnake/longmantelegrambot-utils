<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;

trait ConversationUtils
{
    /**
     * Get command name.
     * @return string
     */
    abstract public static function getCommandName(): string;

    /**
     * @return Conversation|null
     */
    abstract protected function getConversation(): ?Conversation;

    /**
     * @param Conversation|null $_new_conversation_state
     */
    abstract protected function setConversation(?Conversation $_new_conversation_state): void;

    /**
     * Идентификатор текущего состояния conversation.
     * @return string
     */
    abstract protected function getStateNoteName(): string;

    /**
     * @return User
     */
    abstract protected function getTelegramUser(): User;

    /**
     * @return int
     */
    abstract protected function getChatId(): int;

    /**
     * @param string $_new_state
     *
     * @throws TelegramException
     */
    protected function setConversationState(string $_new_state): void
    {
        if ($this->getNote($this->getStateNoteName()) === $_new_state) {
            return;
        }

        $this->setConversationNotes([$this->getStateNoteName() => $_new_state]);
    }

    /**
     * Set permanent variables for the current conversation.
     *
     * @param array $_notes_arr Array with notes.
     *
     * @throws TelegramException
     */
    protected function setConversationNotes(array $_notes_arr): void
    {
        foreach ($_notes_arr as $key => $value) {
            $this->getConversation()->notes[$key] = $value;
        }
        $this->getConversation()->update();
    }

    /**
     * Unset permanent variables for the current conversation.
     *
     * @param array $_note_keys_arr Array of the notes's KEYS.
     *
     * @throws TelegramException
     */
    protected function deleteConversationNotes(array $_note_keys_arr): void
    {
        foreach ($_note_keys_arr as $value) {
            unset ($this->getConversation()->notes[$value]);
        }
        $this->getConversation()->update();
    }

    /**
     * Вернуть значение переменной conversation->notes.
     * Алиас для более быстрого обращения.
     *
     * @param string $_note Ключ массива notes.
     *
     * @return mixed|null Значение переменной или null, если она не существует.
     */
    protected function getNote(string $_note)
    {
        return ($this->getConversation()->notes[$_note] ?? null);
    }

    /**
     * @throws TelegramException
     */
    protected function startConversation(): void
    {
        $this->setConversation(new Conversation ($this->getTelegramUser()->getId(), $this->getChatId(),
            self::getCommandName()));
    }

    /**
     * @throws TelegramException
     */
    protected function stopConversation(): void
    {
        if ($this->getConversation() !== null) {
            $this->getConversation()->stop();
        }
    }
}
