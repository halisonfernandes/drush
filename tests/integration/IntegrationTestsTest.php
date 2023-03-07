<?php

namespace Unish;

use Drush\Commands\core\CoreCommands;

/**
 * @coversDefaultClass \Unish\UnishIntegrationTestCase
 * @group tests
 */
class IntegrationTestsTest extends UnishIntegrationTestCase
{
    /**
     * @covers ::drush
     */
    public function testStdErr(): void
    {
        if ($this->isWindows()) {
            $this->markTestSkipped('@todo Fails on Windows');
        }

        // Ensure that a verbose run does not affect subsequent runs.
        $this->drush(CoreCommands::VERSION, [], ['debug' => null]);
        $this->assertStringContainsString('[info] Starting bootstrap to none', $this->getErrorOutputRaw());
        $this->drush(CoreCommands::VERSION);
        $this->assertStringNotContainsString('[info] Starting bootstrap to none', $this->getErrorOutputRaw());
    }
}
