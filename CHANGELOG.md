# Changelog
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security


## [1.1.24] - 2020-01-31
### Added
- Added the AdvancedConversationUtils trait.

## [1.1.23] - 2019-11-10
### Added
- Added the support of conversation's groups. Now you can set notes for the specified group and delete the group (with all variables stored in it) later. This functionality can be helpful in the situations where you need to set same notes multiple times in the same conversation.
### Changed
- Composer dependencies have been updated.

## [1.1.22] - 2019-11-05
### Fixed
- Fixed a bug with AdvancedSendUtils::forceShowAnyMessage().

## [1.1.21] - 2019-11-05
### Changed
- Logging have been improved.

## [1.1.20] - 2019-11-05
### Added
- Added the $_is_force_del_and_send flag in AdvancedSendUtils::showAnyMessage().
- Added the AdvancedSendUtils::forceShowAnyMessage() method.

## [1.1.19] - 2019-11-05
### Changed
- Logging have been improved.

## [1.1.18] - 2019-11-05
### Changed
- The logger has been moved to the separate trait (This allows to initialize the logger without initializing the abstract methods of traits).

## [1.1.17] - 2019-11-02
### Fixed
- Fixed a small bug in the ConversationUtils.

## [1.1.16] - 2019-11-02
### Changed
- Logging of ConversationUtils have been optimized.
- The translation of the comments have been improved.

## [1.1.15] - 2019-11-02
### Added
- Added the possibility to set a Psr\Log\LoggerInterface object for ConversationUtils.

## [1.1.14] - 2019-09-10
### Added
- Added the method AdvancedSendUtils::deletePrevMsgData().

## [1.1.13] - 2019-09-07
### Fixed
- Fix a bug with SendUtils::sendMediafile()'s reply_markup.

## [1.1.12] - 2019-09-07
### Added
- Added the Mediafile class represents the files that can be edited with editMessageMedia().
- Added the ConversationUtils::assertConversationIsStarted() method.
- Added the SendUtils::sendMediafile() method.
- Added the SendUtils::editMessageMedia() method.
- Added the AdvancedSendUtils::convertServerResponseToTelegramResponse() method.
- Added the AdvancedSendUtils::showAnyMessage() method that allows to show ANY types of messages, including the messages with media files.
### Changed
- SendUtils::sendVarExportDebugMessage() allows to set an object description now.
- SendUtils::getValidationErrorsString() is protected now.
- SendUtils::sendDocument() is deprecated now.
- AdvancedSendUtils::showTextMessage() is deprecated now.
- Updated the composer dependencies.

## [1.1.11] - 2019-08-28
### Added
- Added TelegramFileUtils trait allows easily download files from Telegram.
- Added TelegramMessageTypeEnum contains all types of telegram messages supported by longman/telegram-bot library.

## [1.1.10] - 2019-08-28
### Changed
- The library californiamountainsnake/php-simple-telegram-api has been updated to the version ~2.0.0.

## [1.1.9] - 2019-08-25
### Changed
- Some improvement of the AdvancedSendUtils::showTextMessage().

## [1.1.8] - 2019-08-25
### Fixed
- Fix the bug with namespace of AdvancedSendUtils.

## [1.1.7] - 2019-08-25
### Added
- Added the AdvancedSendUtils trait with showTextMessage() method allows send a message once and edit one later.
### Changed
- !!! SendUtils::editMessageText signature has been changed. It takes now the errors parameter as sendTextMessage().
- Changed formatting of all classes.

## [1.1.6] - 2019-08-07
### Added
- Added the ConversationUtils::getState() method.

## [1.1.5] - 2019-08-07
### Added
- Added the SendUtils::emptyResponse() method.

## [1.1.4] - 2019-08-07
### Added
- Added the possibility to specify the chat_id and parse_mode in all SendUtils's methods.
- Added the SendUtils::getDefaultParseMode() method.
- Added the ConversationUtils::getNotes() method.
### Changed
- !The return type hint of the TelegramUtils::getChatId has been changed from int to string.

## [1.1.3] - 2019-08-06
### Added
- Added the method TelegramUtils::getChatType().

## [1.1.2] - 2019-08-06
### Added
- Added the method TelegramUtils::getChat().

## [1.1.1] - 2019-07-29
### Fixed
- Fixed a bug.

## [1.1.0] - 2019-07-29
### Changed
- ConversationUtils::getName() method renamed to static method ConversationUtils::getCommandName() for the compatibility with other packages.

## [1.0.0] - 2019-07-22
### Added
- Added the useful traits to send messages, working with conversation and getting telegram parameters in any command.

[1.1.24]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.23...1.1.24
[1.1.23]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.22...1.1.23
[1.1.22]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.21...1.1.22
[1.1.21]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.20...1.1.21
[1.1.20]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.19...1.1.20
[1.1.19]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.18...1.1.19
[1.1.18]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.17...1.1.18
[1.1.17]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.16...1.1.17
[1.1.16]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.15...1.1.16
[1.1.15]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.14...1.1.15
[1.1.14]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.13...1.1.14
[1.1.13]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.12...1.1.13
[1.1.12]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.11...1.1.12
[1.1.11]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.10...1.1.11
[1.1.10]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.9...1.1.10
[1.1.9]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.8...1.1.9
[1.1.8]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.7...1.1.8
[1.1.7]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.6...1.1.7
[1.1.6]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/CaliforniaMountainSnake/longmantelegrambot-utils/compare/1.0.0...1.1.0
