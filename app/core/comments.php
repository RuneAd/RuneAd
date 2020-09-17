<?php
  require __DIR__ . '/vendor/autoload.php';

  $options = array(
    'cluster' => 'us2',
    'useTLS' => true
  );
  $pusher = new Pusher\Pusher(
    '2c60f504aff0399cb05c',
    '12309aff82d063c22501',
    '1074984',
    $options
  );

  $data['message'] = 'hello world';
  $pusher->trigger('my-channel', 'my-event', $data);
?>