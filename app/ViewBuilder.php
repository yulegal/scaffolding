<?php
declare(strict_types = 1);

namespace Legax\UI\Views\Support;

use Legax\UI\Views\View;

final class ViewBuilder {

    private View $head;

    private View $body;

    private View $html;

    public function __construct() {
        $this->head = View::fromName('Head');
        $this->body = View::fromName('Body');
        $this->html = View::fromName('Html');
    }

    public function getBody() : View {
        return $this->body;
    }

    public function getHtml() : View {
        return $this->html;
    }

    public function getHead() : View {
        return $this->head;
    }

    public function __toString() : string {
        $this->html->append($this->head);
        $this->html->append($this->body);
        return '<!doctype html>' . ((string)$this->html);
    }

}