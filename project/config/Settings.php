<?php
class Settings {
    private static $file = __DIR__ . '/settings.json';

    public static function get($key, $default = null) {
        if (!file_exists(self::$file)) return $default;
        $data = json_decode(file_get_contents(self::$file), true);
        return isset($data[$key]) ? $data[$key] : $default;
    }

    public static function set($key, $value) {
        $data = [];
        if (file_exists(self::$file)) {
            $data = json_decode(file_get_contents(self::$file), true);
        }
        $data[$key] = $value;
        file_put_contents(self::$file, json_encode($data, JSON_PRETTY_PRINT));
        return true;
    }
}
?>
