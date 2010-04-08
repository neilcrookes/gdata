<?php

class Picasa extends GdataAppModel {

	var $name ='Picasa';
	var $useTable = false;
	var $useDbConfig = 'picasa';

/**
* Imports the datasources from the plugin and adds to the connection manager
*/
	public function __construct() {
		App::import(array('type' => 'File', 'name' => 'Gdata.GDATA_CONFIG', 'file' => 'config'.DS.'gdata_config.php'));
		App::import(array('type' => 'File', 'name' => 'Gdata.GdataSource', 'file' => 'models'.DS.'datasources'.DS.'gdata_source.php'));
		App::import(array('type' => 'File', 'name' => 'Gdata.GdataPicasa', 'file' => 'models'.DS.'datasources'.DS.'gdata'.DS.'gdata_picasa.php'));
		$config =& new GDATA_CONFIG();
		ConnectionManager::create('picasa', $config->picasa);
		parent::__construct();
	}
}
?>
