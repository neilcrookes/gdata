<?php
/**
 * DataSource for the Gdata analytics API
 *
 */
class GdataAnalytics extends GdataSource {

  /**
   * Used in the Gdata ClientLogin request
   *
   * @var string
   */
  protected $_service = 'analytics';

  /**
   * The R in CRUD
   *
   * Returns the results for the metric corresponding to the model on which find
   * is called according to the queryData supplied.
   *
   * Valid queryData options include:
   *  - conditions
   *    - start_date
   *      Format: Y-m-d
   *      Required: True
   *    - end_date
   *      Format: Y-m-d
   *      Required: True
   *  - page
   *    Format: integer
   *    Required: False
   *    Default: 1
   *  - limit
   *    Format: integer
   *    Required: False
   *    Default: 50
   *
   * @param AppModel $model
   * @param array $queryData
   * @return array
   */
  public function read(&$model, $queryData = array()) {

    // Validate
    if (!isset($queryData['conditions']['start_date'])) {
      trigger_error(__('start_date is a required condition', true), E_USER_WARNING);
      return false;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $queryData['conditions']['start_date'])) {
      trigger_error(__('start_date must be in the format yyyy-mm-dd', true), E_USER_WARNING);
      return false;
    }

    if (!isset($queryData['conditions']['end_date'])) {
      trigger_error(__('end_date is a required condition', true), E_USER_WARNING);
      return false;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $queryData['conditions']['end_date'])) {
      trigger_error(__('end_date must be in the format yyyy-mm-dd', true), E_USER_WARNING);
      return false;
    }

    if (isset($queryData['limit'])) {
      if (!preg_match('/^\d+$/', $queryData['limit'])) {
        trigger_error(__('limit must be an integer', true), E_USER_WARNING);
        return false;
      }
      if ($queryData['limit'] < 1
      || $queryData['limit'] > 10000) {
        trigger_error(__('limit must be between 1 and 10,000', true), E_USER_WARNING);
        return false;
      }
      $maxResults = $queryData['limit'];
    } else {
      $maxResults = 50;
    }

    if (isset($queryData['page'])) {
      if (!preg_match('/^\d+$/', $queryData['page'])) {
        trigger_error(__('page must be an integer', true), E_USER_WARNING);
        return false;
      }
      if ($queryData['page'] < 1) {
        trigger_error(__('page must be greater than or equal to 1', true), E_USER_WARNING);
        return false;
      }
      $startIndex = 1 + ($queryData['page'] - 1) * $maxResults;
    } else {
      $startIndex = 1;
    }

    // An anonymous function for prefixing an arg with "ga:"
    $prefix = create_function('&$v', '$v = "ga:".$v;');

    // Prefixes all metrics with "ga:"
    $metrics = explode(',', $queryData['fields']);
    array_walk($metrics, $prefix);
    $metrics = implode(',', $metrics);

    // Prefixes all dimensions with "ga:"
    $dimensions = explode(',', $queryData['group']);
    array_walk($dimensions, $prefix);
    $dimensions = implode(',', $dimensions);

    // Initialising query string params array
    $queryStringParams = array(
      'metrics' => $metrics,
      'dimensions' => $dimensions,
      'start-date' => $queryData['conditions']['start_date'],
      'end-date' => $queryData['conditions']['end_date'],
      'ids' => 'ga:'.$this->config['profileId'],
      'start-index' => $startIndex,
      'max-results' => $maxResults,
      'prettyprint' => 'true',
    );

    // Add order to query string params if set
    $queryData['order'] = array_filter($queryData['order']);
    if (!empty($queryData['order'])) {
      $sorts = explode(',', $queryData['order'][0]);
      foreach ($sorts as $sort) {
        list($sort, $direction) = preg_split('/\s/', $sort);
        list($alias, $field) = explode('.', $sort);
        $sort = 'ga:'.$field;
        if (strtolower($direction) == 'desc') {
          $sort = '-'.$sort;
        }
        $queryStringParams['sort'][] = $sort;
      }
      $queryStringParams['sort'] = implode(',', $queryStringParams['sort']);
    }

    // Compile the request
    $request = array('uri' => 'https://www.google.com/analytics/feeds/data?'.http_build_query($queryStringParams));

    $response = parent::read($model, $request);

    return $response;

  }

}
?>