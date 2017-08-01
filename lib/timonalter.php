<?php

class timonalter extends ExpressiveDate {


  /**
   * Get a relative date string, e.g., 3 days ago.
   *
   * ab Geburt: erst Tage, dann Wochen, ab 4 Wochen zu den Monaten immer noch die Wochen zeigen bis 1 Jahr
   *
   * @param  ExpressiveDate  $compare
   * @return string
   */
  public function getRelativeDate($compare = null)
  {
    if ( ! $compare) {
      $compare = new ExpressiveDate(null, $this->getTimezone());
    }

    $units = array('Sekunde', 'Minute', 'Stunde', 'Tag', 'Woche', 'Monat', 'Jahr');
    $units_pl = array('Sekunden', 'Minuten', 'Stunden', 'Tage', 'Wochen', 'Monate', 'Jahre');
    $values = array(60, 60, 24, 7, 4.35, 12);

    // Get the difference between the two timestamps. We'll use this to cacluate the
    // actual time remaining.
    $difference = abs($compare->getTimestamp() - $this->getTimestamp());

    for ($i = 0; $i < count($values) and $difference >= $values[$i]; $i++) {
      $difference = $difference / $values[$i];
    }

    // Round the difference to the nearest whole number.
    $difference = round($difference);

    $nach_geburt = false;
    if ($compare->getTimestamp() < $this->getTimestamp()) {
      $suffix = ''; //'alt';
      $nach_geburt = true;
    } else {
      $suffix = ' noch';
    }

    // Get the unit of time we are measuring. We'll then check the difference, if it is not equal
    // to exactly 1 then it's a multiple of the given unit so we'll append an 's'.
    $unit = $units[$i];

    if ($difference != 1) {
      $unit = $units_pl[$i];
    }

    // nach Geburt die Wochen zusätzlich ab 5 Wochen
    $additional = '';
    if ($nach_geburt) {
      $week_min = 5 * 7*24*60*60;
      $week_max = 53 * 7*24*60*60;
      $week_diff = abs($compare->getTimestamp() - $this->getTimestamp());
      if ($week_diff >= $week_min && $week_diff <= $week_max) {
        $weeks = round($week_diff / (7*24*60*60));
        $additional = ' / '.$weeks.' '.$units_pl[4];
      }
    }

    return $difference.' '.$unit.$additional.$suffix;
  }

}