<style type="text/css">
  * {
    font:12px verdana,helvetica,arial,sans-serif;
  }
  .checkbox {
    float:left;
    width:245px;
    clear:none;
    margin-bottom:0;
    padding:0;
  }
  fieldset {
    padding:0 0 0 0.5em;
    margin:0;
    border:0;
  }
  fieldset div {
    display:none;
  }
  legend {
    border-bottom:1px dotted;
    padding:0;
    cursor:pointer;
  }
  abbr {
    cursor:help;
  }
  ul {
    margin:1em 0;
    padding:0;
  }
  li {
    list-style:none;
    float:left;
    margin-right:0.5em;
  }
  table {
    border-collapse:collapse;
  }
  th,td {
    border:1px solid #999;
    padding:1px 3px;
  }
</style>
<?php
echo $form->create('Analytic', array('url' => array('action' => 'index')));
echo $form->input('start_date', array('type' => 'date'));
echo $form->input('end_date', array('type' => 'date'));
echo $form->input('metrics', array('multiple' => 'checkbox', 'escape' => false));
echo $form->input('dimensions', array('multiple' => 'checkbox', 'escape' => false));
echo $form->end('Show');
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript">
$("legend").click(function() { $(this).nextAll().toggle(); });
</script>
<?php
if (!$results) :
  echo '<p>Invalid combination, here\'s a list of <a href="http://code.google.com/apis/analytics/docs/gdata/gdataReferenceValidCombos.html">valid combinations</a></p>';
elseif (!empty($results['entry'])) :
  $paginator->options(array('url' => $this->passedArgs));
  echo $paginator->counter(array(
    'format' => __('Results %start% to %end% of <b>%count%</b>', true)
  ));
  ?>
  <table>
    <thead>
      <tr>
        <?php
        function arrayalise($var) {
          if (isset($var[0])) {
            return $var;
          }
          return array($var);
        }
        $results['entry'] = arrayalise($results['entry']);
        foreach (arrayalise($results['entry'][0]['dimension']) as $dimension) {
          echo '<th>'.$paginator->sort(substr($dimension['name'], 3)).'</th>';
        }
        foreach (arrayalise($results['entry'][0]['metric']) as $metric) {
          echo '<th>'.$paginator->sort(substr($metric['name'], 3)).'</th>';
        }
        ?>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($results['entry'] as $entry) {
        echo '<tr>';
        foreach (arrayalise($entry['dimension']) as $dimension) {
          echo '<td>'.$dimension['value'].'</td>';
        }
        foreach (arrayalise($entry['metric']) as $metric) {
          echo '<td>'.$metric['value'].'</td>';
        }
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
  <ul>
    <li><?php echo $paginator->prev(__('Previous', true), array(), null, array('class'=>'disabled', 'tag' => 'span'));?>
    <?php echo $paginator->numbers(array('tag' => 'li', 'separator' => ''));?>
    <li><?php echo $paginator->next(__('Next', true), array(), null, array('class'=>'disabled', 'tag' => 'span'));?></li>
  </ul>
<?php else : ?>
  <p>No results</p>
<?php endif; ?>