<?php
declare(strict_types = 1);

namespace Legax\UI\Views\Support;

use Legax\UI\Views\View;

class ArrayAdapter implements Adapter {

    public function __construct(
        private array $items,
        private View|string|null $view = null
    ) {   
    }

    public function getView(int $position, ?View $gap = null) : ?View {
        if(is_null($this->view)) return null;
        if(is_object($this->view)) return $this->view;
        return ViewParser::fromLayout($this->view, ['item' => $this->getItemAt($position)])->getRoot();
    }

    public function getCount() : int {
        return count($this->items);
    }

    public function getItemAt(int $at) {
        return $this->items[$at] ?? null;
    }

}