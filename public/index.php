<?php

use Legax\Core\Document;
use Legax\UI\Views\View;

error_reporting(E_ALL);//Remove this line in production mode

require_once __DIR__  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'helpers.php';

define('ROOT_PATH', dirname(__DIR__));

define('APP_PATH', pathJoin(ROOT_PATH, 'app'));

define('LIB_PATH', pathJoin(ROOT_PATH, 'lib'));

define('LAYOUTS_PATH', pathJoin(ROOT_PATH, 'layouts'));

define('STORAGE_PATH', pathJoin(ROOT_PATH, 'storage'));

define('SRC_PATH', pathJoin(ROOT_PATH, 'src'));

define('ASSETS_PATH', pathJoin(ROOT_PATH, 'assets'));

define('VALUES_PATH', pathJoin(ROOT_PATH, 'values'));

define('CONFIG', require_once pathJoin(ROOT_PATH, 'manifest.php'));

require_once pathJoin(APP_PATH, 'bootstrap.php');

require_once pathJoin(LIB_PATH, 'http-errors.php');

require_once pathJoin(LIB_PATH, 'db.php');

require_once pathJoin(LIB_PATH, 'route.php');

require_once pathJoin(ROOT_PATH, 'routes.php');

Route::handle(function(Route|array $res) {
    $parseHeaders = function(Document $document) {
        foreach(CONFIG['head'] as $tagName => $tagInfo) {
            foreach($tagInfo as $info) {
                $view = View::fromName(ucfirst($tagName));
                foreach($info as $ak => $av) $view->{$ak} = $av;
                $document
                ->getBuilder()
                ->getHead()
                ->append($view);
            }
        }
    };
    if(is_object($res)) {
        if($res->hasDocument()) {
            $docFn = $res->getDocument();
            $split = explode('.', $docFn);
            $docName = $split[count($split) - 1];
            require_once pathJoin(SRC_PATH, str_replace('.', DIRECTORY_SEPARATOR, $docFn) . '.php');
            $document = new $docName;
            if(!($document instanceof Document)) throw new Exception('Class ' . $docName . ' is not document instance');
            if($res->hasRunner()) $res->getRunner()->call($document, $res);
            if(!$res->isApi()) {
                $parseHeaders($document);
                foreach($res->getCssFiles() as $file) $document->addCssFiles($file);
                foreach($res->getJsFiles() as $file) $document->addJsFiles($file);
                if($res->hasAction()) $document->{$res->getAction()}();
                echo $document;
            }
            else if($res->hasAction()) $document->{$res->getAction()}($res);
        }
        else if($res->hasRunner()) call_user_func($res->getRunner(), $res);
    }
    else {
        require_once pathJoin(SRC_PATH, 'ErrorDocument.php');
        $document = new ErrorDocument;
        if(isset($res['cb'])) $res['cb']->call($document);
        $document->{$res['action']}();
        $parseHeaders($document);
        echo $document;
    }
});