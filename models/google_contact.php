<?php

/**
 * Plugin model for "Google Contact".
 *
 * Provides custom find types for the various calls on the web service, mapping
 * familiar CakePHP methods and parameters to the http request params for
 * issuing to the web service.
 * 
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://fahad19.com
 * @copyright (c) 2011 Fahad Ibnay Heylaal
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
class GoogleContact extends GdataAppModel {

	/**
	 * The name of this model
	 *
	 * @var name
	 */
	public $name = 'GoogleContact';

	/**
	 * The datasource this model uses
	 *
	 * @var name
	 */
	public $useDbConfig = 'googleContacts';

	/**
	 * The custom find types
	 * 
	 * @var array
	 */
	public $_findMethods = array(
		'contacts' => true,
	);

	protected function _findContacts($state, $query = array(), $results = array()) {
		if ($state == 'before') {
			$this->request['auth'] = true;
			$this->request['uri']['path'] = 'm8/feeds/contacts/default/full';
			$results = $this->_paginationParams($query);
		}
		
		return $results;
	}
	
	public function getList() {
		$results = $this->find('contacts');
		//debug($results);
		
		$list = array();
		if (!empty($results['feed']['entry'])) {
			foreach ($results['feed']['entry'] AS $i => $entry) {
				// email
				if (isset($entry['email']['0']) && is_array($entry['email']['0'])) {
					$email = $entry['email']['0']['address'];
				} else {
					$email = $entry['email']['address'];
				}
				
				// title
				if (empty($entry['title'])) {
					$title = '';
				} elseif (!empty($entry['title']) && is_array($entry['title'])) {
					$title = $entry['title']['0'];
				} else {
					$title = $entry['title'];
				}
				
				$list[$email] = $title;
			}
		}
		return $list;
	}

}

?>