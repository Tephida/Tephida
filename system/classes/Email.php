<?php

declare(strict_types=1);

namespace Mozg\classes;

/**
 * Send email
 */
class Email
{
    /**
     * Send email
     * @param string $to
     * @param string $message
     * @param string $subject
     * @return void
     */
    public static function send(string $to, string $subject, string $message): void
    {
        $headers = 'From: ' . strip_tags('noreply@mixchat.ru') . "\r\n";
//                    $headers .= "Reply-To: ". strip_tags('noreply@mixchat.ru') . "\r\n";
        $headers .= "CC: noreply@mixchat.ru\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        mail($to, $subject, $message, $headers);
    }
}