<?php
/**
 * DataSource for interacting with REST APIs
 *
 */
class RestSource extends DataSource {

  /**
   * The description of this data source
   *
   * @var string
   */
  public $description = 'Rest DataSource';

  /**
   * Loads HttpSocket class
   *
   * @param array $config
   */
  public function __construct($config) {
    parent::__construct($config);
    App::import('Core', 'HttpSocket');
    $this->Http = new HttpSocket();
  }

  /**
   * Sets method = POST in query data if not already set
   *
   * @param AppModel $model
   * @param array $queryData
   */
  public function create(&$model, $queryData = array()) {
    $queryData = Set::merge(array('method' => 'POST'), $queryData);
    $this->_request($request);
  }

  /**
   * Sets method = GET in query data if not already set
   *
   * @param AppModel $model
   * @param array $queryData
   */
  public function read(&$model, $queryData = array()) {
    $queryData = Set::merge(array('method' => 'GET'), $queryData);
    return $this->_request($queryData);
  }

  /**
   * Sets method = PUT in query data if not already set
   *
   * @param AppModel $model
   * @param array $queryData
   */
  public function update(&$model, $fields = null, $values = null) {
    $queryData = Set::merge(array('method' => 'PUT'), $queryData);
    return $this->_request($queryData);
  }

  /**
   * Sets method = DELETE in query data if not already set
   *
   * @param AppModel $model
   * @param array $queryData
   */
  public function delete(&$model, $id = null) {
    if ($id == null) {
      $id = $model->id;
    }
    $queryData[$model->primaryKey] = $id;
    $queryData = Set::merge(array('method' => 'DELETE'), $queryData);
    return $this->_request($queryData);
  }

  /**
   * Issues request and returns array from decoded response according to
   * response's content type.
   *
   * @param array $request
   * @return array
   */
  protected function _request($request) {

    // Issues request
    $response = $this->Http->request($request);

    // CHeck response code
    if ($this->Http->response['status']['code'] != 200) {
      return false;
    }

    // Get content type header
    $contentType = $this->Http->response['header']['Content-Type'];

    // Extract content type from content type header
    if (preg_match('/^([a-z0-9\/\+]+);\s*charset=([a-z0-9\-]+)$/i', $contentType, $matches)) {
      $contentType = $matches[1];
      $charset = $matches[2];
    }

    // Decode response according to content type and return an array
    switch ($contentType) {
    	case 'application/atom+xml':
    	  App::import('Core', 'Xml');
      	$Xml = new Xml($response);
      	return $Xml->toArray(false); // Send false to get separate elements
      	break;

    	default:
    		return $response;
    	  break;
    }

  }

}
?>