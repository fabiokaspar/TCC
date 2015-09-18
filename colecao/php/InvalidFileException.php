<?php

class InvalidFileException extends Exception {
    public function __construct($filename) {
        $message = "File '$filename' is invalid.";
        $code = 0;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "{$this->message}";
    }
}