<?php

  /** 
   * Returns localized value from an array
   * 
   * @param object[] $values array of localized values
   * @param string $language preferred language
   * @param string $type filter results by type. optional
   */
  function getLocalizedValue($values, $language, $type = null) {
    $filtered = array_filter($values, function($value) use($type) {
      return !empty($value["value"]) && (!$type || ($value["type"] == $type));
    });

    usort($filtered, function ($a, $b) {
      return $a["language"] == $language ? -1 : 1;
    });

    return $filtered[0] ? $filtered[0]["value"] : "";
  }

  /**
   * Returns localized day name
   * 
   * @param $dayName day name
   * @return localized day name
   */
  function getLocalizedDayName($dayName) {
    switch ($dayName) {
      case "Monday":
        return __("Monday", "sptv");
      case "Tuesday":
        return __("Tuesday", "sptv");
      case "Wednesday":
        return __("Wednesday", "sptv");
      case "Thursday":
        return __("Thursday", "sptv");
      case "Friday":
        return __("Friday", "sptv");
      case "Saturday":
        return __("Saturday", "sptv");
      case "Sunday":
        return __("Sunday", "sptv");
    }

    return $dayName;
  }

  /**
   * Converts wraps text into html paragraphs by line breaks
   * 
   * @param string $text text
   * @return string text as html
   */
  function nl2p($text) {
    if (!$text) {
      return "";
    }

    return implode("", array_map(function ($line) {
      return "<p>$line</p>";
    }, explode("\n", $text)));
  }

  /**
   * Formats opening hour object.
   * 
   * @param object $openingHour openingHour
   * @return string formatted object
   */
  function formatOpeningHour($openingHour) {
    $days = isset($openingHour['dayFrom']) ? getLocalizedDayName($openingHour['dayFrom']) : '';
    $from = "";
    $to = "";
    
    if (isset($openingHour['dayTo'])) {
      $days .= ' - ' . getLocalizedDayName($openingHour['dayTo']);
    }
    
    if (isset($openingHour['from'])) {
      $from = implode('.', array_slice(explode(':', $openingHour['from']), 0, 2));
    }
    
    if (isset($openingHour['to'])) {
      $to = implode('.', array_slice(explode(':', $openingHour['to']), 0, 2));
    }
    
    if (!empty($from) || !empty($to)) {
      return "${days} ${from} - ${to}";
    } else {
      return "${days} ${from}";
    }
  }

  /**
   * Formats list of opening hours
   * 
   * @param object[] $openingHours openingHours
   * @return string formatted string
   */
  function formatOpeningHours($openingHours) {
    return implode(", ", array_map(function ($openingHour) {
      return formatOpeningHour($openingHour);
    }, $openingHours));
  }

?>