<?php
  $data->templateLoader->get_template_part("components/service_location_service_channel/name");
  $data->templateLoader->get_template_part("components/service_location_service_channel/description");
  
  echo "<h3>" . __("Visiting information", "sptv") . "</h3>";

  $data->templateLoader->get_template_part("components/service_location_service_channel/addresses");
  $data->templateLoader->get_template_part("components/service_location_service_channel/service-hours");

  echo "<h3>" . __("Other contact details", "sptv") . "</h3>";

  if (getLocalizedValue($serviceChannel ["emails"], $data->language)) {
    echo "<b>" . __("Email", "sptv") . "</b>";
    $data->templateLoader->get_template_part("components/service_location_service_channel/email");
  }

  include "phone-numbers.php";

  if (getLocalizedValue($serviceChannel ["webPages"], $data->language)) {
    echo "<b>" . __("Website", "sptv") . "</b>";
    $data->templateLoader->get_template_part("components/service_location_service_channel/webpage");
  }

  echo "<h3>" . __("Accessibility information", "sptv") . "</h3>";

  $data->templateLoader->get_template_part("components/service_location_service_channel/accessibility");
?>