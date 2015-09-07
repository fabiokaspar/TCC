<?php

class InvalidInfoException extends Exception {
    public function __construct($info) {
        $message = "Info '$info' is invalid.";
        $code = 0;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "{$this->message} -->";
    }
}