<?php
declare(strict_types = 1);

namespace Legax\UI\Views;

class Placeholder extends View {

    public function __toString(): string {
        $res = '';
        for($i = 0, $t = $this->getChildCount(); $i < $t; ++$i) $res .= (string)($this->getChildAt($i));
        return $res;
    }

}