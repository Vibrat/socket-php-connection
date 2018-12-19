<?php


class CoreFunction {
    /**
     * Split response into Array
     */
    function parseHTTP(String $data) {
        $response = [];

        $data = preg_split('/\r\n/', $data);

        foreach ($data as $index=>$inItem) {
            if ($inItem) {
                $bundle = preg_split('/: /', $inItem);
                $response[(isset($bundle[1]) ? $bundle[0] : 'Header')] = (isset($bundle[1]) ? $bundle[1] : $inItem);
            }
        }

        return $response;
    }

    // Unmask incoming framed message
    function unmask($text) {
        $length = ord($text[1]) & 127;
        if($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        }
        elseif($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        }
        else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i%4];
        }
        return $text;
    }

    function arrayMapSocket($array, $name) {
        return (count($array) ? array_map (function($item) use ($name) {
            return $item[$name];
        }, $array) : []);
    }
}
