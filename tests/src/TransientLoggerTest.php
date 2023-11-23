<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/transient-logger".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\TransientLogger\Tests;

use EliasHaeussler\TransientLogger as Src;
use PHPUnit\Framework;
use Psr\Log;

use function iterator_to_array;

/**
 * TransientLoggerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\TransientLogger::class)]
final class TransientLoggerTest extends Framework\TestCase
{
    private Src\TransientLogger $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\TransientLogger();
    }

    #[Framework\Attributes\Test]
    public function logThrowsExceptionIfLogLevelIsUnsupported(): void
    {
        $this->expectExceptionObject(new Src\Exception\LogLevelIsUnsupported(null));

        $this->subject->log(null, 'baz');
    }

    #[Framework\Attributes\Test]
    public function logThrowsExceptionIfLogLevelIsInvalid(): void
    {
        $this->expectExceptionObject(new Src\Exception\LogLevelIsInvalid('foo'));

        $this->subject->log('foo', 'baz');
    }

    #[Framework\Attributes\Test]
    public function logThrowsExceptionIfLogMessageIsUnsupported(): void
    {
        $this->expectExceptionObject(new Src\Exception\LogMessageIsUnsupported(null));

        $this->subject->log(Log\LogLevel::ALERT, null);
    }

    #[Framework\Attributes\Test]
    public function logProperlyHandlesLogLevelEnums(): void
    {
        $expected = new Src\Log\LogRecord(
            Src\Log\LogLevel::Alert,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        $this->subject->log(
            Src\Log\LogLevel::Alert,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        self::assertCount(1, $this->subject);
        self::assertEquals([$expected], $this->subject->getAll());
    }

    #[Framework\Attributes\Test]
    public function logAddsLogRecordToMemory(): void
    {
        $expected = new Src\Log\LogRecord(
            Src\Log\LogLevel::Alert,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        $this->subject->log(
            Log\LogLevel::ALERT,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        self::assertCount(1, $this->subject);
        self::assertEquals([$expected], $this->subject->getAll());
    }

    #[Framework\Attributes\Test]
    public function getByLogLevelReturnsLogsOfGivenLogLevel(): void
    {
        $this->subject->log(
            Log\LogLevel::ALERT,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        $this->subject->log(
            Log\LogLevel::CRITICAL,
            'Houston, we have another problem!',
            ['error' => 'we\'re hungry'],
        );

        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Emergency));
        self::assertCount(1, $this->subject->getByLogLevel(Src\Log\LogLevel::Alert));
        self::assertCount(1, $this->subject->getByLogLevel(Src\Log\LogLevel::Critical));
        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Error));
        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Warning));
        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Notice));
        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Info));
        self::assertCount(0, $this->subject->getByLogLevel(Src\Log\LogLevel::Debug));
    }

    #[Framework\Attributes\Test]
    public function flushLogClearsAllLogRecords(): void
    {
        $this->subject->log(
            Log\LogLevel::ALERT,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        self::assertCount(1, $this->subject);

        $this->subject->flushLog();

        self::assertCount(0, $this->subject);
    }

    #[Framework\Attributes\Test]
    public function subjectIsCountable(): void
    {
        $this->subject->log(
            Log\LogLevel::ALERT,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        $this->subject->log(
            Log\LogLevel::CRITICAL,
            'Houston, we have another problem!',
            ['error' => 'we\'re hungry'],
        );

        self::assertCount(2, $this->subject);
    }

    #[Framework\Attributes\Test]
    public function subjectIsIterable(): void
    {
        $expected = [
            new Src\Log\LogRecord(
                Src\Log\LogLevel::Alert,
                'Houston, we have a problem!',
                ['error' => 'rocket down'],
            ),
            new Src\Log\LogRecord(
                Src\Log\LogLevel::Critical,
                'Houston, we have another problem!',
                ['error' => 'we\'re hungry'],
            ),
        ];

        $this->subject->log(
            Log\LogLevel::ALERT,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );

        $this->subject->log(
            Log\LogLevel::CRITICAL,
            'Houston, we have another problem!',
            ['error' => 'we\'re hungry'],
        );

        self::assertEquals($expected, iterator_to_array($this->subject));
    }
}
