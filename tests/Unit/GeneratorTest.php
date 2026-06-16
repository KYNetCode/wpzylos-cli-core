<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\Core\Generator;
use WPZylos\Framework\Cli\Core\StubCompiler;
use WPZylos\Framework\Cli\Core\FileWriter;

/**
 * Tests for Generator abstract base class.
 */
class GeneratorTest extends TestCase
{
    /**
     * @return Generator
     */
    private function createConcreteGenerator(): Generator
    {
        $compiler = new StubCompiler(dirname(__DIR__, 2) . '/stubs');
        $writer = new FileWriter();

        return new class ($compiler, $writer, '/tmp/test-plugin') extends Generator {
            protected function getStubName(): string
            {
                return 'test';
            }

            protected function getOutputPath(string $name): string
            {
                return $this->basePath . '/app/' . $name . '.php';
            }

            /**
             * Expose protected methods for testing.
             */
            public function testToClassName(string $name): string
            {
                return $this->toClassName($name);
            }

            public function testToVariableName(string $name): string
            {
                return $this->toVariableName($name);
            }

            public function generate(string $name, array $options = []): array
            {
                return [];
            }
        };
    }

    public function testToClassNameConvertsDashesToPascalCase(): void
    {
        $generator = $this->createConcreteGenerator();
        $this->assertSame('MyThing', $generator->testToClassName('my-thing'));
    }

    public function testToClassNameConvertsUnderscoresToPascalCase(): void
    {
        $generator = $this->createConcreteGenerator();
        $this->assertSame('MyThing', $generator->testToClassName('my_thing'));
    }

    public function testToClassNameHandlesAlreadyPascalCase(): void
    {
        $generator = $this->createConcreteGenerator();
        $this->assertSame('MyThing', $generator->testToClassName('MyThing'));
    }

    public function testToVariableNameReturnsCamelCase(): void
    {
        $generator = $this->createConcreteGenerator();
        $this->assertSame('myThing', $generator->testToVariableName('my-thing'));
    }

    public function testToVariableNameLowercasesFirst(): void
    {
        $generator = $this->createConcreteGenerator();
        $this->assertSame('myThing', $generator->testToVariableName('MyThing'));
    }
}
