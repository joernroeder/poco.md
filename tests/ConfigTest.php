<?php
 
use joernroeder\Pocomd\Config;
 
class ConfigTest extends PHPUnit_Framework_TestCase {

	private $config = null;

	public function setUp() {
		$this->config = $this->getMockForAbstractClass('joernroeder\Pocomd\Config');
	}

	public function tearDown() {
		$this->config = null;
	}

	public function testShouldBeAnInstanceOfConfig() {
		$this->assertInstanceOf('joernroeder\Pocomd\Config', $this->config);
	}

	public function testShouldCorrectlyReturnAKeyFromTheStore() {

		Config::$defaults = array(
			'foo' => 'bar'
		);		
		
		$this->assertEquals('bar', $this->config->get('foo'));
	}

	public function testShouldCorrectlyReturnAnUndefindedKeyFromTheStore() {
		$this->assertNull($this->config->get('key'));
	}

	public function testShouldCorrectlyAddAValueToTheStore() {
		$this->config->set('bar', 'foo');
		$this->assertEquals('foo', $this->config->get('bar'));
	}

	public function testGetNavigationReturnsTheLinksAsAnArray() {
		$links = array(
			'Title' => 'My Title',
			'Url' => 'http://foo.bar'
		);

		Config::$defaults = array(
			'pocomd.navigation' => array(
				$links
			)
		);

		$nav = $this->config->getNavigation();

		$this->assertEquals(array(
			'Navigation' => array(
				$links
			)
		), $nav);
	}

	// todo: test initNavigation
	public function testInitNavigation() {
	}

	/*public function testShouldCorrectlyInitTheNavigation() {
		$this->config->setNavigation(array('foo', 'bar'));

		$this->assertEquals(array(
			'Navigation' => array('foo', 'bar')
		), $this->config->getNavigation());
	}*/

	public function testGetTemplateDataShouldCorrectlyMergeTheNavigation() {
		Config::$defaults = array(
			'pocomd.navigation' => array(
				'Title' => 'My Title',
				'Url' => 'http://foo.bar'
			),
			'template.data' => array(
				'Foo' => 'bar'
			)
		);

		$this->assertEquals(array(
			'Navigation' => array(
				'Title' => 'My Title',
				'Url' => 'http://foo.bar'
			),
			'Foo' => 'bar'
		), $this->config->getTemplateData());
	}
 
}
