<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Utils intended for the comfortable manipulating of the Conversation object.
 */
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

        $this->getConversationLogger()->info('Conversation notes have been set', $_notes_arr);
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

        $this->getConversationLogger()->info('Conversation notes have been deleted', $_note_keys_arr);
    }

    /**
     * Get conversation note's value.
     *
     * @param string $_note The key of the conversation->notes array.
     *
     * @return mixed|null The value of a variable or null if it does not exists.
     */
    protected function getNote(string $_note)
    {
        $value = ($this->getConversation()->notes[$_note] ?? null);
        $this->getConversationLogger()->debug('Conversation note "' . $_note . '" returned', $value);
        return $value;
    }

    /**
     * Get all conversation's notes.
     *
     * @return array
     */
    protected function getNotes(): array
    {
        $values = $this->getConversation()->notes;
        $this->getConversationLogger()->debug('All conversation notes returned', $values);
        return $values;
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

        $this->getConversationLogger()->info('Conversation has been started', $conversationParams);
    }

    /**
     * @throws TelegramException
     */
    protected function stopConversation(): void
    {
        if ($this->getConversation() !== null) {
            $this->getConversation()->stop();
        }
        $this->getConversationLogger()->info('Conversation has been stopped');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param LoggerInterface $_logger
     */
    public function setConversationLogger(LoggerInterface $_logger): void
    {
        $this->conversationLogger = $_logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getConversationLogger(): LoggerInterface
    {
        if ($this->conversationLogger === null) {
            $this->conversationLogger = new NullLogger();
        }
        return $this->conversationLogger;
    }
}
