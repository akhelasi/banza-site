<?php

declare(strict_types=1);

function notification_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

function notification_email_enabled(array $notifications): bool
{
    $recipient = trim((string) ($notifications['recipient_email'] ?? ''));

    return !empty($notifications['enabled']) && filter_var($recipient, FILTER_VALIDATE_EMAIL) !== false;
}

function send_contact_notification(array $notifications, array $message): bool
{
    if (!notification_email_enabled($notifications)) {
        return false;
    }

    $recipient = notification_header_value((string) $notifications['recipient_email']);
    $prefix = notification_header_value((string) ($notifications['subject_prefix'] ?? '[Banza Site]'));
    $from = notification_header_value((string) ($notifications['from_email'] ?? ''));
    $replyTo = notification_header_value((string) ($message['email'] ?? ''));

    $subject = trim($prefix . ' ' . (string) ($message['subject'] ?? 'New contact message'));
    $body = implode(PHP_EOL, [
        'New contact message',
        '',
        'Name: ' . (string) ($message['name'] ?? ''),
        'Email: ' . (string) ($message['email'] ?? ''),
        'Phone: ' . (string) ($message['phone'] ?? ''),
        'Subject: ' . (string) ($message['subject'] ?? ''),
        '',
        (string) ($message['message'] ?? ''),
    ]);

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'From: ' . $from;
    }

    if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $sent = @mail($recipient, $subject, $body, implode("\r\n", $headers));
    if (!$sent) {
        error_log('Contact notification email failed for recipient: ' . $recipient);
    }

    return $sent;
}
