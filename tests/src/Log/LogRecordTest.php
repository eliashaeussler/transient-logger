<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/transient-logger".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\TransientLogger\Tests\Log;

use EliasHaeussler\TransientLogger as Src;
use PHPUnit\Framework;

/**
 * LogRecordTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Log\LogRecord::class)]
final class LogRecordTest extends Framework\TestCase
{
    private Src\Log\LogRecord $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Log\LogRecord(
            Src\Log\LogLevel::Alert,
            'Houston, we have a problem!',
            ['error' => 'rocket down'],
        );
    }

    #[Framework\Attributes\Test]
    public function subjectIsStringable(): void
    {
        self::assertSame('Houston, we have a problem!', (string) $this->subject);
    }
}
