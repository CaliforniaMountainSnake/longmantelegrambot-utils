<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;

/**
 * Трейт позволяет получить основные telegram-параметры, как в контексте обычной команды,
 * так и в контексте CallbackqueryCommand.
 */
trait TelegramUtils
{
    /**
     * Текущее сообщение.
     * @var Message|null
     */
    protected $message;

    /**
     * От кого получено текущее сообщение.
     * @var User
     */
    protected $telegramUser;

    /**
     * @var Chat
     */
    protected $chat;

    /**
     * Текущий id чата.
     * @var int
     */
    protected $chatId;

    /**
     * Текущий текст. (without cmd).
     * @var string
     */
    protected $text;

    /**
     * @return Message
     */
    abstract public function getTelegramMessage(): ?Message;

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @return Update
     */
    abstract public function getUpdate();

    protected function initTelegramParams(): void
    {
        $this->message  = $this->getTelegramMessage();
        $callback_query = $this->getUpdate()->getCallbackQuery();
        if ($this->message !== null) {
            $this->telegramUser = $this->message->getFrom();
            $this->chat         = $this->message->getChat();
            $this->text         = $this->message->getText(true) ?? '';
        } elseif ($callback_query) {
            $this->telegramUser = $callback_query->getFrom();
            $this->chat         = $callback_query->getMessage()->getChat();
            $this->text         = $callback_query->getData() ?? '';
        }
        $this->chatId = $this->chat->getId();
    }

    /**
     * @return User
     */
    protected function getTelegramUser(): User
    {
        return $this->telegramUser;
    }

    /**
     * @return Chat
     */
    public function getChat(): Chat
    {
        return $this->chat;
    }

    /**
     * @return int
     */
    protected function getChatId(): int
    {
        return $this->chatId;
    }
}
