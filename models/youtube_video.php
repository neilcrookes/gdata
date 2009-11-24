<?php

class YoutubeVideo extends GdataAppModel {

  var $name ='YoutubeVideo';
  var $useTable = false;
  var $useDbConfig = 'youtube';

  /**
   * Imports the datasources from the plugin and adds to the connection manager
   */
  function __construct() {
    App::import(array('type' => 'File', 'name' => 'Gdata.GDATA_CONFIG', 'file' => 'config'.DS.'gdata_config.php'));
    App::import(array('type' => 'File', 'name' => 'Gdata.GdataSource', 'file' => 'models'.DS.'datasources'.DS.'gdata_source.php'));
    App::import(array('type' => 'File', 'name' => 'Gdata.GdataYoutubeSource', 'file' => 'models'.DS.'datasources'.DS.'gdata'.DS.'gdata_youtube.php'));
    $config =& new GDATA_CONFIG();
    ConnectionManager::create('youtube', $config->youtube);
    parent::__construct();
  }
}

?>