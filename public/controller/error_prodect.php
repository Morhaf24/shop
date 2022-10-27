<?php
    function error_product($message, $code) {
        $error = array("message" => $message);

    echo json_encode($error);
        http_response_code($code);
        
        die();
 }
?>

