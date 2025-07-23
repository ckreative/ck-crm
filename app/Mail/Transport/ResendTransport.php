<?php

namespace App\Mail\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Resend\Client;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class ResendTransport extends AbstractTransport
{
    /**
     * The Resend client instance.
     */
    protected Client $client;

    /**
     * Create a new Resend transport instance.
     */
    public function __construct(
        Client $client,
        ?EventDispatcherInterface $dispatcher = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($dispatcher, $logger);
        
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $payload = $this->getPayload($email, $message->getEnvelope());

        try {
            $response = $this->client->emails->send($payload);
            
            if (!isset($response['id'])) {
                throw new TransportException('Resend API did not return a message ID');
            }
            
            $message->setMessageId($response['id']);
        } catch (\Exception $e) {
            throw new TransportException(
                sprintf('Unable to send email via Resend: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get the payload for the Resend API.
     */
    protected function getPayload(Email $email, Envelope $envelope): array
    {
        $payload = [
            'from' => $this->formatAddress($envelope->getSender() ?: $email->getFrom()[0]),
            'to' => $this->formatAddresses($email->getTo()),
            'subject' => $email->getSubject(),
        ];

        // Add CC recipients
        if ($cc = $email->getCc()) {
            $payload['cc'] = $this->formatAddresses($cc);
        }

        // Add BCC recipients
        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = $this->formatAddresses($bcc);
        }

        // Add Reply-To
        if ($replyTo = $email->getReplyTo()) {
            $payload['reply_to'] = $this->formatAddresses($replyTo);
        }

        // Set email content
        if ($email->getHtmlBody()) {
            $payload['html'] = $email->getHtmlBody();
        }

        if ($email->getTextBody()) {
            $payload['text'] = $email->getTextBody();
        }

        // Add attachments
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'filename' => $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename'),
                'content' => base64_encode($attachment->getBody()),
            ];
        }
        
        if (!empty($attachments)) {
            $payload['attachments'] = $attachments;
        }

        // Add custom headers
        $headers = [];
        foreach ($email->getHeaders()->all() as $header) {
            // Skip standard headers that Resend handles
            if (in_array($header->getName(), ['From', 'To', 'Cc', 'Bcc', 'Subject', 'Reply-To', 'Content-Type'])) {
                continue;
            }
            $headers[$header->getName()] = $header->getBodyAsString();
        }
        
        if (!empty($headers)) {
            $payload['headers'] = $headers;
        }

        return $payload;
    }

    /**
     * Format an address for the Resend API.
     */
    protected function formatAddress(Address $address): string
    {
        $email = $address->getAddress();
        $name = $address->getName();

        return $name ? sprintf('%s <%s>', $name, $email) : $email;
    }

    /**
     * Format multiple addresses for the Resend API.
     */
    protected function formatAddresses(array $addresses): array
    {
        return array_map([$this, 'formatAddress'], $addresses);
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'resend';
    }
}