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
