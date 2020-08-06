<?php
/**
 * Bee plugin Notification Parts
 */
function hgodbee_beeplugin_notification_area() {
   echo '<div id="notification-area"></div>';
}

function hgodbee_beeplugin_notification($header, $text, $color ) {
    ?>
<div id="hgodbee-notification" class="ui <?php echo $color ?> message">
   <i class="close icon"></i>
   <div class="header">
      <?php echo $header; ?>
   </div>
   <p><?php echo $text; ?></p>
</div>
<?php
return;
}

/**
 * Wraper for hgodbee_plugin_notification
 *
 * @see hogbee_beeplugin_notification()
 * @see https://stackoverflow.com/a/528453/11085794
 * @param string $title
 * @param mixed $message
 * @param string $color
 * @return object ob_get_clean()
 */
function hgodbee_html_notification ($title, $message, $color) {
   ob_start();
   hgodbee_beeplugin_notification($title, $message, $color);
   return ob_get_clean();
}