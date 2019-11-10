<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\LongmanTelegrambotUtils\Logger\TelegrambotUtilsLogger;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;

/**
 * Utils intended for the comfortable manipulating of the Conversation object.
 */
trait ConversationUtils
{
    use TelegrambotUtilsLogger;

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
     * @return string
     */
    protected function getConversationGroupsNoteName(): string
    {
        return 'conversation_groups';
    }

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
     * @param array       $_notes_arr  The array with notes.
     * @param string|null $_group_name The name of conversation's group.
     *
     * @throws TelegramException
     */
    protected function setConversationNotes(array $_notes_arr, ?string $_group_name = null): void
    {
        foreach ($_notes_arr as $key => $value) {
            if ($_group_name !== null) {
                $this->getConversation()->notes[$this->getConversationGroupsNoteName()][$_group_name][$key] = $value;
            } else {
                $this->getConversation()->notes[$key] = $value;
            }
        }
        $this->getConversation()->update();

        $this->getTelegrambotUtilsLogger()->info('Conversation notes have been set',
            ['group_name' => $_group_name, 'notes' => $_notes_arr]);
    }

    /**
     * Unset permanent variables for the current conversation.
     *
     * @param array       $_note_keys_arr Array of the notes's KEYS.
     * @param string|null $_group_name    The name of conversation's group.
     *
     * @throws TelegramException
     */
    protected function deleteConversationNotes(array $_note_keys_arr, ?string $_group_name = null): void
    {
        foreach ($_note_keys_arr as $value) {
            if ($_group_name !== null) {
                unset ($this->getConversation()->notes[$this->getConversationGroupsNoteName()][$_group_name][$value]);
            } else {
                unset ($this->getConversation()->notes[$value]);
            }
        }
        $this->getConversation()->update();

        $this->getTelegrambotUtilsLogger()->info('Conversation notes have been deleted',
            ['group_name' => $_group_name, 'notes' => $_note_keys_arr]);
    }

    /**
     * Unset all permanent variables for the given conversation group.
     *
     * @param string $_group_name The name of conversation's group.
     */
    protected function deleteConversationGroup(string $_group_name): void
    {
        unset ($this->getConversation()->notes[$this->getConversationGroupsNoteName()][$_group_name]);
        $this->getTelegrambotUtilsLogger()->info('Conversation notes group has been deleted',
            ['group_name' => $_group_name]);
    }

    /**
     * Get conversation note's value.
     *
     * @param string      $_note       The key of the conversation->notes array.
     * @param string|null $_group_name The name of conversation's group.
     *
     * @return mixed|null The value of a variable or null if it does not exists.
     */
    protected function getNote(string $_note, ?string $_group_name = null)
    {
        if ($_group_name !== null) {
            $value = ($this->getConversation()->notes[$this->getConversationGroupsNoteName()][$_group_name][$_note] ??
                null);
        } else {
            $value = ($this->getConversation()->notes[$_note] ?? null);
        }

        $this->getTelegrambotUtilsLogger()->debug('Conversation note returned',
            ['group_name' => $_group_name, $_note => $value]);
        return $value;
    }

    /**
     * Get all conversation's notes.
     *
     * @param string|null $_group_name The name of conversation's group.
     *
     * @return array
     */
    protected function getNotes(?string $_group_name = null): array
    {
        if ($_group_name !== null) {
            $values = $this->getConversation()->notes[$this->getConversationGroupsNoteName()][$_group_name] ?? [];
        } else {
            $values = $this->getConversation()->notes ?? [];
        }

        $this->getTelegrambotUtilsLogger()->debug('All conversation notes returned',
            ['group_name' => $_group_name, 'notes' => $values]);
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

        $this->getTelegrambotUtilsLogger()->info('Conversation has been started', $conversationParams);
    }

    /**
     * @throws TelegramException
     */
    protected function stopConversation(): void
    {
        if ($this->getConversation() !== null) {
            $this->getConversation()->stop();
        }
        $this->getTelegrambotUtilsLogger()->info('Conversation has been stopped');
    }
}
