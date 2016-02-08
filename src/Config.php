<?php

namespace joernroeder\Pocomd;

abstract class Config {

	public static $defaults = array();

	private $store = array();

	public abstract function pathFor($key, $urlSegements);
	public abstract function render(Page $page);
	public abstract function notFound();

	/**
	 * Config getter.
	 * Returns the value stored under the given key. 
	 * In addition to values added via `set()` it also checks the class $defaults.
	 *
	 * @param  string $key
	 *
	 * @return any
	 */
	public function get($key) {
		if (isset(static::$defaults[$key])) {
			return static::$defaults[$key];
		}

		return isset($this->store[$key]) ? $this->store[$key] : null;
	}

	/**
	 * Adds a value by key to the config store
	 *
	 * @param string $key
	 * @param any $value
	 */
	public function set($key, $value) {
		$this->store[$key] = $value;
	}

	/**
	 * Returns the navigation object stored at `pocomd.navigation`.
	 * 
	 *
	 * @return [type] [description]
	 */
	public function getNavigation() {
		return array(
			'Navigation' => $this->get('pocomd.navigation')
		);
	}

	protected function setNavigation(array $navigation) {
		$this->set('pocomd.navigation', $navigation);
	}

	public function initNavigation($currentUrl) {
		$nav = new NavigationLoader($this);
		$nav->loadNavigation();
		$links = $nav->getLinks($currentUrl);

		// store navigation loader instance
		$this->set('navigation.loader', $nav);

		// save links for template
		$this->setNavigation($links);
	}

	public function getFolderName($name) {
		// todo: construct navigation loader inside config
		$folderName = $this->get('navigation.loader')->getFolderName($name);
	
		// couldn't resolve folder path for the given name.
		if (!$folderName) {
			return $this->notFound();
		}

		return $folderName;
	}

	public function renderPage($name) {
		$folderName = $this->getFolderName($name);

		$pageLoader = new PageLoader($this, $name, $folderName);
		$page = $pageLoader->load();

		// couldn't load page -> returning 404
		if (!$page) {
			return $this->notFound();
		}

		return $this->render($page);
	}
	
	public function getTemplateData() {
		return array_merge(
			$this->getNavigation(),
			$this->get('template.data')
		);
	}

	// add 
	public function updateNavigationItem(&$navItem, $page) {
	}

}