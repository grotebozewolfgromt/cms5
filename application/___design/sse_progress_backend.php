<?php
header('Content-Type: text/event-stream');
// recommended to prevent caching of event data.
header('Cache-Control: no-cache'); 
 
function send_message($id, $message, $progress) {
    $d = array('message' => $message , 'progress' => $progress);
     
    echo "id: $id" . PHP_EOL;
    echo "data: " . json_encode($d) . PHP_EOL;
    echo PHP_EOL;
     
    ob_flush();
    flush();
}
 
 
//LONG RUNNING TASK
for($i = 1; $i <= 3600; $i++) {
    send_message($i, 'on iteration ' . $i . ' of 20' , $i*5); 

    sleep(1);
}
 
send_message('CLOSE', 'Process complete', 0);
?>