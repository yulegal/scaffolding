<?php
declare(strict_types = 1);

namespace Legax\Core;

use Legax\UI\Views\Support\ViewBuilder;
use Legax\UI\Views\Support\ViewParser;
use Legax\UI\Views\View;

abstract class Document {

    private ViewBuilder $builder;

    private ?View $root = null;

    public final function __construct() {
        $this->builder = new ViewBuilder;
    }

    public function getBuilder() : ViewBuilder {
        return $this->builder;
    }
    
    public function addJsFiles(string ...$files) : void {
        foreach($files as $file) {
            $view = View::fromName('Script');
            $view->type = 'text/javascript';
            $view->src = $file;
            $this
            ->builder
            ->getHead()
            ->append($view);
        }
    }

    public function setTitle(string $titleText) : void {
        $title = $this->builder->getHead()->findViewByName('Title');
        if(!$title) {
            $title = View::fromName('Title');
            $this->builder->getHead()->append($title);
        }
        $title->put($titleText);
    }
    
    public function addCssFiles(string ...$files) : void {
        foreach($files as $file) {
            $view = View::fromName('Link');
            $view->href = $file;
            $view->rel = 'stylesheet';
            $view->type = 'text/css';
            $this
            ->builder
            ->getHead()
            ->append($view);
        }
    }

    public function findViewById(int $id) : ?View {
        if(is_null($this->root)) return null;
        if($this->root->getID() == $id) return $this->root;
        if($this->root->canContain()) return $this->root->findViewById($id);
        return null;
    }

    public function setContentView(View|string $view, array $vars = []) : void {
        if(is_null($this->root)) {
            $this->root = is_object($view) ? $view : ViewParser::fromLayout($view, $vars)->getRoot();
            $this->builder->getBody()->append($this->root);
        }
    }

    public function __toString() : string {
        return (string)($this->builder);
    }

}