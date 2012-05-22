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

	public function save($data = null, $validate = true, $fieldList = array()) {
		$contact = new DOMDocument('1.0', 'utf-8');
		$entry = $contact->createElementNS('http://www.w3.org/2005/Atom', 'atom:entry');
		$entry->setAttribute('gd:etag', $data['entry']['gd:etag']);

		$id = $contact->createElement('id', $data['entry']['id']);
		$entry->appendChild($id);

		$category = $contact->createElement('atom:category');
		$category->setAttribute('scheme' ,'http://schemas.google.com/g/2005#kind');
		$category->setAttribute('term' ,'http://schemas.google.com/docs/2007#contact');
		$entry->appendChild($category);

		if (isset($data['entry']['email'])) {
			$emails = $data['entry']['email'];
			if (!isset($emails['0'])) {
				$emails = array($emails);
			}
			
			// title
			$title = $contact->createElement('atom:title', $data['entry']['title']);
			$entry->appendChild($title);
			
			// name
			$name = $contact->createElement('gd:name');
			$fullName = $contact->createElement('gd:fullName', $data['entry']['title']);
			$name->appendChild($fullName);
			$entry->appendChild($name);

			// organization
			if (!empty($data['entry']['organization'])) {
				$organization = $contact->createElement('gd:organization');
				$organization->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
				if (isset($data['entry']['organization']['orgName'])) {
					$orgName = $contact->createElement('gd:orgName', $data['entry']['organization']['orgName']);
					$organization->appendChild($orgName);
				}
				if (isset($data['entry']['organization']['orgTitle'])) {
					$orgTitle = $contact->createElement('gd:orgTitle', $data['entry']['organization']['orgTitle']);
					$organization->appendChild($orgTitle);
				}
				$entry->appendChild($organization);
			}

			// emails
			foreach ($emails AS $email) {
				$element = $contact->createElementNS('http://schemas.google.com/g/2005', 'gd:email'); //$contact->createElement('gd:email');

				// address
				$element->setAttribute('address', $email['address']);

				// rel
				if (isset($email['rel'])) {
					$element->setAttribute('rel', $email['rel']);
				} else {
					$element->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
				}

				// primary
				if (!empty($email['primary'])) {
					$element->setAttribute('primary', true);
				}
				$entry->appendChild($element);
			}

			// phoneNumbers
			if (!empty($data['entry']['phoneNumber'])) {
				$phoneNumbers = $data['entry']['phoneNumber'];
				foreach ($phoneNumbers AS $phoneNumber) {
					$element = $contact->createElement('gd:phoneNumber', $phoneNumber['value']);

					// rel
					if (isset($phoneNumber['rel'])) {
						$element->setAttribute('rel', $phoneNumber['rel']);
					} else {
						$element->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
					}

					$entry->appendChild($element);
				}
			}

			// groups
			if (!empty($data['entry']['groupMembershipInfo'])) {
				$groupMembershipInfo = $data['entry']['groupMembershipInfo'];
				foreach ($groupMembershipInfo AS $group) {
					$element = $contact->createElement('gContact:groupMembershipInfo');

					// deleted
					$element->setAttribute('deleted', 'false');

					// href
					if (isset($group['href'])) {
						$element->setAttribute('href', $group['href']);	
					}

					$entry->appendChild($element);
				}
			}
		}

		$contact->appendChild($entry);
		$body = $contact->saveXML();

		$entryIdE = explode('base/', $data['entry']['id']);
		$contactId = $entryIdE['1'];
		$this->request = array(
			'uri' => array(
				'host' => 'www.google.com',
				'path' => 'm8/feeds/contacts/default/full/' . $contactId,
			),
			'method' => 'PUT',
			'header' => array(
				'Content-Type' => 'application/atom+xml',
				'Slug' => $data['entry']['title'],
				'If-Match' => $data['entry']['gd:etag'],
			),
			'auth' => array(
				'method' => 'OAuth',
			),
			'body' => $body,
		);

		$result = parent::save($data, $validate, $fieldList);
		
		/*if ($result){
			// In Google's documentation it looks like there should be a gd:resourceId node, but it appears
			// as simply resourceId to us. Keep an eye on this.
			if(empty($this->response['entry']['id'])) {
				trigger_error('No contact id from google.');
				return false;
			}
			$this->setInsertID($this->response['entry']['id']);
		}*/

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