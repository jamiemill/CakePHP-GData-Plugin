<?php

/**
 * Plugin model for "Google Contact".
 *
 * Provides custom find types for the various calls on the web service, mapping
 * familiar CakePHP methods and parameters to the http request params for
 * issuing to the web service.
 * 
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
			$query = $this->_paginationParams($query);
			return $query;
		} else {
			return $results;
		}
	}

}

?>