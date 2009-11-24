<?php
/**
 * DataSource for the Gdata Youtube API
 *
 */
class GdataYoutube extends GdataSource {

  /**
   * Used in the Gdata ClientLogin request
   *
   * @var string
   */
  protected $_service = 'youtube';

  /**
   * The URI of the Client Login.
   *
   * @var string
   */
  protected $_clientLoginUri = 'https://www.google.com/youtube/accounts/ClientLogin';

  /**
   * The R in CRUD
   * @param AppModel $model
   * @param array $queryData
   * @return array
   */
  public function read(&$model, $queryData = array()) {

    // Compile the request
    $request = array(
      'uri' => 'http://gdata.youtube.com/feeds/api/users/default/uploads'
    );

    $response = parent::read($model, $request);

    return $response;

  }

}
?>