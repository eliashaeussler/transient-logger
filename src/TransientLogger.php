<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/transient-logger".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\TransientLogger;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psr\Log\AbstractLogger;
use Stringable;

use function array_filter;
use function array_values;
use function count;
use function is_string;

/**
 * TransientLogger.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 *
 * @implements IteratorAggregate<Log\LogRecord>
 */
final class TransientLogger extends AbstractLogger implements Countable, IteratorAggregate
{
    /**
     * @var list<Log\LogRecord>
     */
    private array $log = [];

    /**
     * @param array<string, mixed> $context
     *
     * @throws Exception\LogLevelIsInvalid
     * @throws Exception\LogLevelIsUnsupported
     * @throws Exception\LogMessageIsUnsupported
     */
    public function log($level, $message, array $context = []): void
    {
        $logLevel = $this->resolveLevel($level);

        // Validate log message
        if (!is_string($message) && !($message instanceof Stringable)) {
            throw new Exception\LogMessageIsUnsupported($message);
        }

        $this->log[] = new Log\LogRecord($logLevel, $message, $context);
    }

    /**
     * @return list<Log\LogRecord>
     */
    public function getAll(): array
    {
        return $this->log;
    }

    /**
     * @return list<Log\LogRecord>
     */
    public function getByLogLevel(Log\LogLevel $level): array
    {
        return array_values(
            array_filter(
                $this->log,
                static fn (Log\LogRecord $log) => $log->level === $level,
            ),
        );
    }

    public function flushLog(): void
    {
        $this->log = [];
    }

    public function count(): int
    {
        return count($this->log);
    }

    /**
     * @return ArrayIterator<int, Log\LogRecord>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->log);
    }

    /**
     * @throws Exception\LogLevelIsInvalid
     * @throws Exception\LogLevelIsUnsupported
     */
    private function resolveLevel(mixed $level): Log\LogLevel
    {
        if ($level instanceof Log\LogLevel) {
            return $level;
        }

        if (!is_string($level)) {
            throw new Exception\LogLevelIsUnsupported($level);
        }

        return Log\LogLevel::tryFrom($level) ?? throw new Exception\LogLevelIsInvalid($level);
    }
}
