<?php

/**
 * Note Viewing Page
 * Handles the display and decryption of secure notes
 */

require_once 'config/config.php';

// Get note UUID from URL parameter
$uuid = $_GET['id'] ?? '';

if (empty($uuid)) {
    header('Location: /');
    exit;
}

// Sanitize UUID
$uuid = sanitizeInput($uuid);

// Rate limiting
$clientIP = getClientIP();
if (isRateLimited($clientIP, 'view')) {
    http_response_code(429);
    include '429.php';
    exit;
}

$noteFound = false;
$noteExpired = false;
$noteData = null;
$requiresPasscode = false;
$error = '';
$success = '';

try {
    $db = Database::getInstance()->getConnection();

    // Check if note exists and is not destroyed
    $stmt = $db->prepare("
        SELECT uuid, encrypted_content, encryption_key, has_passcode, passcode_hash,
               expiry_type, expires_at, max_views, current_views, is_destroyed,
               created_at
        FROM notes 
        WHERE uuid = ? AND is_destroyed = FALSE
    ");

    $stmt->execute([$uuid]);
    $noteData = $stmt->fetch();

    if (!$noteData) {
        $error = 'Note not found or has been destroyed.';
    } else {
        $noteFound = true;

        // Check if note has expired by time
        if ($noteData['expires_at'] && strtotime($noteData['expires_at']) < time()) {
            $noteExpired = true;
            $error = 'This note has expired.';

            // Mark as destroyed
            $stmt = $db->prepare("UPDATE notes SET is_destroyed = TRUE, destroyed_at = NOW() WHERE uuid = ?");
            $stmt->execute([$uuid]);
        }
        // Check if note has exceeded max views
        elseif ($noteData['current_views'] >= $noteData['max_views']) {
            $noteExpired = true;
            $error = 'This note has been viewed the maximum number of times.';

            // Mark as destroyed
            $stmt = $db->prepare("UPDATE notes SET is_destroyed = TRUE, destroyed_at = NOW() WHERE uuid = ?");
            $stmt->execute([$uuid]);
        } else {
            $requiresPasscode = $noteData['has_passcode'];
        }
    }
} catch (Exception $e) {
    logError('Error fetching note: ' . $e->getMessage(), ['uuid' => $uuid, 'ip' => $clientIP]);
    $error = 'An error occurred while retrieving the note.';
}

// Handle form submission for passcode or note viewing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $noteFound && !$noteExpired) {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Invalid request');
        }

        $passcode = $_POST['passcode'] ?? '';

        // Verify passcode if required
        if ($requiresPasscode) {
            if (empty($passcode)) {
                throw new Exception('Passcode is required');
            }

            if (!password_verify($passcode, $noteData['passcode_hash'])) {
                // Log failed access attempt
                $stmt = $db->prepare("
                    INSERT INTO access_logs (note_uuid, ip_address, user_agent, success, failure_reason) 
                    VALUES (?, ?, ?, FALSE, 'Invalid passcode')
                ");
                $stmt->execute([$uuid, $clientIP, $_SERVER['HTTP_USER_AGENT'] ?? '']);

                throw new Exception('Invalid passcode');
            }
        }

        // Decrypt the content
        $decryptedContent = decryptData($noteData['encrypted_content'], $noteData['encryption_key']);

        if ($decryptedContent === false) {
            throw new Exception('Failed to decrypt note');
        }

        // Update view count
        $stmt = $db->prepare("
            UPDATE notes 
            SET current_views = current_views + 1, accessed_at = NOW() 
            WHERE uuid = ?
        ");
        $stmt->execute([$uuid]);

        // Check if note should be destroyed after this view
        $newViewCount = $noteData['current_views'] + 1;
        $shouldDestroy = false;

        if ($noteData['expiry_type'] === 'view' || $noteData['expiry_type'] === 'both') {
            if ($newViewCount >= $noteData['max_views']) {
                $shouldDestroy = true;
            }
        }

        if ($shouldDestroy) {
            $stmt = $db->prepare("UPDATE notes SET is_destroyed = TRUE, destroyed_at = NOW() WHERE uuid = ?");
            $stmt->execute([$uuid]);
        }

        // Log successful access
        $stmt = $db->prepare("
            INSERT INTO access_logs (note_uuid, ip_address, user_agent, success) 
            VALUES (?, ?, ?, TRUE)
        ");
        $stmt->execute([$uuid, $clientIP, $_SERVER['HTTP_USER_AGENT'] ?? '']);

        // Send email notification if configured
        try {
            $stmt = $db->prepare("SELECT recipient_email FROM email_notifications WHERE note_uuid = ? AND email_status = 'pending'");
            $stmt->execute([$uuid]);
            $notifications = $stmt->fetchAll();

            foreach ($notifications as $notification) {
                // Send access notification
                $accessDetails = [
                    'access_time' => date('Y-m-d H:i:s'),
                    'ip_address' => $clientIP,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ];

                sendNoteAccessNotification($uuid, $notification['recipient_email'], $accessDetails);

                // Mark notification as sent
                $stmt = $db->prepare("UPDATE email_notifications SET email_status = 'sent', sent_at = NOW() WHERE note_uuid = ? AND recipient_email = ?");
                $stmt->execute([$uuid, $notification['recipient_email']]);
            }
        } catch (Exception $e) {
            logError('Failed to send email notification: ' . $e->getMessage());
        }

        // Set success state
        $success = $decryptedContent;
        $requiresPasscode = false;
    } catch (Exception $e) {
        logError('Error accessing note: ' . $e->getMessage(), ['uuid' => $uuid, 'ip' => $clientIP]);
        $error = $e->getMessage();
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo $noteFound ? 'Secure Note' : 'Note Not Found'; ?></title>
    <meta name="description" content="Privacy policy for SecureNotes - Learn how we protect your data and maintain your privacy.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - <?php echo $noteFound ? 'Secure Note' : 'Note Not Found'; ?>">
    <meta property="og:description" content="Share sensitive information securely with self-destructing encrypted notes.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/privacy/">
    <meta property="og:type" content="website">
    <meta name="robots" content="noindex, nofollow">

    <?php include "includes/head.php" ?>

    <!-- Structured Data Schema -->
    <?php
    require_once 'includes/schema.php';
    outputPageSchemas('default');
    ?>
</head>

<body class="bg-light">
    <!-- Navigation -->
    <?php include "includes/nav.php" ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if ($error): ?>
                    <!-- Error State -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                            </div>
                            <h2 class="h3 mb-3">Note Not Available</h2>
                            <p class="text-muted mb-4"><?php echo htmlspecialchars($error); ?></p>
                            <a href="/" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create New Note
                            </a>
                        </div>
                    </div>

                <?php elseif ($success): ?>
                    <!-- Success State - Show Decrypted Content -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-unlock-fill text-success me-2 fs-4"></i>
                                <h2 class="h4 mb-0">Secure Note Decrypted</h2>
                            </div>

                            <div class="alert alert-warning border-0 mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                                    <div>
                                        <strong>Important:</strong> This note will be destroyed after you close this page or refresh it.
                                        Make sure to copy any information you need.
                                    </div>
                                </div>
                            </div>

                            <div class="note-content-container mb-4">
                                <label class="form-label fw-semibold">Note Content:</label>
                                <div class="note-content p-3 bg-light border rounded" id="noteContent">
                                    <?php echo nl2br(htmlspecialchars($success)); ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-secondary" id="copyBtn">
                                    <i class="bi bi-clipboard me-2"></i>Copy to Clipboard
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i>Print
                                </button>
                                <a href="/" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create New Note
                                </a>
                            </div>

                            <!-- Auto-destroy countdown -->
                            <div class="mt-4 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-fire me-1"></i>
                                    This note has been accessed and will self-destruct when you leave this page.
                                </small>
                            </div>
                        </div>
                    </div>

                <?php elseif ($requiresPasscode): ?>
                    <!-- Passcode Required State -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="text-centder mb-4">
                                <i class="bi bi-key-fill display-4 text-warning mb-3"></i>
                                <h2 class="h3">Passcode Required</h2>
                                <p class="text-muted">This note is protected with a passcode. Please enter it to continue.</p>
                            </div>

                            <form method="POST" id="passcodeForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                                <div class="mb-4">
                                    <label for="passcode" class="form-label">Enter Passcode</label>
                                    <input type="password" class="form-control form-control-lg text-center"
                                        id="passcode" name="passcode" required autocomplete="off"
                                        placeholder="Enter passcode">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-unlock me-2"></i>Unlock Note
                                    </button>
                                    <a href="/" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Home
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Default State - Show Note Info -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="bi bi-envelope-fill display-4 text-primary mb-3"></i>
                                <h2 class="h3">You've Received a Secure Note</h2>
                                <p class="text-muted">This note is encrypted and will be destroyed after viewing.</p>
                            </div>

                            <div class="note-info bg-light p-4 rounded mb-4">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="info-item">
                                            <i class="bi bi-eye text-muted me-2"></i>
                                            <strong>Views:</strong> <?php echo $noteData['current_views']; ?>/<?php echo $noteData['max_views']; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-item">
                                            <i class="bi bi-calendar text-muted me-2"></i>
                                            <strong>Created:</strong> <?php echo date('M j, Y', strtotime($noteData['created_at'])); ?>
                                        </div>
                                    </div>
                                    <?php if ($noteData['expires_at']): ?>
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <i class="bi bi-clock text-muted me-2"></i>
                                                <strong>Expires:</strong> <?php echo date('M j, Y g:i A', strtotime($noteData['expires_at'])); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-sm-6">
                                        <div class="info-item">
                                            <i class="bi bi-shield-lock text-muted me-2"></i>
                                            <strong>Encryption:</strong> AES-256
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning border-0 mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                                    <div>
                                        <strong>Warning:</strong> This note will be permanently destroyed after you view it.
                                        Make sure you're ready to read and save the information.
                                    </div>
                                </div>
                            </div>

                            <form method="POST" id="viewNoteForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-unlock me-2"></i>Decrypt & View Note
                                    </button>
                                    <a href="/" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Home
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Copy Success Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Note content copied to clipboard!
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-focus passcode input
        document.addEventListener('DOMContentLoaded', function() {
            const passcodeInput = document.getElementById('passcode');
            if (passcodeInput) {
                passcodeInput.focus();
            }
        });

        // Copy to clipboard functionality
        const copyBtn = document.getElementById('copyBtn');
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                const noteContent = document.getElementById('noteContent');
                const text = noteContent.textContent || noteContent.innerText;

                navigator.clipboard.writeText(text).then(function() {
                    // Show success toast
                    const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                    toast.show();
                }).catch(function(err) {
                    console.error('Failed to copy text: ', err);
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);

                    const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                    toast.show();
                });
            });
        }

        // Warn before leaving page if note is decrypted
        <?php if ($success): ?>
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = 'This note will be destroyed if you leave this page. Are you sure?';
            });
        <?php endif; ?>

        // Form validation and submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                }
            });
        });
    </script>
</body>

</html>