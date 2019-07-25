<?php
  include "name.php";
  include "description.php";
  
  echo "<h3>" . __("Visiting information", "sptv") . "</h3>";

  include "addresses.php";
  include "service-hours.php";

  echo "<h3>" . __("Other contact details", "sptv") . "</h3>";

  if (getLocalizedValue($serviceChannel ["emails"], $data->language)) {
    echo "<b>" . __("Email", "sptv") . "</b>";
    include "email.php";
  }

  include "phone-numbers.php";

  if (getLocalizedValue($serviceChannel ["webPages"], $data->language)) {
    echo "<b>" . __("Website", "sptv") . "</b>";
    include "webpage.php";
  }

?>