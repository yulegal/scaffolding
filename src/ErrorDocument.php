<?php

use Legax\Core\Document;

class ErrorDocument extends Document {

    public function error404() : void {
        //manage 404 error
        raise(404);
    }

    public function error405() : void {
        //manage 405 error
        raise(405);
    }

}