<?php
// Import the Rest Data source that this extends
require('rest_source.php');
/**
 * A base datasource used by all Gdata service data sources
 *
 * Handles authentication and results parsing e.g. extracting the number of
 * results returned from the request
 *
 */
class GdataSource extends RestSource {

  /**
   * Stores the Auth hash returned from Google
   *
   * @var string
   */
  protected $_auth = false;

  /**
   * Logs in to Google using the email, password and source values set in the
   * data source config.
   *
   * @return boolean
   */
  public function connect() {

    $this->connected = false;

    // Construct the google login request
    $request = array(
      'uri' => 'https://www.google.com/accounts/ClientLogin',
      'method' => 'POST',
      'body' => array(
        'accountType' => 'HOSTED_OR_GOOGLE',
        'Email' => $this->config['email'],
        'Passwd' => $this->config['passwd'],
        'service' => $this->_service,
        'source' => $this->config['source'],
      ),
    );

    // Issue the request using the RestSource (which is it's parent)
    $response = parent::_request($request);

    // Parse the response into an array
    parse_str(str_replace(array("\n", "\r\n"), '&', $response), $response);

    // Check we have the Auth part of the response
    if (!is_string($response['Auth'])) {
    	trigger_error("Request to Google for Authentication failed.", ERROR_WARN);
      return false;
    }

    // Store the Auth hash returned from Google for later requests
    $this->_auth = $response['Auth'];

    $this->connected = true;

    return $this->connected;

  }

  /**
   * Required if we have a connect method
   *
   * @return boolean
   */
  public function close() {
    return true;
  }

  /**
   * Connects if not already connected to Google, then adds the Authorisation
   * header into the request
   *
   * @param array $request An HTTP Request as used by HttpSocket
   * @return array
   * @access protected
   */
  protected function _request($request) {

    // Attempts to connect to google if not connected, if fails, returns false
    if (!$this->connected && !$this->connect()) {
      return false;
    }

    // Add in authorisation header
    $request = Set::merge(array(
      'header' => array(
        'Authorization' => 'GoogleLogin auth=' . $this->_auth
      ),
    ), $request);

    // Get the response from calling _request on the Rest Source (it's parent)
    $response = parent::_request($request);

    // If response is false, add the body to the errors property
    if (!$response) {
      $this->_errors[] = $this->Http->response['body'];
      return false;
    }

    // Set the number of rows and results properties from the response
    $this->numRows = $response['feed']['totalResults'];
    $this->_result = $response['feed'];

    // Return the result
    return $this->_result;

  }

}
?>
