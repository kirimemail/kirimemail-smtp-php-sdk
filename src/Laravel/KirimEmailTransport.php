<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Laravel;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Log;
use KirimEmail\Smtp\Api\MessagesApi;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class KirimEmailTransport extends Transport
{
    protected string $domain;
    protected MessagesApi $messagesApi;

    public function __construct(
        MessagesApi $messagesApi,
        string $domain
    ) {
        $this->messagesApi = $messagesApi;
        $this->domain = $domain;
    }

    public function send(SentMessage $message, Envelope $envelope = null): SentMessage
    {
        $envelope = $envelope ?? SentMessage::create($message->getOriginalMessage())->getEnvelope();

        $this->doSend($message, $envelope);

        return $message;
    }

    protected function doSend(SentMessage $message, Envelope $envelope): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $headersToBypass = ['from', 'to', 'cc', 'bcc', 'reply-to', 'sender', 'subject', 'content-type'];

        $headers = [];
        foreach ($email->getHeaders()->all() as $name => $header) {
            if (in_array($name, $headersToBypass, true)) {
                continue;
            }
            $headers[$header->getName()] = $header->getBodyAsString();
        }

        $attachments = [];
        if ($email->getAttachments()) {
            foreach ($email->getAttachments() as $attachment) {
                $attachmentHeaders = $attachment->getPreparedHeaders();
                $contentType = $attachmentHeaders->get('Content-Type')->getBody();
                $disposition = $attachmentHeaders->getHeaderBody('Content-Disposition');
                $filename = $attachmentHeaders->getHeaderParameter('Content-Disposition', 'filename');

                $attachments[] = [
                    'content_type' => $contentType,
                    'content' => str_replace("\r\n", '', $attachment->bodyToString()),
                    'filename' => $filename,
                ];
            }
        }

        $from = $envelope->getSender();
        $fromAddress = $from->getAddress();
        $fromName = null;

        if ($from instanceof Address && $from->getName()) {
            $fromName = $from->getName();
        }

        $htmlBody = $email->getHtmlBody();
        $textBody = $email->getTextBody();

        if (!$textBody && $htmlBody) {
            $converter = new HtmlConverter();
            $textBody = $converter->convert($htmlBody);
        }

        $data = [
            'from' => $fromAddress,
            'to' => $this->stringifyAddresses($this->getRecipients($email, $envelope)),
            'subject' => $email->getSubject(),
            'text' => $textBody ?: ' ',
            'html' => $htmlBody ?: '',
        ];

        if ($fromName) {
            $data['from_name'] = $fromName;
        }

        $cc = $this->stringifyAddresses($email->getCc());
        if ($cc) {
            $data['cc'] = $cc;
        }

        $bcc = $this->stringifyAddresses($email->getBcc());
        if ($bcc) {
            $data['bcc'] = $bcc;
        }

        $replyTo = $this->stringifyAddresses($email->getReplyTo());
        if ($replyTo) {
            $data['reply_to'] = $replyTo;
        }

        if ($headers) {
            $data['headers'] = $headers;
        }

        try {
            $result = $this->messagesApi->sendMessage(
                $this->domain,
                $data,
                $attachments
            );

            if (!$result['success']) {
                throw new TransportException($result['message'] ?? 'Failed to send email via KirimEmail');
            }
        } catch (\Throwable $exception) {
            $errorMessage = $exception->getMessage();

            if (method_exists($exception, 'getErrors') && $exception->getErrors()) {
                $errorMessage .= ' Errors: ' . json_encode($exception->getErrors());
            }

            throw new TransportException(
                sprintf('KirimEmail API failed. Reason: %s.', $errorMessage),
                is_int($exception->getCode()) ? $exception->getCode() : 0,
                $exception
            );
        }
    }

    protected function getRecipients(Email $email, Envelope $envelope): array
    {
        return array_filter($envelope->getRecipients(), function (Address $address) use ($email) {
            return in_array($address, array_merge($email->getCc(), $email->getBcc()), true) === false;
        });
    }

    protected function stringifyAddresses(array $addresses): array|string
    {
        if (empty($addresses)) {
            return [];
        }

        return array_map(function ($address) {
            if ($address instanceof Address) {
                return $address->getAddress();
            }
            return (string) $address;
        }, $addresses);
    }
}