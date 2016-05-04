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

	public function createNavigationLoader($config) {
		return new NavigationLoader($config);
	}

	public function createPageLoader($config, $name, $folderName = null) {
		return new PageLoader($config, $name, $folderName);
	}

	public function createPageParser($folder) {
		return new PageParser($folder);
	}

	public function createPageObject($config, $data = null, $meta = null, $template = null) {
		return new Page($config, $data, $meta);
	}

	public function initNavigation($currentUrl) {
		$nav = $this->createNavigationLoader($this);
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

		$pageLoader = $this->createPageLoader($this, $name, $folderName);
		$page = $pageLoader->load();

		// couldn't load page -> returning 404
		if (!$page) {
			return $this->notFound();
		}

		return $this->render($page);
	}

	public function getTemplateData() {
		$data = array_merge(
			$this->getNavigation(),
			$this->get('template.data')
		);

		$this->updateTemplateData($data);

		return $data;
	}

	// add
	public function updateNavigationItem(&$navItem, $page) {
	}

	// provides the ability to update the given template data.
	public function updateTemplateData(&$templateData) {

	}
}
