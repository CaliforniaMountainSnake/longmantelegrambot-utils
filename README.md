# longmantelegrambot-utils
This is the set of util traits intended for the longman/telegram-bot library!

## Functionality:
- Easily send text messages in one line.


## Install:
### Require this package with Composer
Install this package through [Composer](https://getcomposer.org/).
Edit your project's `composer.json` file to require `californiamountainsnake/longmantelegrambot-utils`:
```json
{
    "name": "yourproject/yourproject",
    "type": "project",
    "require": {
        "php": "^7.2",
        "californiamountainsnake/longmantelegrambot-utils": "*"
    }
}
```
and run `composer update`

### or
run this command in your command line:
```bash
composer require californiamountainsnake/longmantelegrambot-utils
```

## Usage:
1. Create your custom bot command as usual.
2. Use needed traits and realise the abstract traits' methods:
```php
<?php
class TestCommand extends Command
{
    use TelegramUtils;
    use SendUtils;
    use ConversationUtils;


    /**
     * @var Conversation|null
     */
    protected $conversation;


    public function getTelegramMessage(): ?Message
    {
        return $this->getMessage();
    }

    protected function getStateNoteName(): string
    {
        return 'state';
    }

    protected function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    protected function setConversation(?Conversation $_new_conversation_state): void
    {
        $this->conversation = $_new_conversation_state;
    }

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        return $this->sendTextMessage('Test!');
    }
}

```  
