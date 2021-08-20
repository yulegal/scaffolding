<?php
declare(strict_types = 1);

namespace Legax\UI\Views;

class View {

    private const VIEWS = [
        'Div',
        'Span',
        'HyperLink',
        'Paragraph',
        'Bold',
        'Link',
        'Html',
        'Body',
        'Select',
        'Option',
        'UL',
        'LI',
        'OL',
        'Table',
        'TR',
        'TD',
        'TBody',
        'THead',
        'TFoot',
        'Header',
        'Footer',
        'Meta',
        'Script',
        'Style',
        'Figure',
        'Form',
        'Input',
        'Abbr',
        'Address',
        'Bdi',
        'Bdo',
        'Blockquote',
        'Cite',
        'Code',
        'Del',
        'Dfn',
        'EM',
        'Italic',
        'Ins',
        'Kbd',
        'Mark',
        'Meter',
        'Pre',
        'Progress',
        'Quote',
        'Samp',
        'Small',
        'Strong',
        'Sub',
        'Sup',
        'Template',
        'Time',
        'Var',
        'Wbr',
        'Caption',
        'Button',
        'Canvas',
        'BR',
        'Col',
        'ColGroup',
        'Data',
        'DataList',
        'DD',
        'Details',
        'Dialog',
        'DL',
        'DT',
        'Embed',
        'FieldSet',
        'IFrame',
        'FigCaption',
        'Head',
        'HR',
        'Image',
        'Label',
        'Legend',
        'Main',
        'Map',
        'Nav',
        'NoScript',
        'Object',
        'OptGroup',
        'Output',
        'H1',
        'H2',
        'H3',
        'H4',
        'H5',
        'H6',
        'Param',
        'Picture',
        'Section',
        'Source',
        'Audio',
        'Area',
        'Svg',
        'TextArea',
        'Title',
        'Track',
        'Video',
        'Aside',
        'Base',
        'Article',
        'Slot',
        'Acronym',
        'ListView',
        'Placeholder',
        'RecyclerView',
        'DynamicView'
    ];

    private const VIEW_MAPPINGS = [
        'Bold' => 'b',
        'Paragraph' => 'p',
        'HyperLink' => 'a',
        'Italic' => 'i',
        'Quote' => 'q',
        'Image' => 'img'
    ];

    private const SINGLE_VIEWS = [
        'Image',
        'Area',
        'Base',
        'BR',
        'Col',
        'Embed',
        'HR',
        'Image',
        'Input',
        'Link',
        'Meta',
        'Param',
        'Source',
        'Track',
        'Wbr'
    ];

    private const SPECIAL_VIEWS = [
        'ListView',
        'Placeholder',
        'RecyclerView',
        'DynamicView'
    ];

    private array $attributes = [];

    private array $content = [];

    private int $id = 0;

    private function __construct(
        private string $viewName
    ) {}

    public static function fromName(string $viewName, array $attributes = []) : ?self {
        if(!in_array($viewName, self::VIEWS)) return null;
        if(!in_array($viewName, self::SPECIAL_VIEWS)) $res = new self($viewName);
		else {
			$name = __NAMESPACE__ . '\\' . $viewName;
			$res = new $name($viewName);
		}
        foreach($attributes as $k => $v) {
            if(is_string($k)) $res->{$k} = $v;
        }
        return $res;
    }

    public function addClasses(string ...$classes) : self {
        $cls = $this->attributes['class'] ?? '';
        foreach($classes as $class) {
            if($cls) $cls .= ' ';
            $cls .= $class;
        }
        $this->attributes['class'] = $cls;
        return $this;
    }

    public function addStyles(array $styles) : self {
        $stl = $this->attributes['style'] ?? '';
        foreach($styles as $n => $v) {
            if($stl) $stl .= ';';
            $stl .= $n . ':' . $v;
        }
        $this->attributes['style'] = $stl;
        return $this;
    }

    public function setID(int $id) : self {
        $this->id = $id;
        return $this;
    }
    
    public function getID() : int {
        return $this->id;
    }

    public function __set(string $name, string|int|float $value) : void {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name) : string|int|float|null {
        return $this->attributes[$name] ?? null;
    }

    public function asHtml() : ?string {
        if(in_array($this->viewName, self::SPECIAL_VIEWS)) return null;
        return self::VIEW_MAPPINGS[$this->viewName] ?? strtolower($this->viewName);
    }

    public function canContain() : bool {
        return !in_array($this->viewName, self::SINGLE_VIEWS);
    }

    public function getName() : string {
        return $this->viewName;
    }

    public function append(self|int|string $content, bool $encode = false) : self {
        if($this->canContain()) {
            if(is_string($content) && $encode) $content = htmlspecialchars($content);
            $this->content[] = $content;
        }
        return $this;
    }

    public function put(self|int|string $content, bool $encode = false) : self {
        if($this->canContain()) {
            $this->clear();
            if(is_string($content) && $encode) $content = htmlspecialchars($content);
            $this->content[] = $content;
        }
        return $this;
    }

    public function findViewById(int $id) : ?self {
        if($this->canContain()) {
            foreach($this->content as $content) {
                if(!is_object($content)) continue;
                $cid = $content->getID();
                if($cid == $id) return $content;
                if($content->canContain()) {
                    $find = $content->findViewById($id);
                    if($find) return $find;
                }
            }
        }
        return null;
    }

    public function hasAttribute(string $attrName) : bool {
        return isset($this->attributes[$attrName]);
    }

    public function getChildCount() : int {
        return count($this->content);
    }

    public function getChildAt(int $at) : self|int|string|null {
        return $this->content[$at] ?? null;
    }

    public function removeAt(int $at) : void {
        if(isset($this->content[$at])) unset($this->content[$at]);
    }

    public function clear() : void {
        array_clear($this->content);
    }

    public function prepend(self|int|string $content, bool $encode = false) : self {
        if($this->canContain()) {
            if(is_string($content) && $encode) $content = htmlspecialchars($content);
            $this->content = array_merge([$content], $this->content);
        }
        return $this;
    }

    public function findViewByName(string $viewName) : ?self {
        if($this->canContain()) {
            foreach($this->content as $content) {
                if(!is_object($content)) continue;
                $cname = $content->getName();
                if($cname == $viewName) return $content;
                if($content->canContain()) {
                    $find = $content->findViewByName($viewName);
                    if($find) return $find;
                }
            }
        }
        return null;
    }

    public function __toString() : string {
        $tag = $this->asHtml();
        $res = '<' . $tag;
        $attrs = '';
        foreach($this->attributes as $attrName => $attrValue) {
            if($attrs) $attrs .= ' ';
            $attrs .= $attrName . '="' . $attrValue . '"';
        }
        if($attrs) $res .= ' ' . $attrs;
        $res .= '>';
        if($this->canContain()) {
            $res .= implode('', array_map(fn($item) => (string)$item, $this->content));
            $res .= '</' . $tag . '>';
        }
        return $res;
    }

}