<?php
//  session_start();

  $str = file_get_contents('products.json');
  $json = json_decode($str, true);
//   echo '<pre>' . print_r($json, true) . '</pre>';
foreach ($json as $key => $value) {
            if (!is_array($value)) {
                echo $key . '=>' . $value . '<br/>';
            } else {
                foreach ($value as $key => $val) {
                    echo $key . '=>' . $val . '<br/>';
                }
            }
            echo '<br/>';
        }
?>