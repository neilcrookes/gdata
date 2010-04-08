<?php
/**
 * DataSource for the Gdata Picasa API
 * 
 * "I want to give a shoutout to my Mom; she's right here." I love you mom!
 * ..-. .- .. .-.. ..-. .- ... -
 *
 */
class GdataPicasa extends GdataSource {

/**
 * Used in the Gdata ClientLogin request
 *
 * @link http://code.google.com/support/bin/answer.py?answer=62712&topic=10711
 * @var string
 */
	protected $_service = 'lh2';
	
/**
 * Valid keys for use as uri parameters.
 *
 * @param array $_validUriKeys
 */
	protected $_uriKeys = array(
		'user', 'albumid', 'photoid'
	);
	
/**
 * Base uri used in building uri for use in http request.
 *
 * @param string $_baseUri
 */
	protected $_baseUri = 'http://picasaweb.google.com/data/feed/api/';
	
/**
 * The R in CRUD
 *
 * @param object $model the AppModel
 * @param array $queryData a multi-dimensional array of query parameters
 * @return array
 */
	public function read(&$model, $queryData = array()) {		
		
		$uriStringParams = array(
			'user' => 'default'
		);
		
		$queryStringParams = array();
		
		if (!empty($queryData['conditions'])) {
			$conditions = $queryData['conditions'];
			
			foreach ($this->_uriKeys as $key) {
				if (!empty($conditions[$key])) {
					$uriStringParams[$key] = $conditions[$key];
					unset($conditions[$key]);
				}
			}
			
			if (!empty($conditions['tag'])) {
				if (is_array($conditions['tag'])) {
					$conditions['tag'] = implode(',', $conditions['tag']);
				}
				if (empty($conditions['kind'])) {
					$conditions['kind'] = 'photo';
				}
			}
			
			$queryStringParams = $conditions;
			
		}
		
		// Compile the request
		$request = array('uri' => $this->_build_picasa_uri($uriStringParams, $queryStringParams));

		$response = parent::read($model, $request);
		
		return $response;
	}

/**
 * Method to process uri and query string parameters for use as http request.
 *
 * @param array $uriStringParams Two-dimensional array of keys and avlues for appendage to base uri
 * @param array $queryStringParams Two-dimensional array of keys and values for use as parameters in uri
 * @return string
 */
	protected  function _build_picasa_uri($uriStringParams = array(), $queryStringParams = array()) {
		$result = $this->_baseUri;
		if (!empty($uriStringParams)) {
			$_result = array();
			foreach ($this->_uriKeys as $key) {
				if (!empty($uriStringParams[$key])) {
					$_result[] = $key . "/" . $uriStringParams[$key];
				}
			}
			$result .= implode("/", $_result);
		}
		if (!empty($queryStringParams)) {
			$result .= '?' . http_build_query($queryStringParams);
		}
		return $result;
	}

/**
 * Picasa Developer's guide suggests adding GData Version header to every request. Merging value and passing to parent
 * class' request method.
 *
 * @link http://code.google.com/apis/picasaweb/docs/2.0/developers_guide_protocol.html#Versioning
 * @param array $request An HTTP Request as used by HttpSocket
 * @return array
 */
	protected function _request($request) {
		$request = Set::merge(array(
			'header' => array(
				'GData-Version' => '2'
			)
		), $request);
		if ($this->config['cache']) {
			if (!$result = $this->_getCache($request)) {
				$result = parent::_request($request);
				$this->_setCache($request, $result);
			}
		} else {
			$result = parent::_request($request);
		}
		return $result;
	}
	
/**
 * Set result data as cache for a paricular request
 *
 * @param array $request Will be serialized and md5 to create cache key
 * @param mixed $result data to be cached
 * @return mixed will return $result if set, otherwise (bool) false
 */
	private function _setCache($request, $result = null) {
		$key = $this->_requestToCacheKey($request);
		Cache::set(array('duration' => $this->config['cacheDuration']));
		if (Cache::write($key, $result)) {
			return $result;
		} else {
			return false;
		}
	}
	
	private function _getCache($request) {
		$key = $this->_requestToCacheKey($request);
		$cached = Cache::read($key);
		if ($cached !== false) {
			return $cached;
		}
		return false;
	}
	
	private function _requestToCacheKey($request) {
		return 'picasa_' . md5(serialize($request));
	}
}

?>
