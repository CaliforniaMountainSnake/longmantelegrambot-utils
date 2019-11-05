<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils;

use CaliforniaMountainSnake\LongmanTelegrambotUtils\Enums\TelegramChatTypeEnum;
use CaliforniaMountainSnake\LongmanTelegrambotUtils\Logger\TelegrambotUtilsLogger;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;

/**
 * This trait let you to get main Telegram params both in the context of the regular bot command
 * and in the context of the CallbackqueryCommand.
 */
trait TelegramUtils
{
    use TelegrambotUtilsLogger;
    
    /**
     * Current message.
     *
     * @var Message|null
     */
    protected $message;

    /**
     * The person from which has been received current message.
     *
     * @var User
     */
    protected $telegramUser;

    /**
     * @var Chat
     */
    protected $chat;

    /**
     * Current chat id.
     *
     * @var string
     */
    protected $chatId;

    /**
     * Current text. (without cmd).
     *
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
        $this->message = $this->getTelegramMessage();
        $callback_query = $this->getUpdate()->getCallbackQuery();
        if ($this->message !== null) {
            $this->telegramUser = $this->message->getFrom();
            $this->chat = $this->message->getChat();
            $this->text = $this->message->getText(true) ?? '';
        } elseif ($callback_query) {
            $this->telegramUser = $callback_query->getFrom();
            $this->chat = $callback_query->getMessage()->getChat();
            $this->text = $callback_query->getData() ?? '';
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
     * @return string
     */
    protected function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @return TelegramChatTypeEnum
     */
    protected function getChatType(): TelegramChatTypeEnum
    {
        if ($this->chat->isPrivateChat()) {
            return TelegramChatTypeEnum::PRIVATE_CHAT();
        }
        if ($this->chat->isGroupChat()) {
            return TelegramChatTypeEnum::GROUP_CHAT();
        }
        if ($this->chat->isSuperGroup()) {
            return TelegramChatTypeEnum::SUPERGROUP_CHAT();
        }
        if ($this->chat->isChannel()) {
            return TelegramChatTypeEnum::CHANNEL();
        }

        throw new \RuntimeException('Unknown telegram chat type!');
    }
}
