<?php
declare(strict_types = 1);

namespace Legax\UI\Views;

use Exception;

class DynamicView extends View {

    private ?View $currentView = null;

    public function setCurrent(View $current) : void {
        $this->currentView = $current;
    }

    public function current() : ?View {
        return $this->currentView;
    }

    public function __toString(): string {
        if(is_null($this->currentView)) throw new Exception('No current view is set');
        return (string)($this->currentView);
    }

}