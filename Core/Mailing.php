<?php

declare(strict_types=1);

namespace Core;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

class Mailing implements MailerInterface
{
    protected TransportInterface $transport;

    public function __construct()
    {
        $this->transport = Transport::fromDsn($_ENV['MAILER_DSN']);
    }

    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $this->transport->send($message, $envelope);
    }
}