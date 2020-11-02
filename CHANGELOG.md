# Change Log

## [3.0.0] - TBD
### Changed
- Sentry 3.0

## [2.0.1] - 2020-05-14
### Changed
- Removed usage of deprecated methods


## [2.0.0] - 2020-01-23
### Changed
- Migrated to laminas


## [1.0.1] - 2020-01-23
### Added
- Added compatibility with php 7.4

### Changed
- Bumped minimum sentry version to 2.3.1

### Fixed
- Fixed compatibility with sentry 2.3


## [1.0.0] - 2019-08-31

**This version is not compatible with previous versions.**

### Changed
- New release for Sentry 2.0


## [0.7.1] - 2017-11-09
### Added
- Allowed not injecting js file when config value is empty.


## [0.7.0] - 2017-07-07

**This version is not compatible with previous versions.**

### Added
- Added type hints

### Changed
- Minimum required PHP version is now PHP 7.0
- Minimum `sentry/sentry` is now 1.7
- Removed support to hhvm
- Removed fluent interface
- Updated default raven javascript resource url (3.16.0)
- ZF Logger: backtrace in Zend Logger is now automatically cleaned by namespaces anymore
- ZF Logger: extra params are now passed as variables
- Changed configuration

### Fixed
- Logs with `exception` in `extra` where the message is different from the exception message
does not create a `ContextException` anymore

### Removed
- Removed `excluded_backtrace_namespaces` options from Sentry Zend Logger


## [0.6.1] - 2016-10-05
### Added
- Added `raven_javascript_options`


## [0.6.0] - 2016-10-05
### Added
- Log Writer: Added Monolog namespace to default excluded function calls on backtrace
- Log Writer: Added the possibility to add other namespaces to excluded function calls on backtrace
- Added the possibility to add multiple `send_callback`s and retrieving it from container
- Added `CallbackInterface`
- Added the possibility to specify the `transport` via service name, retrieving it from container  
- Added `TransportInterface`
### Changed
- Changed default logger name to `SentryModule`
- Required minimum version 1.4 of sentry library


## [0.5.0] - 2016-09-29
### Added
- Log Writer: possibility to specify `exception` extra parameter to handle exceptions

### Changed
- Log Writer: retrieve backtrace root excluding logs function calls


## [0.4.3] - 2016-09-07
### Fixed
- Fixed error when `extra` key is not an array 


## [0.4.2] - 2016-09-01
### Added
- Allowed sentry 1.0 dependency


## [0.4.1] - 2016-07-05
### Added
- Added set method to set Raven error handler


## [0.4.0] - 2016-07-02
### Added
- Added zend-mvc 3 compatibility
- Added a writer for Zend Log

### Removed
- Removed psr/log-implementation from composer.json
- Removed compatibility with php < 5.6


## [0.3.4] - 2016-05-30
### Fixed
- Fixed invalid configuration key in module config


## [0.3.3] - 2016-05-30
### Added
- Added method to set which exceptions should not be catched in `ErrorHandlerListener`


## [0.3.2] - 2016-05-29
### Added
- Raven javascript integration

### Fixed
- Fix #2 for a warning in raven serializer: added custom default processor to serialize objects and resources


## [0.3.1] - 2016-05-28
### Fixed
- Fixed error creating instance of ErrorHandlerListener


## [0.3.0] - 2016-05-28
### Changed
- Completely redesigned


## [0.2.0]
### Changed
- Upgrade raven/raven to ^0.13.0


## [0.1.0]
### Changed
- Raven Client Abstract Service Factory
