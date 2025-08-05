<?php
require_once 'config/config.php';

$emailService = new EmailService();
$processed = $emailService->processQueuedEmails(10);

echo "Processed $processed emails\n";
