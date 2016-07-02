# Change Log

## [0.4.1] - TBD
### Added
- Nothing

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