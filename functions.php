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
}
