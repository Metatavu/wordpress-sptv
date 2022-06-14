<?php
  include "name.php";
  include "description.php";
  
  echo "<h2>" . __("Visiting information", "sptv") . "</h2>";

  include "addresses.php";
  include "service-hours.php";

  echo "<h2>" . __("Other contact details", "sptv") . "</h2>";

  if (getLocalizedValue($serviceChannel ["emails"], $data->language)) {
    echo "<b>" . __("Email", "sptv") . "</b>";
    include "email.php";
  }

  include "phone-numbers.php";

  if (getLocalizedValue($serviceChannel ["webPages"], $data->language)) {
    echo "<b>" . __("Website", "sptv") . "</b>";
    include "webpage.php";
  }

  echo "<h2>" . __("Accessibility information", "sptv") . "</h2>";

  include "accessibility.php";
?>