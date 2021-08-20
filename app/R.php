<?php
declare(strict_types = 1);

namespace Legax\Resources;

final class R {

    private const ID_START_VALUE = 1000000;

    private static ?object $idClass = null;

    private static ?object $stringClass = null;

    private static ?object $stylesClass = null;

    private static ?object $arrayClass = null;

    private static ?object $scriptClass = null;
    
    private static ?object $imagesClass = null;

    private static array $idList = [];

    private static array $scriptList = [];

    private static array $imagesList = [];

    private static array $stylesList = [];

    private static array $stringList = [];

    private static array $arrayList = [];

    public static function scripts() : object {
        if(is_null(self::$scriptClass)) self::$scriptClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        if(empty(self::$scriptList)) {
            $path = pathJoin(ASSETS_PATH, 'js');
            foreach(scandir($path) as $item) {
                if(is_dir($item)) continue;
                $split = explode('.', $item);
                $name = implode('', array_slice($split, 0, count($split) - 1));
                self::$scriptList[$name] = file_get_contents(pathJoin($path, $item));
                self::$scriptClass->{$name} = self::$scriptList[$name];
            }
        }
        return self::$scriptClass;
    }

    public static function id(?string $idName = null) : object {
        if(is_null(self::$idClass)) self::$idClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        if(!is_null($idName) && !isset(self::$idList[$idName])) {
            $value = empty(self::$idList) ? self::ID_START_VALUE : max(self::$idList) + 1;
            self::$idList[$idName] = $value;
            self::$idClass->{$idName} = $value;
        }
        return self::$idClass;
    }

    public static function images() : object {
        if(is_null(self::$imagesClass)) self::$imagesClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        if(empty(self::$imagesList)) {
            $path = pathJoin(ASSETS_PATH, 'images');
            foreach(scandir($path) as $item) {
                if(is_dir($item)) continue;
                $split = explode('.', $item);
                $name = implode('', array_slice($split, 0, count($split) - 1));
                self::$imagesList[$name] = file_get_contents(pathJoin($path, $item));
                self::$imagesClass->{$name} = self::$imagesList[$name];
            }
        }
        return self::$imagesClass;
    }

    public static function arrays() : object {
        if(is_null(self::$arrayClass)) self::$arrayClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        $lang = getInterfaceLanguage();
        $path = file_exists(pathJoin(VALUES_PATH . '-' . $lang, 'string.json')) ?
            pathJoin(VALUES_PATH . '-' . $lang, 'string.json') :
            pathJoin(VALUES_PATH, 'string.json');
        if(empty(self::$arrayList)) {
            $data = json_decode(file_get_contents($path), true);
            foreach($data as $k => $v) {
                if(!is_array($v)) continue;
                self::$arrayList[$k] = $v;
                self::$arrayClass->{$k} = $v;
            }
        }
        return self::$arrayClass;
    }

    public static function styles() : object {
        if(is_null(self::$stylesClass)) self::$stylesClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        $lang = getInterfaceLanguage();
        $path = file_exists(pathJoin(VALUES_PATH . '-' . $lang, 'styles.json')) ?
            pathJoin(VALUES_PATH . '-' . $lang, 'styles.json') :
            pathJoin(VALUES_PATH, 'styles.json');
        if(empty(self::$stylesList)) {
            $data = json_decode(file_get_contents($path), true);
            foreach($data as $k => $v) {
                self::$stylesList[$k] = $v;
                self::$stylesClass->{$k} = $v;
            }
        }
        return self::$stylesClass;
    }

    public static function strings() : object {
        if(is_null(self::$stringClass)) self::$stringClass = new class {
            public function __get(string $name) {
                return $this->{$name} ?? null;
            }
        };
        $lang = getInterfaceLanguage();
        $path = file_exists(pathJoin(VALUES_PATH . '-' . $lang, 'string.json')) ?
            pathJoin(VALUES_PATH . '-' . $lang, 'string.json') :
            pathJoin(VALUES_PATH, 'string.json');
        if(empty(self::$stringList)) {
            $data = json_decode(file_get_contents($path), true);
            foreach($data as $k => $v) {
                if(is_array($v)) continue;
                self::$stringList[$k] = $v;
                self::$stringClass->{$k} = $v;
            }
        }
        return self::$stringClass;
    }

    private function __construct() {}

}