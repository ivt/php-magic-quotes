<?php

namespace IVT;

/**
 * Class to control whether global variables $_GET, $_POST, $_COOKIE, $_REQUEST and $_FILES have quotes escaped with
 * slashes (magic quotes enabled/disabled).
 */
final class MagicQuotes {
    /**
     * @var bool|null
     */
    private static $enabled;

    /**
     * Get whether magic quotes are enabled.
     * @return bool
     */
    public static function get() {
        // The initial value is whatever PHP starts with
        if (self::$enabled === null) {
            self::$enabled = (bool)\get_magic_quotes_gpc();
        }
        return self::$enabled;
    }

    /**
     * Set whether magic quotes are enabled. The global variables are updated to reflect the new setting.
     * @param bool $enabled Whether magic quotes should be enabled.
     * @return bool The previous setting.
     * @throws \Exception
     */
    public static function set($enabled) {
        if (!\is_bool($enabled)) {
            throw new \Exception('Expected a bool');
        }
        $previous = self::get();
        if ($previous != $enabled) {
            $_GET = self::modify($_GET, $enabled);
            $_POST = self::modify($_POST, $enabled);
            $_COOKIE = self::modify($_COOKIE, $enabled);
            $_REQUEST = self::modify($_REQUEST, $enabled);
            $_FILES = self::modify($_FILES, $enabled);
            self::$enabled = $enabled;
        }
        return $previous;
    }

    /**
     * If magic quotes are currently enabled, strip slashes from the given value.
     * @param mixed $x
     * @return mixed
     */
    public static function strip($x) {
        return self::get() ? self::modify($x, false) : $x;
    }

    /**
     * If magic quotes are currently enabled, add slashes to the given value.
     * @param mixed $x
     * @return mixed
     */
    public static function add($x) {
        return self::get() ? self::modify($x, true) : $x;
    }

    /**
     * Add or remove slashes from the given value.
     * @param mixed $x
     * @param bool $add
     * @return mixed
     */
    private static function modify($x, $add) {
        if (\is_string($x)) {
            return $add ? \addslashes($x) : \stripslashes($x);
        }
        if (\is_array($x)) {
            $ret = [];
            foreach ($x as $k => $v) {
                $k = self::modify($k, $add);
                $v = self::modify($v, $add);
                $ret[$k] = $v;
            }
            return $ret;
        }
        return $x;
    }

    private function __construct() {
    }
}
