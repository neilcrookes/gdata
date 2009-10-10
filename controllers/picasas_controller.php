<?php

class PicasasController extends GdataAppController {

	var $name = 'Picasas';
	
	var $helpers = array('Picaso');
	
	function index() {
		$albums = $this->Picasa->find('all');
		$this->set(compact('albums'));
	}
	
	function album($id = null) {
		try {
			if (empty($id)) {
				throw new Exception('No album id specified.');
			}
			$photos = $this->Picasa->find('all', array('conditions' => array('albumid' => $id)));
			if (empty($photos)) {
				throw new Exception('That album is inaccesible or does not exist.');
			}			
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect(array('action' => 'index'));
		}
		$this->set(compact('photos'));
	}
	
	function photo($id = null) {
		try {
			if (empty($id)) {
				throw new Exception('No photo id specified.');
			}
			$photo = $this->Picasa->find('all', array('conditions' => array('photoid' => $id)));
			if (empty($photo)) {
				throw new Exception('That photo is inaccesible or does not exist.');
			}			
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect(array('action' => 'index'));
		}
		$this->set(compact('photo'));
	}

}

?>
