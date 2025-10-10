<?php

class AlreadyReservedBookException extends \Exception
{
    protected $message = 'O livro já está reservado.';
    protected $code = 400;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
