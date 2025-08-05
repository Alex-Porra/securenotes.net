<?php

/**
 * Note Creation API
 * Handles the creation of encrypted secure notes
 */

require_once '../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Rate limiting
$clientIP = getClientIP();
if (isRateLimited($clientIP, 'create')) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // If JSON input is not available, try form data
    if (!$input) {
        $input = $_POST;
    }

    // Check CSRF token
    if (!isset($input['csrf_token'])) {
        throw new Exception('Invalid CSRF token');
    }

    // Validate required fields
    if (empty($input['content'])) {
        throw new Exception('Content is required');
    }

    // Sanitize and validate input
    $content = trim($input['content']);
    $expiryType = sanitizeInput($input['expiry_type'] ?? 'view');
    $expiryTime = (int)($input['expiry_time'] ?? 24);
    $maxViews = (int)($input['max_views'] ?? 1);
    $passcode = $input['passcode'] ?? '';
    $notificationEmail = sanitizeInput($input['notification_email'] ?? '');

    // Validate content length
    if (strlen($content) > 10000) {
        throw new Exception('Content too long (max 10,000 characters)');
    }

    // Validate expiry type
    if (!in_array($expiryType, ['view', 'time', 'both'])) {
        throw new Exception('Invalid expiry type');
    }

    // Validate max views
    if ($maxViews < 1 || $maxViews > 100) {
        throw new Exception('Invalid max views (1-100)');
    }

    // Validate expiry time
    if ($expiryTime < 1 || $expiryTime > 8760) { // Max 1 year
        throw new Exception('Invalid expiry time');
    }

    // Validate email if provided
    if (!empty($notificationEmail) && !filter_var($notificationEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Generate unique UUID
    $uuid = generateUUID();

    // Generate encryption key for this note
    $encryptionKey = bin2hex(random_bytes(32));

    // Encrypt the content
    $encryptedContent = encryptData($content, $encryptionKey);
    if (!$encryptedContent) {
        throw new Exception('Failed to encrypt content');
    }

    // Hash passcode if provided
    $passcodeHash = null;
    $hasPasscode = false;
    if (!empty($passcode)) {
        $passcodeHash = password_hash($passcode, PASSWORD_ARGON2ID);
        $hasPasscode = true;
    }

    // Calculate expiry date
    $expiresAt = null;
    if ($expiryType === 'time' || $expiryType === 'both') {
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryTime * 3600));
    }

    // Get database connection
    $db = Database::getInstance()->getConnection();

    // Insert note into database
    $stmt = $db->prepare("
        INSERT INTO notes (
            uuid, encrypted_content, encryption_key, has_passcode, passcode_hash,
            expiry_type, expires_at, max_views, creator_ip, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $success = $stmt->execute([
        $uuid,
        $encryptedContent,
        $encryptionKey,
        $hasPasscode,
        $passcodeHash,
        $expiryType,
        $expiresAt,
        $maxViews,
        $clientIP,
        $userAgent
    ]);

    if (!$success) {
        throw new Exception('Failed to create note');
    }

    // Send email notification if requested
    if (!empty($notificationEmail)) {
        try {
            $stmt = $db->prepare("INSERT INTO email_notifications (note_uuid, recipient_email) VALUES (?, ?)");
            $stmt->execute([$uuid, $notificationEmail]);
        } catch (Exception $e) {
            // Log email notification error but don't fail the note creation
            logError('Failed to queue email notification: ' . $e->getMessage(), [
                'note_uuid' => $uuid,
                'email' => $notificationEmail
            ]);
        }
    }

    // Log successful creation
    logError('Note created successfully', [
        'uuid' => $uuid,
        'ip' => $clientIP,
        'expiry_type' => $expiryType,
        'has_passcode' => $hasPasscode
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'uuid' => $uuid,
        'url' => APP_URL . '/' . $uuid,
        'expires_at' => $expiresAt,
        'max_views' => $maxViews,
        'has_passcode' => $hasPasscode
    ]);
} catch (Exception $e) {
    // Log error
    logError('Note creation failed: ' . $e->getMessage(), [
        'ip' => $clientIP,
        'input' => $input ?? []
    ]);

    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
