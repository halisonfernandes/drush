<?php

namespace Drush\SiteAlias;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\Util\YamlDataFileLoader;
use Consolidation\SiteAlias\SiteAlias;

class SiteAliasFileLoaderTest extends TestCase
{
    use \Unish\Utils\Fixtures;
    use \Unish\Utils\FunctionUtils;

    protected $sut;

    public function setup(): void
    {
        $this->sut = new SiteAliasFileLoader();

        $ymlLoader = new YamlDataFileLoader();
        $this->sut->addLoader('yml', $ymlLoader);
    }

    public function testLoadSingleAliasFile()
    {
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/single';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->assertTrue(is_file($siteAliasFixtures . '/simple.site.yml'));
        $this->assertTrue(is_file($siteAliasFixtures . '/single.site.yml'));

        $this->sut->addSearchLocation($siteAliasFixtures);

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $result = $this->callProtected($this->sut, 'loadSingleAliasFile', [$name]);
        $this->assertEquals(SiteAlias::class, get_class($result));
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $result = $this->callProtected($this->sut, 'loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = new SiteAliasName('single', 'alternate');
        $result = $this->callProtected($this->sut, 'loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->callProtected($this->sut, 'loadSingleAliasFile', [$name]);
        $this->assertFalse($result);
    }

    public function testLoad()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/group');

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = new SiteAliasName('single', 'alternate');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);
    }

    public function testLoadAll()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');

        $all = $this->sut->loadAll();
        $this->assertEquals('@single.single.alternate,@single.single.dev', implode(',', array_keys($all)));
    }
}
