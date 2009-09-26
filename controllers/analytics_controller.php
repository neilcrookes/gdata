<?php
class AnalyticsController extends GdataAppController {

  var $name = 'Analytics';

  function index() {

    // If submitted form, redirect with form values in url
    if (!empty($this->data)) {
      $redirect = array();
      foreach ($this->data[$this->modelClass] as $k => $v) {
        if (is_array($v)) {
          if (strpos($k, 'date')) {
            $v = $v['year'].'-'.$v['month'].'-'.$v['day'];
          } else {
            $v = implode(',', $v);
          }
        }
        $redirect[$k] = $v;
      }
      $this->redirect($redirect);
    } elseif (!empty($this->passedArgs)) {
      // Load url params into this->data to re-populate form values
      $this->data[$this->modelClass] = $this->passedArgs;
    } else {
      // Set up form defaults
      $this->data[$this->modelClass] = array(
        'start_date' => date('Y-m-d', strtotime('-1 month')),
        'end_date' => date('Y-m-d'),
        'metrics' => 'visitors',
        'dimensions' => 'year,month',
      );
    }

    // Set up pagination conditions for dates
    $this->paginate[$this->modelClass]['conditions'] = array(
      'start_date' => $this->data[$this->modelClass]['start_date'],
      'end_date' => $this->data[$this->modelClass]['end_date'],
    );

    // Set the fields param as the metrics
    $this->paginate[$this->modelClass]['fields'] = $this->data[$this->modelClass]['metrics'];

    // Set the group param as the dimensions
    $this->paginate[$this->modelClass]['group'] = $this->data[$this->modelClass]['dimensions'];

    $results = $this->paginate($this->modelClass);

    $metrics = $dimensions = array();

    $metricCategories = $this->{$this->modelClass}->metrics;
    $dimensionCategories = $this->{$this->modelClass}->dimensions;

    foreach ($metricCategories as $category => $categoryMetrics) {
      foreach ($categoryMetrics as $metric => $description) {
        $metrics[$category][$metric] = '<abbr title="'.$description.'">'.$metric.'</abbr>';
      }
    }
    foreach ($dimensionCategories as $category => $categoryDimensions) {
      foreach ($categoryDimensions as $dimension => $description) {
        $dimensions[$category][$dimension] = '<abbr title="'.$description.'">'.$dimension.'</abbr>';
      }
    }

    $this->set(compact('results', 'metrics', 'dimensions'));

    // Separate out date components and comma separated values
    foreach ($this->data[$this->modelClass] as $k => $v) {
      if (strpos($k, 'date')) {
        list($this->data[$this->modelClass][$k]['year'], $this->data[$this->modelClass][$k]['month'], $this->data[$this->modelClass][$k]['day']) = explode('-', $v);
      } else {
        $this->data[$this->modelClass][$k] = explode(',', $v);
      }
    }
  }

}
?>