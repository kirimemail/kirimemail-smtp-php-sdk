<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Laravel;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use KirimEmail\Smtp\Api\MessagesApi;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class KirimEmailTransport extends AbstractTransport
{
    public function __construct(
        protected MessagesApi $messagesApi,
        protected string $domain,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $headersToBypass = [
            "from",
            "to",
            "cc",
            "bcc",
            "reply-to",
            "sender",
            "subject",
            "content-type",
        ];

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
                $contentType = $attachmentHeaders
                    ->get("Content-Type")
                    ->getBody();
                $filename = $attachmentHeaders->getHeaderParameter(
                    "Content-Disposition",
                    "filename",
                );

                $attachments[] = [
                    "filename" => $filename,
                    "contents" => $attachment->getBody(),
                    "content_type" => $contentType,
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
            "from" => $fromAddress,
            "to" => $this->stringifyAddresses(
                $this->getRecipients($email, $envelope),
            ),
            "subject" => $email->getSubject(),
            "text" => $textBody ?: " ",
            "html" => $htmlBody ?: "",
        ];

        if ($fromName) {
            $data["from_name"] = $fromName;
        }

        $cc = $this->stringifyAddresses($email->getCc());
        if ($cc) {
            $data["cc"] = $cc;
        }

        $bcc = $this->stringifyAddresses($email->getBcc());
        if ($bcc) {
            $data["bcc"] = $bcc;
        }

        $replyTo = $this->stringifyAddresses($email->getReplyTo());
        if ($replyTo) {
            $data["reply_to"] = $replyTo;
        }

        if ($headers) {
            $data["headers"] = $headers;
        }

        try {
            $result = $this->messagesApi->sendMessage(
                $this->domain,
                $data,
                $attachments,
            );

            if (!$result["success"]) {
                throw new TransportException(
                    $result["message"] ?? "Failed to send email via KirimEmail",
                );
            }
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();

            if (
                method_exists($exception, "getErrors") &&
                $exception->getErrors()
            ) {
                $message .= " Errors: " . json_encode($exception->getErrors());
            }

            throw new TransportException(
                sprintf("KirimEmail API failed. Reason: %s.", $message),
                is_int($exception->getCode()) ? $exception->getCode() : 0,
                $exception,
            );
        }
    }

    protected function getRecipients(Email $email, Envelope $envelope): array
    {
        return array_filter($envelope->getRecipients(), function (
            Address $address,
        ) use ($email) {
            return in_array(
                $address,
                array_merge($email->getCc(), $email->getBcc()),
                true,
            ) === false;
        });
    }

    protected function stringifyAddresses(array $addresses): array
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

    public function __toString(): string
    {
        return "kirimemail";
    }
}
