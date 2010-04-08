<?php

/**
 * Picaso Helper - For use with the GdataPicasa Source
 *
 * I just wanted to provide a jumping-off point to rendering data pulled from the Picasa API
 * 
 */
class PicasoHelper extends AppHelper {

	var $helpers = array('Html');

/**
 * Base render method. Passed data to appropriate render method according to specified type. You can also pass the data
 * the type parameter and this will try and figure out the what the data is.
 *
 * @param mixed $type Can be string (albums|photos|photo) or array of data
 * @param array $data if type parameter is a string, data is passed as second parameter
 * @return string output
 */
	public function render($type = null, $data = array()) {
		if (is_array($type)) {
			$data = Set::merge($type, $data);
			$type = $this->_analyzeDataType($data);
		}
		//debug($data);
		if (!empty($type) && !empty($data)) {
			$method = 'render'.Inflector::camelize($type);
			if (method_exists($this, $method)) {
				return call_user_func(array($this, $method), $data);
			} else {
				// This is not my beautiful house...
				//														... how did I get here?
			}
		}
		return null;
	}
	
/**
 * Render albums with link to album photos
 *
 * @param array $data
 */
	public function renderAlbums($data = array()) {
		$output = '';
		if (!empty($data)) {
			$output .= '<h1>Albums for ' . $data['author']['name'] . '</h1>';
			if (!empty($data['entry'])) {
				if (!isset($data['entry'][0])) {
					$data['entry'][0] = $data['entry'];
				}
				foreach ($data['entry'] as $album) {
					$output .= '<div class="album">';
					$output .= '<h2 class="album-title">' . $this->Html->link($album['title'], array('action' => 'album', $this->_pullIdFromHref($album['link'][0]['href']))) . '</h2>';
					$output .= '<img src="'.$album['group']['thumbnail']['url'].'" height="50" alt="" style="float:left; margin:0 1em 1em 0;" />';
					$output .= '<p class="summary">' . nl2br($album['summary']) . '</p>';
					$output .= '<p class="meta">' . $album['numphotos'] . ife(($album['numphotos'] <= 1), ' Photo', ' Photos') . ' | Published: ' . date('m-d-Y', strtotime($album['published'])) . ' | Location(s): ' . $album['location'] . '</p>';
					if ($album['numphotos'] > 0) {
						// render photos?
					}					
					$output .= '</div>';
				}
			} else {
				$output .= 'There are no albums available.';
			}
		}
		return $this->output($output);
	}

/**
* Render Photos in an album with links to photo detail
*
* @param array $data
* @return string
*/
	public function renderPhotos($data = array()) {
		$output = '';
		if (!empty($data)) {
			$output = '<h1>'. $data['title'] . '</h1>';
			if (!empty($data['entry'])) {
				if (empty($data['entry'][0])) {
					$data['entry'][0] = $data['entry'];
				}
				foreach ($data['entry'] as $photo) {
					$output .= '<div class="photo" style="float:left; padding:0 1em 1em 0;">';
					$output .= $this->Html->image($photo['group']['thumbnail'][1]['url'], array('title' => $photo['title'], 'url' => array('action' => 'photo', $this->_pullIdFromHref($photo['link'][0]['href']))));
					$output .= '</div>';
				}
			} else {
				$output .= 'There are no available photos in this album.';
			}
		}
		return $this->output($output);
	}

/**
* Render Photo data and comments
*
* @param array $data
* @return string
*/
	public function renderPhoto($data = array()) {
		$output = '';
		if (!empty($data)) {
			$output = '<h1>' . $data['title'] . '</h1>';
			$output .= $this->Html->image($data['group']['content']['url']);
		}
		return $this->output($output);
	}
	
/**
 * Internal method to analyze data to discover type. Primarily for use when no type is provided to base render method.
 *
 * @param array $data data from xml result
 * @return string (albums|photos|photo)
 */
	private function _analyzeDataType($data) {
		$type = null;
		if (!empty($data)) {
			if (!empty($data['category']['term'])) {
				switch (true) {
					case ($data['category']['term'] == 'http://schemas.google.com/photos/2007#user'):
						$type = 'albums';
					break;
					case ($data['category']['term'] == 'http://schemas.google.com/photos/2007#album'):
						$type = 'photos';
					break;
					case ($data['category']['term'] == 'http://schemas.google.com/photos/2007#photo'):
						$type = 'photo';
					break;
				}
			}
		}
		return $type;
	}
	
/** 
 * Quick and dirty pull from the numeric tail of the urls used as ids
 *
 * @param string href
 * @return string
 */
	private function _pullIdFromHref($href) {
		preg_match('/^.*\/([0-9]+)$/', $href, $matches);
		return $matches[1];
	}

}

?>
