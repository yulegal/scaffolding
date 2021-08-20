<?php
declare(strict_types = 1);

namespace Legax\UI\Views;

use Exception;
use Legax\UI\Views\Support\Adapter;

class ListView extends View {

    private ?Adapter $adapter = null;

    public function setAdapter(Adapter $adapter) : void {
        $this->adapter = $adapter;
    }

    public function getAdapter() : ?Adapter {
        return $this->adapter;
    }

    public function __toString(): string {
        if(is_null($this->adapter)) throw new Exception('No adapter is set to the ListView');
        $res = View::fromName('UL');
        for($i = 0, $t = $this->adapter->getCount(); $i < $t; ++$i) {
            $li = View::fromName('LI');
            $view = $this->adapter->getView($i, $li);
            if(is_null($view)) continue;
            $li->append($view);
            $res->append($li);
        }
        return (string)$res;
    }

}