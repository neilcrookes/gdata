<?php if (empty($youtubeVideos['entry'])) : ?>
  <p>Sorry, there are no macthing videos</p>
<?php else : ?>
  <table>
    <tr>
      <th>Thumbnail</th>
      <th>Title</th>
      <th>Content</th>
      <th>Location</th>
      <th>Category</th>
      <th>Keywords</th>
      <th>Duration</th>
      <th>Published</th>
      <th>Updated</th>
    </tr>
    <?php foreach ($youtubeVideos['entry'] as $youtubeVideo) : ?>
      <tr>
        <td>
          <?php
          $thumbnail = current($youtubeVideo['group']['thumbnail']);
          $image = $html->image($thumbnail['url'], array(
            'width' => $thumbnail['width'],
            'height' => $thumbnail['height'],
            'alt' => $thumbnail['time'],
          ));
          echo $html->link($image, $youtubeVideo['group']['player']['url'], array('escape' => false));
          ?>
        </td>
        <td><?php echo $html->link($youtubeVideo['title']['value'], $youtubeVideo['group']['player']['url']); ?></td>
        <td><?php echo $youtubeVideo['content']['value']; ?></td>
        <td><?php echo $youtubeVideo['location']; ?></td>
        <td><?php echo $youtubeVideo['group']['category']['label']; ?></td>
        <td><?php echo $youtubeVideo['group']['keywords']; ?></td>
        <td><?php echo $youtubeVideo['group']['duration']['seconds']; ?></td>
        <td><?php echo $youtubeVideo['published']; ?></td>
        <td><?php echo $youtubeVideo['updated']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>