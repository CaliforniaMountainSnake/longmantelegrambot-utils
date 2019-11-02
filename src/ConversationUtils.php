<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;

trait ConversationUtils
{
    /**
     * @var LoggerInterface
     */
    protected $conversationLogger;

    /**
     * Get command name.
     *
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
     *
     * @return string
     */
    abstract protected function getStateNoteName(): string;

    /**
     * @return User
     */
    abstract protected function getTelegramUser(): User;

    /**
     * @return string
     */
    abstract protected function getChatId(): string;

    /**
     * @throws \RuntimeException
     */
    protected function assertConversationIsStarted(): void
    {
        if ($this->getConversation() === null) {
            throw new \RuntimeException('You must start a conversation before!');
        }
    }

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

        if ($this->conversationLogger !== null) {
            $this->conversationLogger->debug('Conversation notes have been set', $_notes_arr);
        }
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

        if ($this->conversationLogger !== null) {
            $this->conversationLogger->debug('Conversation notes have been deleted', $_note_keys_arr);
        }
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
     * Get all conversation's notes.
     *
     * @return array
     */
    protected function getNotes(): array
    {
        return $this->getConversation()->notes;
    }

    /**
     * Return the current conversation's state value.
     *
     * @return mixed|null
     */
    protected function getState()
    {
        return $this->getNote($this->getStateNoteName());
    }

    /**
     * @throws TelegramException
     */
    protected function startConversation(): void
    {
        $conversationParams = [$this->getTelegramUser()->getId(), $this->getChatId(), static::getCommandName()];
        $this->setConversation(new Conversation (...$conversationParams));

        if ($this->conversationLogger !== null) {
            $this->conversationLogger->debug('Conversation has been started', $conversationParams);
        }
    }

    /**
     * @throws TelegramException
     */
    protected function stopConversation(): void
    {
        if ($this->getConversation() !== null) {
            $this->getConversation()->stop();
        }

        if ($this->conversationLogger !== null) {
            $this->conversationLogger->debug('Conversation has been stopped');
        }
    }

    /**
     * @param LoggerInterface $_logger
     */
    public function setConversationLogger(LoggerInterface $_logger): void
    {
        $this->conversationLogger = $_logger;
    }
}
