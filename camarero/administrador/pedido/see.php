<?php
// SSE.php

class SSE {
    public function start($id) {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Nginx: disable buffering

        // Set the session ID
        session_id($id);
        session_start();
    }

    public function sendEvent($event, $data) {
        echo "event: $event\n";
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
}
?>
