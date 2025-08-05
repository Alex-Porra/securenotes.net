<?php
/**
 * Email Service for SecureNotes
 * Handles sending email notifications when notes are accessed
 */

class EmailService {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $fromAddress;
    private $fromName;
    
    public function __construct() {
        $this->host = MAIL_HOST;
        $this->port = MAIL_PORT;
        $this->username = MAIL_USERNAME;
        $this->password = MAIL_PASSWORD;
        $this->encryption = MAIL_ENCRYPTION;
        $this->fromAddress = MAIL_FROM_ADDRESS;
        $this->fromName = MAIL_FROM_NAME;
    }
    
    /**
     * Send email notification when a note is accessed
     */
    public function sendAccessNotification($recipientEmail, $noteUuid, $accessDetails = []) {
        try {
            $subject = 'Your secure note has been accessed';
            $htmlBody = $this->generateAccessNotificationHTML($noteUuid, $accessDetails);
            $textBody = $this->generateAccessNotificationText($noteUuid, $accessDetails);
            
            return $this->sendEmail($recipientEmail, $subject, $htmlBody, $textBody);
            
        } catch (Exception $e) {
            logError('Failed to send access notification: ' . $e->getMessage(), [
                'recipient' => $recipientEmail,
                'note_uuid' => $noteUuid
            ]);
            return false;
        }
    }
    
    /**
     * Send email with note link (optional feature)
     */
    public function sendNoteLink($recipientEmail, $noteUrl, $senderMessage = '') {
        try {
            $subject = 'You have received a secure note';
            $htmlBody = $this->generateNoteLinkHTML($noteUrl, $senderMessage);
            $textBody = $this->generateNoteLinkText($noteUrl, $senderMessage);
            
            return $this->sendEmail($recipientEmail, $subject, $htmlBody, $textBody);
            
        } catch (Exception $e) {
            logError('Failed to send note link: ' . $e->getMessage(), [
                'recipient' => $recipientEmail,
                'note_url' => $noteUrl
            ]);
            return false;
        }
    }
    
    /**
     * Send email using PHP's mail function or SMTP
     */
    private function sendEmail($to, $subject, $htmlBody, $textBody = '') {
        // Use PHPMailer if available, otherwise fall back to mail()
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return $this->sendWithPHPMailer($to, $subject, $htmlBody, $textBody);
        } else {
            return $this->sendWithBuiltIn($to, $subject, $htmlBody, $textBody);
        }
    }
    
    /**
     * Send email using PHPMailer (recommended)
     */
    private function sendWithPHPMailer($to, $subject, $htmlBody, $textBody = '') {
        try {
            require_once 'vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->encryption;
            $mail->Port = $this->port;
            
            // Recipients
            $mail->setFrom($this->fromAddress, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromAddress, $this->fromName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            logError('PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email using PHP's built-in mail function
     */
    private function sendWithBuiltIn($to, $subject, $htmlBody, $textBody = '') {
        try {
            $headers = [];
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: ' . $this->fromName . ' <' . $this->fromAddress . '>';
            $headers[] = 'Reply-To: ' . $this->fromAddress;
            $headers[] = 'X-Mailer: SecureNotes';
            
            $success = mail($to, $subject, $htmlBody, implode("\r\n", $headers));
            
            if (!$success) {
                throw new Exception('mail() function returned false');
            }
            
            return true;
            
        } catch (Exception $e) {
            logError('Built-in mail error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate HTML email template for access notification
     */
    private function generateAccessNotificationHTML($noteUuid, $accessDetails) {
        $accessTime = $accessDetails['access_time'] ?? date('Y-m-d H:i:s');
        $ipAddress = $accessDetails['ip_address'] ?? 'Unknown';
        $userAgent = $accessDetails['user_agent'] ?? 'Unknown';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Secure Note Accessed</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px 20px; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
                .alert { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .details { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔓 Secure Note Accessed</h1>
                </div>
                
                <div class="content">
                    <p>Hello,</p>
                    
                    <p>This is a notification that your secure note has been successfully accessed and decrypted.</p>
                    
                    <div class="alert">
                        <strong>⚠️ Important:</strong> The note has been automatically destroyed as per your security settings.
                    </div>
                    
                    <div class="details">
                        <h3>Access Details:</h3>
                        <ul>
                            <li><strong>Access Time:</strong> ' . htmlspecialchars($accessTime) . '</li>
                            <li><strong>IP Address:</strong> ' . htmlspecialchars($ipAddress) . '</li>
                            <li><strong>User Agent:</strong> ' . htmlspecialchars(substr($userAgent, 0, 100)) . '</li>
                            <li><strong>Note ID:</strong> ' . htmlspecialchars(substr($noteUuid, 0, 8)) . '...</li>
                        </ul>
                    </div>
                    
                    <p>If this access was not authorized by you, please take appropriate security measures for any sensitive information that was contained in the note.</p>
                    
                    <p>
                        <a href="' . APP_URL . '" class="button">Create New Secure Note</a>
                    </p>
                </div>
                
                <div class="footer">
                    <p>This email was sent by ' . APP_NAME . ' - Secure note sharing service</p>
                    <p>Visit us at <a href="' . APP_URL . '">' . APP_URL . '</a></p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Generate plain text email for access notification
     */
    private function generateAccessNotificationText($noteUuid, $accessDetails) {
        $accessTime = $accessDetails['access_time'] ?? date('Y-m-d H:i:s');
        $ipAddress = $accessDetails['ip_address'] ?? 'Unknown';
        $userAgent = $accessDetails['user_agent'] ?? 'Unknown';
        
        return "
SECURE NOTE ACCESSED

Hello,

This is a notification that your secure note has been successfully accessed and decrypted.

IMPORTANT: The note has been automatically destroyed as per your security settings.

Access Details:
- Access Time: {$accessTime}
- IP Address: {$ipAddress}
- User Agent: " . substr($userAgent, 0, 100) . "
- Note ID: " . substr($noteUuid, 0, 8) . "...

If this access was not authorized by you, please take appropriate security measures for any sensitive information that was contained in the note.

Create a new secure note at: " . APP_URL . "

---
This email was sent by " . APP_NAME . " - Secure note sharing service
Visit us at " . APP_URL . "
        ";
    }
    
    /**
     * Generate HTML email template for note link sharing
     */
    private function generateNoteLinkHTML($noteUrl, $senderMessage = '') {
        $messageSection = '';
        if (!empty($senderMessage)) {
            $messageSection = '
            <div class="message">
                <h3>Message from Sender:</h3>
                <p style="font-style: italic; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">' . 
                htmlspecialchars($senderMessage) . '</p>
            </div>';
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>You have received a secure note</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px 20px; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
                .alert { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .security-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔒 You have received a secure note</h1>
                </div>
                
                <div class="content">
                    <p>Hello,</p>
                    
                    <p>Someone has sent you a secure, encrypted note using ' . APP_NAME . '.</p>
                    
                    ' . $messageSection . '
                    
                    <div class="alert">
                        <strong>⚠️ Important:</strong> This note will be automatically destroyed after you read it. Make sure to save any important information before it\'s gone forever.
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . htmlspecialchars($noteUrl) . '" class="button">🔓 View Secure Note</a>
                    </div>
                    
                    <div class="security-info">
                        <h3>🛡️ Security Features:</h3>
                        <ul>
                            <li><strong>End-to-end encryption</strong> - Your note is protected with AES-256 encryption</li>
                            <li><strong>Self-destructing</strong> - The note will be permanently deleted after viewing</li>
                            <li><strong>Zero-knowledge</strong> - We cannot read your note, even if we wanted to</li>
                            <li><strong>No tracking</strong> - We don\'t log or monitor note contents</li>
                        </ul>
                    </div>
                    
                    <p><small><strong>Note:</strong> If you didn\'t expect to receive this note, please do not click the link and contact the sender to verify its authenticity.</small></p>
                </div>
                
                <div class="footer">
                    <p>This email was sent by ' . APP_NAME . ' - Secure note sharing service</p>
                    <p>Visit us at <a href="' . APP_URL . '">' . APP_URL . '</a></p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Generate plain text email for note link sharing
     */
    private function generateNoteLinkText($noteUrl, $senderMessage = '') {
        $messageSection = '';
        if (!empty($senderMessage)) {
            $messageSection = "\nMessage from Sender:\n" . $senderMessage . "\n";
        }
        
        return "
YOU HAVE RECEIVED A SECURE NOTE

Hello,

Someone has sent you a secure, encrypted note using " . APP_NAME . ".
{$messageSection}
IMPORTANT: This note will be automatically destroyed after you read it. Make sure to save any important information before it's gone forever.

View your secure note: {$noteUrl}

Security Features:
- End-to-end encryption - Your note is protected with AES-256 encryption
- Self-destructing - The note will be permanently deleted after viewing  
- Zero-knowledge - We cannot read your note, even if we wanted to
- No tracking - We don't log or monitor note contents

Note: If you didn't expect to receive this note, please do not click the link and contact the sender to verify its authenticity.

---
This email was sent by " . APP_NAME . " - Secure note sharing service
Visit us at " . APP_URL . "
        ";
    }
    
    /**
     * Queue email for sending (useful for batch processing)
     */
    public function queueEmail($type, $recipient, $data) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                INSERT INTO email_notifications (note_uuid, recipient_email, email_status, email_type, email_data) 
                VALUES (?, ?, 'pending', ?, ?)
            ");
            
            return $stmt->execute([
                $data['note_uuid'] ?? null,
                $recipient,
                $type,
                json_encode($data)
            ]);
            
        } catch (Exception $e) {
            logError('Failed to queue email: ' . $e->getMessage(), [
                'type' => $type,
                'recipient' => $recipient,
                'data' => $data
            ]);
            return false;
        }
    }
    
    /**
     * Process queued emails (run this from a cron job)
     */
    public function processQueuedEmails($limit = 10) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT * FROM email_notifications 
                WHERE email_status = 'pending' 
                ORDER BY created_at ASC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $emails = $stmt->fetchAll();
            
            $processed = 0;
            
            foreach ($emails as $email) {
                $data = json_decode($email['email_data'], true);
                $success = false;
                
                switch ($email['email_type']) {
                    case 'access_notification':
                        $success = $this->sendAccessNotification(
                            $email['recipient_email'],
                            $email['note_uuid'],
                            $data
                        );
                        break;
                        
                    case 'note_link':
                        $success = $this->sendNoteLink(
                            $email['recipient_email'],
                            $data['note_url'],
                            $data['sender_message'] ?? ''
                        );
                        break;
                }
                
                // Update email status
                $status = $success ? 'sent' : 'failed';
                $stmt = $db->prepare("
                    UPDATE email_notifications 
                    SET email_status = ?, sent_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $email['id']]);
                
                $processed++;
            }
            
            return $processed;
            
        } catch (Exception $e) {
            logError('Failed to process email queue: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Test email configuration
     */
    public function testConfiguration() {
        try {
            $testEmail = 'test@example.com';
            $subject = 'SecureNotes Email Test';
            $body = 'This is a test email to verify email configuration.';
            
            // Don't actually send, just test connection
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $this->host;
                $mail->SMTPAuth = true;
                $mail->Username = $this->username;
                $mail->Password = $this->password;
                $mail->SMTPSecure = $this->encryption;
                $mail->Port = $this->port;
                
                // Test connection without sending
                $mail->smtpConnect();
                $mail->smtpClose();
                
                return ['success' => true, 'message' => 'SMTP connection successful'];
            } else {
                return ['success' => false, 'message' => 'PHPMailer not available, using built-in mail()'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate email address
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Check if email sending is enabled
     */
    public function isEnabled() {
        return !empty($this->host) && !empty($this->username) && !empty($this->password);
    }
}

// Email notification helper functions
function sendNoteAccessNotification($noteUuid, $recipientEmail, $accessDetails = []) {
    if (empty($recipientEmail)) {
        return false;
    }
    
    $emailService = new EmailService();
    
    if (!$emailService->isEnabled()) {
        logError('Email service not configured', ['note_uuid' => $noteUuid]);
        return false;
    }
    
    return $emailService->sendAccessNotification($recipientEmail, $noteUuid, $accessDetails);
}

function queueNoteAccessNotification($noteUuid, $recipientEmail, $accessDetails = []) {
    if (empty($recipientEmail)) {
        return false;
    }
    
    $emailService = new EmailService();
    
    return $emailService->queueEmail('access_notification', $recipientEmail, array_merge([
        'note_uuid' => $noteUuid
    ], $accessDetails));
}
?>