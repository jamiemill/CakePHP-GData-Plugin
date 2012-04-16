<?php

/**
 * DataSource for the Gdata Google Contacts Data API
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://fahad19.com
 * @copyright (c) 2011 Fahad Ibnay Heylaal
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
class GoogleContacts extends GdataSource {

  /**
   * The scope for this datasource that must be supplied to Google when getting
   * an OAuth request token
   *
   * @var string
   */
  public $oAuthScope = 'https://www.google.com/m8/feeds/';

  /**
   * Inserts the default hostname and scheme in the request uri if not already
   * set and then calls the parent request() method
   *
   * @param Model $model The model object that operation is performed on,
   * @return mixed The response from the parent datasource request()
   */
  public function request(&$model) {

    if (!isset($model->request['uri']['host'])) {
      $model->request['uri']['host'] = 'www.google.com';
    }

    if (!isset($model->request['uri']['scheme'])) {
      $model->request['uri']['scheme'] = 'https';
    }

    $response = parent::request($model);

    return $response;

  }

}
?>