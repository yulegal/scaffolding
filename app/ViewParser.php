<?php
declare(strict_types = 1);

namespace Legax\UI\Views\Support;

use Exception;
use Legax\Resources\R;
use Legax\UI\Views\View;
use SimpleXMLElement;

class ViewParser {

    private View $root;

    private View|string|int|null $append = null;

    private const EXPRESSION_PATT = '\{\{(.+)\}\}';

    private const ID_PATT = '^[a-z]+[a-z\d_]+';

    private function __construct(
        private string $layout,
        private array $vars = []
    ) {
        $this->root = $this->parse(simplexml_load_file(pathJoin(LAYOUTS_PATH, str_replace('.', DIRECTORY_SEPARATOR, $layout) . '.xml')));
    }

    private function parse(SimpleXMLElement $elem) : ?View {
        $name = $elem->getName();
        if($name == 'include') {
            $attrs = $elem->attributes();
            if(!isset($attrs['layout'])) throw new Exception('No layout is specified to include');
            return $this->parse(simplexml_load_file(pathJoin(LAYOUTS_PATH, str_replace('.', DIRECTORY_SEPARATOR, (string)$attrs['layout']) . '.xml')));
        }
        $view = View::fromName($name);
        if(!$view) throw new Exception('Non-existing view ' . $name);
        $this->parseAttributes($elem, $view);
        if($view->canContain()) {
            if(isset($this->append)) {
                $append = $this->append;
                $this->append = null;
            }
            foreach($elem->children() as $child) {
                $cl = $this->parse($child, $view);
                if($cl) $view->append($cl);
            }
            if(isset($append)) $view->append($append);
        }
        return $view;
    }

    private function parseAttributes(SimpleXMLElement $elem, View $view) : void {
        $lgxAttrs = $elem->attributes('legax', true);
        $attrs = $elem->attributes();
        $validateValue = function(string $value) : string {
            foreach($this->vars as $n => $v) {
                if(is_string($n)) ${$n} = $v;
            }
            if(str_starts_with($value, '@string/')) $value = R::strings()->{substr($value, strlen('@string/'))};
            else if(preg_match('/' . self::EXPRESSION_PATT . '/', $value, $matches)) {
                $perform = eval('return (string)(' . $matches[1] . ');');
                $value = str_replace($matches[0], $perform, $value);
            }
            return $value;
        };
        if(isset($lgxAttrs['append'])) $this->append = $validateValue((string)$lgxAttrs['append']);
        if(isset($lgxAttrs['prepend'])) $view->prepend($validateValue((string)$lgxAttrs['prepend']));
        if(isset($lgxAttrs['id'])) {
            $value = (string)$lgxAttrs['id'];
            if(!str_starts_with($value, '@id/')) throw new Exception('Invalid id type');
            $value = substr($value, strlen('@id/'));
            if(!preg_match('/' . self::ID_PATT . '/', $value)) throw new Exception('Invalid id type');
            $id = R::id($value)->{$value};
            $view->setID($id);
        }
        foreach($attrs as $n => $v) {
            $value = $validateValue((string)$v);
            $view->{$n} = $v;
        }
    }

    public function getRoot() : ?View {
        return $this->root;
    }

    public static function fromLayout(string $layout, array $vars = []) : ?self {
        $path = pathJoin(LAYOUTS_PATH, str_replace('.', DIRECTORY_SEPARATOR, $layout) . '.xml');
        return file_exists($path) ? new self($layout, $vars) : null;
    }

}