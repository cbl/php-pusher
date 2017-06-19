<?php
namespace PhpPusher\Helper;

class Console
{

    private static $COLORS = [
        'red'   => '0;31',
        'green' => '0;32'
    ];

    public static function line($message) {
        echo "{$message}\n";
    }

    public static function error($message) {
        echo self::generateColorString('red', $message);
    }

    public static function success($message) {
        echo self::generateColorString('green', $message);
    }

    private static function generateColorString($color, $message) {
        return "{$message}\33[" . self::$COLORS[$color] . "m";
    }

}
