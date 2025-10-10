<?php

namespace App\Domain\Contracts;

interface MailerInterface
{
    public function send(string $to, string $subject, string $body): bool;
}
