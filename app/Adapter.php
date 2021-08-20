<?php
declare(strict_types = 1);

namespace Legax\UI\Views\Support;

use Legax\UI\Views\View;

interface Adapter {

    public function getView(int $position, ?View $gap = null) : ?View;

    public function getCount() : int;

    public function getItemAt(int $at);

}