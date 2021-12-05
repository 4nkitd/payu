<?php

namespace Dagar\PayU\Resources;

use Dagar\PayU\Genesis;

class View extends Genesis {

    protected function _loadFile($file_location) {

        return file_get_contents($file_location);

    }

    protected function load($view_name, $data = []) {

        $view_location = $this->_getViewLocation($view_name);

        if (!file_exists($view_location)) {
            throw new \Exception("View file not found: " . $view_location);
        }

        $view_content = $this->_loadFile($view_location);

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $view_content = str_replace("{{" . $key . "}}", $value, $view_content);
            }
        }

        return $view_content;

    }


}
