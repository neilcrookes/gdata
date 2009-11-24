<?php
class YoutubeVideosController extends GdataAppController {

  var $name = 'YoutubeVideos';

  function index() {
    $youtubeVideos = $this->YoutubeVideo->find('all');
    $this->set(compact('youtubeVideos'));
  }

}
?>