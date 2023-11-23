<div align="center">

# Transient PSR-3 logger

[![Coverage](https://img.shields.io/codecov/c/github/eliashaeussler/transient-logger?logo=codecov&token=faro4tAGWd)](https://codecov.io/gh/eliashaeussler/transient-logger)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/transient-logger?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/transient-logger/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/transient-logger/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/transient-logger/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/transient-logger/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/transient-logger/actions/workflows/tests.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/transient-logger/php?logo=php)](https://packagist.org/packages/eliashaeussler/transient-logger)

</div>

This library provides a small PSR-3 compliant logger to stores log
records in memory. Each log is converted to a log record and attached
to the current logger instance. Logs will be available as long as
the logger object is available in memory. This is especially useful
for testing applications and libraries.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/transient-logger?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/transient-logger)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/transient-logger?color=brightgreen)](https://packagist.org/packages/eliashaeussler/transient-logger)

```bash
composer require --dev eliashaeussler/transient-logger
```

## 💥 Compatibility with `psr/log`

Make sure to require the correct version of this library, depending
on which version of `psr/log` you're using:

| `psr/log` version | `eliashaeussler/transient-logger` version |
|-------------------|-------------------------------------------|
| `^1.0`            | `^1.0`                                    |
| `^2.0 \|\| ^3.0`  | `^2.0`                                    |

## ⚡ Usage

### Create logger

The library provides a [`TransientLogger`](src/TransientLogger.php) class
which implements PSR's [`LoggerInterface`](https://github.com/php-fig/log/blob/master/src/LoggerInterface.php).
You can use it just like any other PSR-3 compliant logger:

```php
use EliasHaeussler\TransientLogger;

// Create a new logger
$logger = new TransientLogger\TransientLogger();

// Log messages
$logger->alert('Houston, we have a problem!', ['error' => 'rocket down']);
```

### Log messages

For each logged message, a new [`Log\LogRecord`](src/Log/LogRecord.php) is
created and attached to the logger instance. The appropriate log levels are
represented by a [`Log\LogLevel`](src/Log/LogLevel.php) enum which is a wrapper
around PSR's [`LogLevel`](https://github.com/php-fig/log/blob/master/src/LogLevel.php) constants.

### Access log records

You can access all log records in several ways:

```php
// Get all log records
$logs = $logger->getAll();

// Get by specific log level
$errors = $logger->getByLogLevel(TransientLogger\Log\LogLevel::Error);

// Iterate over log records
foreach ($logger as $logRecord) {
    $level = $logRecord->level; // instanceof \EliasHaeussler\TransientLogger\Log\LogLevel
    $message = $logRecord->message; // string or instanceof Stringable
    $context = $logRecord->context; // array<string, mixed>
}
```

### Flush log

If required, you can always flush the log attached to a logger:

```php
$logger->flushLog();
```

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
