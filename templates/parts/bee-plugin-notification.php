<?php
/**
 * Bee plugin Notification Parts
 */
function hgodbee_beeplugin_notification_area() {
   ?> 
   <div id="notification-area"></div>
   <?php
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
}