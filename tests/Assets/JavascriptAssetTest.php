<?php

class JavascriptAssetTest extends PHPUnit_Framework_TestCase
{
  public function testMinify()
  {
    $original = 'function myFunction(){
      alert("Hello\nHow are you?");
    }';

    $nominify = '@' . 'do-not-minify
    function myFunction(){
      alert("Hello\nHow are you?");
    }';

    $asset = new \Packaged\Dispatch\Assets\JavascriptAsset();

    $asset->setContent($original);
    $this->assertEquals(
    'function myFunction(){alert("Hello\nHow are you?");}',
    $asset->getContent()
    );

    $asset->setContent($nominify);
    $this->assertEquals($nominify, $asset->getContent());

    $asset->setContent($original);
    $asset->setOptions(['minify' => 'false']);
    $this->assertEquals($original, $asset->getContent());
  }

  public function testAsset()
  {
    $asset = new \Packaged\Dispatch\Assets\JavascriptAsset();
    $this->assertEquals('js', $asset->getExtension());
    $this->assertEquals('text/javascript', $asset->getContentType());
  }

  /**
   * @ref  Issue 2
   * @link https://github.com/packaged/dispatch/issues/2
   */
  public function testSingleLineCommands()
  {
    $raw = '$(document).ready(function(){

    $(window).scroll(function () {

        var max_scroll = 273; //height to scroll before hitting nav-bar

        var navbar = $(".main-nav");
    });
});';

    $asset = new \Packaged\Dispatch\Assets\JavascriptAsset();
    $asset->setContent($raw);
    $this->assertEquals(
    '$(document).ready(function(){$(window).scroll(function()'
    . '{var max_scroll=273;var navbar=$(".main-nav");});});',
    $asset->getContent()
    );
  }

  public function testMinifyException()
  {
    $raw   = 'var string = "This string is hanging out.

    alert($x)';
    $asset = new \Packaged\Dispatch\Assets\JavascriptAsset();
    $asset->setContent($raw);
    $asset->setOptions(['minify' => 'true']);
    $this->assertEquals($raw, $asset->getContent());
  }
}
