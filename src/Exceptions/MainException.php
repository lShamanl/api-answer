<?php


namespace lShamanl\ApiAnswer\Exceptions;


use Exception;

class MainException extends Exception
{
    public function __construct($message, $code = 500, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}