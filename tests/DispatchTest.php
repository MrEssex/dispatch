<?php

namespace Packaged\Dispatch\Tests;

use Packaged\Dispatch\Dispatch;
use Packaged\Dispatch\Manager\ResourceManager;
use Packaged\Dispatch\ResourceStore;
use Packaged\Helpers\Path;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;

class DispatchTest extends TestCase
{
  public function testInstance()
  {
    $dispatch = new Dispatch(__DIR__);
    $this->assertNull(Dispatch::instance());
    Dispatch::bind($dispatch);
    $this->assertSame($dispatch, Dispatch::instance());
    Dispatch::destroy();
    $this->assertNull(Dispatch::instance());
    $this->assertEquals(Path::system(__DIR__, Dispatch::RESOURCES_DIR), $dispatch->getResourcesPath());
    $this->assertEquals(Path::system(__DIR__, Dispatch::PUBLIC_DIR), $dispatch->getPublicPath());
    $this->assertEquals(Path::system(__DIR__, Dispatch::VENDOR_DIR, 'a', 'b'), $dispatch->getVendorPath('a', 'b'));
  }

  public function testAlias()
  {
    $dispatch = new Dispatch(__DIR__);
    $this->assertNull($dispatch->getAliasPath('abc'));
    $dispatch->addAlias('abc', 'a/b/c');
    $this->assertEquals('a/b/c', $dispatch->getAliasPath('abc'));
  }

  public function testHandle()
  {
    $dispatch = new Dispatch(dirname(__DIR__));
    Dispatch::bind($dispatch);
    $request = Request::create('/r/randomhash/css/test.css');
    $response = $dispatch->handle($request);
    $this->assertContains('url(\'r/d41d8cd9/img/x.jpg\')', $response->getContent());
    Dispatch::destroy();
  }

  public function testBaseUri()
  {
    $dispatch = new Dispatch(dirname(__DIR__), 'http://assets.packaged.in');
    Dispatch::bind($dispatch);
    $request = Request::create('/r/randomhash/css/test.css');
    $response = $dispatch->handle($request);
    $this->assertContains('url(\'http://assets.packaged.in/r/d41d8cd9/img/x.jpg\')', $response->getContent());
    Dispatch::destroy();
  }

  public function testStore()
  {
    Dispatch::bind(new Dispatch(dirname(__DIR__), 'http://assets.packaged.in'));
    ResourceManager::resources()->requireCss('css/test.css');
    ResourceManager::resources()->requireCss('css/do-not-modify.css');
    $response = Dispatch::instance()->store()->generateHtmlIncludes(ResourceStore::TYPE_CSS);
    $this->assertContains('href="http://assets.packaged.in/r/e69b7a20/css/test.css"', $response);
    ResourceManager::resources()->requireJs('js/alert.js');
    $response = Dispatch::instance()->store()->generateHtmlIncludes(ResourceStore::TYPE_JS);
    $this->assertContains('src="http://assets.packaged.in/r/ef6402a7/js/alert.js"', $response);
    Dispatch::destroy();
  }
}
