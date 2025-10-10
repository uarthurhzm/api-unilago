<?php

class SamePasswordException extends \Exception
{
    protected $message = 'A nova senha deve ser diferente da senha atual.';
    protected $code = 400;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
