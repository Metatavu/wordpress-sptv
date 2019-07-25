<?php
  include "name.php";
  include "description.php";
  
  echo "<h3>" . __("Visiting information", "sptv") . "</h3>";

  include "addresses.php";
  include "service-hours.php";

  echo "<h3>" . __("Other contact details", "sptv") . "</h3>";

  include "phone-numbers.php";

?>