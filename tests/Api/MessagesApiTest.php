<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Api;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\MessagesApi;
use KirimEmail\Smtp\Exception\ApiException;

class MessagesApiTest extends TestCase
{
    private SmtpClient $mockClient;
    private MessagesApi $messagesApi;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(SmtpClient::class);
        $this->messagesApi = new MessagesApi($this->mockClient);
    }

    public function testSendMessage()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Message sent successfully'
        ];

        $emailData = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Test Email',
            'text' => 'Hello World!'
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message', $emailData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendMessage('example.com', $emailData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Message sent successfully', $result['message']);
    }

    public function testSendMessageWithAttachments()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Message with attachments sent successfully'
        ];

        $emailData = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Email with Attachments',
            'text' => 'Please see attachments'
        ];

        $files = ['/path/to/file1.pdf', '/path/to/file2.jpg'];

        $this->mockClient->expects($this->once())
            ->method('postMultipart')
            ->with('/api/domains/example.com/message', $emailData, $files)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendMessage('example.com', $emailData, $files);

        $this->assertTrue($result['success']);
        $this->assertEquals('Message with attachments sent successfully', $result['message']);
    }

    public function testSendTemplateMessage()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Template message sent successfully',
            'data' => [
                'template_guid' => 'template-guid-123',
                'template_name' => 'Welcome Email'
            ]
        ];

        $templateData = [
            'template_guid' => 'template-guid-123',
            'to' => 'recipient@example.com',
            'variables' => [
                'name' => 'John Doe',
                'product' => 'Test Product'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message/template', $templateData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendTemplateMessage('example.com', $templateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Template message sent successfully', $result['message']);
        $this->assertEquals('template-guid-123', $result['data']['template_guid']);
    }

    public function testSendTemplateMessageWithAttachments()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Template message with attachments sent successfully'
        ];

        $templateData = [
            'template_guid' => 'template-guid-456',
            'to' => 'recipient@example.com'
        ];

        $files = ['/path/to/template-attachment.pdf'];

        $this->mockClient->expects($this->once())
            ->method('postMultipart')
            ->with('/api/domains/example.com/message/template', $templateData, $files)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendTemplateMessage('example.com', $templateData, $files);

        $this->assertTrue($result['success']);
        $this->assertEquals('Template message with attachments sent successfully', $result['message']);
    }

    public function testSendBulkMessage()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Bulk message sent successfully',
            'data' => [
                'recipients_count' => 3,
                'message_id' => 'bulk-message-123'
            ]
        ];

        $bulkData = [
            'from' => 'sender@example.com',
            'to' => ['user1@example.com', 'user2@example.com', 'user3@example.com'],
            'subject' => 'Bulk Email',
            'text' => 'Hello everyone!'
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message', $bulkData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendBulkMessage('example.com', $bulkData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Bulk message sent successfully', $result['message']);
    }

    public function testSendMessageWithAttachmentOptions()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Message with attachment options sent successfully'
        ];

        $emailData = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Secure Document',
            'text' => 'Please find protected document attached'
        ];

        $files = ['/path/to/secret.pdf'];
        $attachmentOptions = [
            'compress' => true,
            'password' => 'secure123',
            'watermark' => [
                'enabled' => true,
                'text' => 'CONFIDENTIAL',
                'position' => 'center'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('postMultipart')
            ->with('/api/domains/example.com/message', $this->callback(function($data) use ($attachmentOptions) {
                return isset($data['attachment_options']) &&
                       json_decode($data['attachment_options'], true) === $attachmentOptions;
            }), $files)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendMessageWithAttachmentOptions('example.com', $emailData, $files, $attachmentOptions);

        $this->assertTrue($result['success']);
        $this->assertEquals('Message with attachment options sent successfully', $result['message']);
    }

    public function testSendMessageWithCustomHeaders()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Message with custom headers sent successfully'
        ];

        $emailData = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Email with Headers',
            'text' => 'Custom headers included',
            'headers' => [
                'X-Campaign-ID' => 'welcome-series',
                'X-Priority' => '1',
                'List-Unsubscribe' => 'mailto:unsubscribe@example.com'
            ],
            'reply_to' => 'support@example.com'
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message', $emailData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendMessage('example.com', $emailData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Message with custom headers sent successfully', $result['message']);
    }

    public function testSendMessageWithFromName()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Message with from_name sent successfully'
        ];

        $emailData = [
            'from' => 'sender@example.com',
            'from_name' => 'Company Name',
            'to' => 'recipient@example.com',
            'subject' => 'Email with From Name',
            'text' => 'This email has a custom from name'
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message', $emailData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendMessage('example.com', $emailData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Message with from_name sent successfully', $result['message']);
    }



    public function testSendMessageValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Missing required field: from');

        $invalidData = [
            'to' => 'recipient@example.com',
            'subject' => 'Test',
            'text' => 'Hello'
        ];

        $this->messagesApi->sendMessage('example.com', $invalidData);
    }

    public function testSendTemplateMessageValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Missing required field: template_guid');

        $invalidData = [
            'to' => 'recipient@example.com',
            'variables' => ['name' => 'John']
        ];

        $this->messagesApi->sendTemplateMessage('example.com', $invalidData);
    }

    public function testSendBulkMessageValidationWithSingleRecipient()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Bulk email requires \'to\' field to be an array of email addresses');

        $invalidData = [
            'from' => 'sender@example.com',
            'to' => 'single@example.com', // Should be array
            'subject' => 'Bulk Email',
            'text' => 'Hello'
        ];

        $this->messagesApi->sendBulkMessage('example.com', $invalidData);
    }

    public function testSendBulkMessageValidationWithTooManyRecipients()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Maximum 1000 recipients allowed per request');

        $recipients = array_fill(0, 1001, 'user@example.com'); // 1001 recipients

        $invalidData = [
            'from' => 'sender@example.com',
            'to' => $recipients,
            'subject' => 'Bulk Email',
            'text' => 'Hello'
        ];

        $this->messagesApi->sendBulkMessage('example.com', $invalidData);
    }

    public function testSendMessageWithInvalidEmail()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid from email address: invalid-email');

        $invalidData = [
            'from' => 'invalid-email',
            'to' => 'recipient@example.com',
            'subject' => 'Test',
            'text' => 'Hello'
        ];

        $this->messagesApi->sendMessage('example.com', $invalidData);
    }

    public function testSendMessageWithInvalidFromName()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid from_name: must be a string');

        $invalidData = [
            'from' => 'sender@example.com',
            'from_name' => 123, // Invalid type
            'to' => 'recipient@example.com',
            'subject' => 'Test',
            'text' => 'Hello'
        ];

        $this->messagesApi->sendMessage('example.com', $invalidData);
    }

    public function testSendMessageThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message', $this->anything())
            ->willThrowException(new ApiException('Sending failed'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Sending failed');

        $this->messagesApi->sendMessage('example.com', [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'subject' => 'Test',
            'text' => 'Hello'
        ]);
    }

    public function testSendTemplateMessageWithMultipleRecipients()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Template message sent to multiple recipients'
        ];

        $templateData = [
            'template_guid' => 'template-guid-multi',
            'to' => ['user1@example.com', 'user2@example.com'],
            'variables' => ['name' => 'Team']
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message/template', $templateData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendTemplateMessage('example.com', $templateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Template message sent to multiple recipients', $result['message']);
    }

    public function testSendTemplateMessageWithFromName()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Template message with from_name sent successfully',
            'data' => [
                'template_guid' => 'template-guid-123',
                'template_name' => 'Welcome Email'
            ]
        ];

        $templateData = [
            'template_guid' => 'template-guid-123',
            'from' => 'sender@example.com',
            'from_name' => 'Company Name',
            'to' => 'recipient@example.com',
            'variables' => [
                'name' => 'John Doe',
                'product' => 'Test Product'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/message/template', $templateData)
            ->willReturn($mockResponse);

        $result = $this->messagesApi->sendTemplateMessage('example.com', $templateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Template message with from_name sent successfully', $result['message']);
        $this->assertEquals('template-guid-123', $result['data']['template_guid']);
    }


}