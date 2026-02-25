<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmailService
{
    /**
     * Send an email using settings from database
     */
    public static function send(string $to, string $subject, string $message, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        // Get email settings from database
        $protocol = setting('email_protocol') ?? 'smtp';
        
        // Get from email/name - use settings or fall back to config
        $fromEmail = $fromEmail ?? setting('email_sent_from_address') ?? config('Email')->fromEmail;
        $fromName = $fromName ?? setting('email_sent_from_name') ?? config('Email')->fromName;

        /** @var Email $email */
        $email = service('email');
        
        // Reset email config
        $email->clear();
        
        // Set protocol
        $email->setProtocol($protocol);
        
        // Set from
        $email->setFrom($fromEmail, $fromName);
        
        // Set recipient
        $email->setTo($to);
        
        // Set subject and message
        $email->setSubject($subject);
        $email->setMessage($message);
        
        // Configure SMTP if using SMTP protocol
        if ($protocol === 'smtp') {
            $email->setSMTPHost(setting('email_smtp_host') ?? '');
            $email->setSMTPPort(setting('email_smtp_port') ?? 587);
            $email->setSMTPUser(setting('email_smtp_user') ?? '');
            $email->setSMTPPass(setting('email_smtp_pass') ?? '');
            
            $encryption = setting('email_smtp_security_type') ?? 'tls';
            $email->setSMTPCrypto($encryption);
        }
        
        // Try to send
        try {
            $result = $email->send();
            
            if (!$result) {
                log_message('error', 'Email sending failed: ' . $email->printDebugger());
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send HTML email
     */
    public static function sendHTML(string $to, string $subject, string $htmlBody, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        /** @var Email $email */
        $email = service('email');
        $email->clear();
        
        $protocol = setting('email_protocol') ?? 'smtp';
        $email->setProtocol($protocol);
        
        $fromEmail = $fromEmail ?? setting('email_sent_from_address') ?? config('Email')->fromEmail;
        $fromName = $fromName ?? setting('email_sent_from_name') ?? config('Email')->fromName;
        
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMailType('html');
        $email->setMessage($htmlBody);
        
        if ($protocol === 'smtp') {
            $email->setSMTPHost(setting('email_smtp_host') ?? '');
            $email->setSMTPPort(setting('email_smtp_port') ?? 587);
            $email->setSMTPUser(setting('email_smtp_user') ?? '');
            $email->setSMTPPass(setting('email_smtp_pass') ?? '');
            
            $encryption = setting('email_smtp_security_type') ?? 'tls';
            $email->setSMTPCrypto($encryption);
        }
        
        try {
            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordReset(string $to, string $resetUrl): bool
    {
        $subject = 'Password Reset Request';
        
        $htmlBody = '
        <html>
        <body style="font-family: Arial, sans-serif; padding: 20px;">
            <h2>Password Reset Request</h2>
            <p>You requested a password reset. Click the link below to reset your password:</p>
            <p><a href="' . $resetUrl . '" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Reset Password</a></p>
            <p>Or copy and paste this link: ' . $resetUrl . '</p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this, please ignore this email.</p>
        </body>
        </html>
        ';
        
        return self::sendHTML($to, $subject, $htmlBody);
    }

    /**
     * Test email connection
     */
    public static function testConnection(): array
    {
        $protocol = setting('email_protocol') ?? 'smtp';
        
        if ($protocol !== 'smtp') {
            return ['success' => false, 'message' => 'Only SMTP protocol is supported for testing'];
        }
        
        $host = setting('email_smtp_host') ?? '';
        $port = setting('email_smtp_port') ?? 587;
        $user = setting('email_smtp_user') ?? '';
        $pass = setting('email_smtp_pass') ?? '';
        
        if (empty($host) || empty($user)) {
            return ['success' => false, 'message' => 'SMTP settings are not configured'];
        }
        
        /** @var Email $email */
        $email = service('email');
        $email->clear();
        $email->setProtocol('smtp');
        $email->setSMTPHost($host);
        $email->setSMTPPort($port);
        $email->setSMTPUser($user);
        $email->setSMTPPass($pass);
        
        try {
            // Try to connect to SMTP server
            $result = @fsockopen($host, $port, $errno, $errstr, 5);
            
            if ($result) {
                fclose($result);
                return ['success' => true, 'message' => 'Connection successful'];
            } else {
                return ['success' => false, 'message' => 'Could not connect to SMTP server: ' . $errstr];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }
}
