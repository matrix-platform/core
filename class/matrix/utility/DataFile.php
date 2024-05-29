<?php //>

namespace matrix\utility;

trait DataFile {

    private function getData($name, $token) {
        $folder = create_folder(APP_DATA . $name);

        if ($folder) {
            $file = "{$folder}/{$token}";

            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true);
            }
        }

        return false;
    }

    private function removeData($name, $token) {
        $folder = create_folder(APP_DATA . $name);

        unlink("{$folder}/{$token}");
    }

    private function saveData($name, $data) {
        $folder = create_folder(APP_DATA . $name);

        if ($folder) {
            while (true) {
                $token = sha1(uniqid('', true));
                $file = "{$folder}/{$token}";

                if (!file_exists($file)) {
                    file_put_contents($file, json_encode($data));

                    return $token;
                }
            }
        }

        return false;
    }

}
