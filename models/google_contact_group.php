<?php

/**
 * Plugin model for "Google Contact Group".
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
class GoogleContactGroup extends GdataAppModel {

	/**
	 * The name of this model
	 *
	 * @var name
	 */
	public $name = 'GoogleContactGroup';

	/**
	 * The datasource this model uses
	 *
	 * @var name
	 */
	public $useDbConfig = 'googleContacts';
	
	/**
	 * The fields and their types for the form helper and ensuring Model::save 
	 * acknowledges the data.
	 *
	 * @var array
	 */
	public $_schema = array(
		'id' => array('type' => 'string', 'length' => '255'),
		'entry' => array('type' => 'text'),
	);

	/**
	 * The custom find types
	 * 
	 * @var array
	 */
	public $_findMethods = array(
		'groups' => true,
		'group' => true,
	);

	protected function _findGroup($state, $query = array(), $results = array()) {
		if ($state == 'before' && isset($query['conditions']['contactId'])) {
			$this->request['auth'] = true;
			$this->request['uri']['path'] = 'm8/feeds/groups/' . $query['conditions']['contactId'] . '/full';
			//$results = $this->_paginationParams($query);
			$results['page'] = 1;
			$results['order'] = '';
			$results['callbacks'] = array();
		}

		return $results;
	}

	protected function _findGroups($state, $query = array(), $results = array()) {
		if ($state == 'before' && isset($query['conditions']['contactId'])) {
			$this->request['auth'] = true;
			$this->request['uri']['path'] = 'm8/feeds/groups/' . $query['conditions']['contactId'] . '/full';
			$results['page'] = 1;
			$results['order'] = '';
			$results['callbacks'] = array();
		}

		return $results;
	}

	public function save($data = null, $validate = true, $fieldList = array()) {
		$result = true;
		return $result;
	}
	
	/**
	 * Overrides cake's Model::exists() to prevent a find('count') being triggered and
	 * instead just checks whether $this->id or $this->data[alias][id] is set.
	 * 
	 * This is to help cake correctly choose between create() and update() methods on the datasource
	 * when saving.
	 * 
	 * TODO: perhaps implement a proper call to google to check if a record with
	 * the found ID actually exists, to match Model::exists() more closely?
	 * 
	 * @return boolean
	 */
	
	function exists() {
		return !empty($this->id) || !empty($this->data[$this->alias][$this->primaryKey]);
	}

}

?>