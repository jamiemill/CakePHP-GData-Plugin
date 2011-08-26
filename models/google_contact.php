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
		'contact' => true,
	);

	protected function _findContact($state, $query = array(), $results = array()) {
		if ($state == 'before' && isset($query['conditions']['contactId'])) {
			$this->request['auth'] = true;
			$this->request['uri']['path'] = 'm8/feeds/contacts/default/full/' . $query['conditions']['contactId'];
			//$results = $this->_paginationParams($query);
			$results['page'] = 1;
			$results['order'] = '';
			$results['callbacks'] = array();
		}

		return $results;
	}

	protected function _findContacts($state, $query = array(), $results = array()) {
		if ($state == 'before') {
			$this->request['auth'] = true;
			$this->request['uri']['path'] = 'm8/feeds/contacts/default/full';
			if(!empty($query['conditions']['title'])) {
				$this->request['uri']['query']['title'] = $query['conditions']['title'];
			} elseif (!empty($query['conditions']['q'])) {
				$this->request['uri']['query']['q'] = $query['conditions']['q'];
			}
			$results = $this->_paginationParams($query);
		}
		
		return $results;
	}

	
}

?>