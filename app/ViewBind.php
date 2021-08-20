<?php
declare(strict_types = 1);

namespace Legax\UI\Views\Support;

use Legax\Core\Document;
use Legax\Resources\R;

final class ViewBind {

    private function __construct(
        private Document $document
    ) {}

    public static function from(Document $document) : self {
        return new self($document);
    }

    public function __get(string $name) {
        return $this->document->findViewById(R::id()->{$name});
    }

}