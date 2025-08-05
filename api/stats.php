<?php

/**
 * Stats API
 * Provides anonymized statistics about the SecureNotes service
 */

require_once '../config/config.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Rate limiting
$clientIP = getClientIP();
if (isRateLimited($clientIP, 'stats')) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Get time periods for statistics
    $now = new DateTime();
    $last24h = $now->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s');
    $last7d = $now->sub(new DateInterval('P6D'))->format('Y-m-d H:i:s'); // Total 7 days
    $last30d = $now->sub(new DateInterval('P23D'))->format('Y-m-d H:i:s'); // Total 30 days
    $now = new DateTime(); // Reset to current time

    $stats = [];

    // Total notes created (all time)
    $stmt = $db->query("SELECT COUNT(*) as total FROM notes");
    $stats['total_notes_created'] = (int)$stmt->fetch()['total'];

    // Active notes (not destroyed)
    $stmt = $db->query("SELECT COUNT(*) as active FROM notes WHERE is_destroyed = FALSE");
    $stats['active_notes'] = (int)$stmt->fetch()['active'];

    // Destroyed notes
    $stmt = $db->query("SELECT COUNT(*) as destroyed FROM notes WHERE is_destroyed = TRUE");
    $stats['destroyed_notes'] = (int)$stmt->fetch()['destroyed'];

    // Notes created in last 24 hours
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE created_at >= ?");
    $stmt->execute([$last24h]);
    $stats['notes_24h'] = (int)$stmt->fetch()['count'];

    // Notes created in last 7 days
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE created_at >= ?");
    $stmt->execute([$last7d]);
    $stats['notes_7d'] = (int)$stmt->fetch()['count'];

    // Notes created in last 30 days
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE created_at >= ?");
    $stmt->execute([$last30d]);
    $stats['notes_30d'] = (int)$stmt->fetch()['count'];

    // Total access attempts
    $stmt = $db->query("SELECT COUNT(*) as total FROM access_logs");
    $stats['total_access_attempts'] = (int)$stmt->fetch()['total'];

    // Successful accesses
    $stmt = $db->query("SELECT COUNT(*) as successful FROM access_logs WHERE success = TRUE");
    $stats['successful_accesses'] = (int)$stmt->fetch()['successful'];

    // Failed accesses
    $stmt = $db->query("SELECT COUNT(*) as failed FROM access_logs WHERE success = FALSE");
    $stats['failed_accesses'] = (int)$stmt->fetch()['failed'];

    // Access attempts in last 24 hours
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM access_logs WHERE access_time >= ?");
    $stmt->execute([$last24h]);
    $stats['access_attempts_24h'] = (int)$stmt->fetch()['count'];

    // Most popular expiry types
    $stmt = $db->query("
        SELECT expiry_type, COUNT(*) as count 
        FROM notes 
        GROUP BY expiry_type 
        ORDER BY count DESC
    ");
    $expiryTypes = $stmt->fetchAll();
    $stats['expiry_types'] = [];
    foreach ($expiryTypes as $type) {
        $stats['expiry_types'][$type['expiry_type']] = (int)$type['count'];
    }

    // Average note lifetime (for destroyed notes)
    $stmt = $db->query("
        SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, destroyed_at)) as avg_lifetime
        FROM notes 
        WHERE is_destroyed = TRUE AND destroyed_at IS NOT NULL
    ");
    $result = $stmt->fetch();
    $stats['average_note_lifetime_seconds'] = $result['avg_lifetime'] ? (int)$result['avg_lifetime'] : 0;

    // Notes with passcode protection
    $stmt = $db->query("SELECT COUNT(*) as count FROM notes WHERE has_passcode = TRUE");
    $stats['notes_with_passcode'] = (int)$stmt->fetch()['count'];

    // Notes without passcode
    $stmt = $db->query("SELECT COUNT(*) as count FROM notes WHERE has_passcode = FALSE");
    $stats['notes_without_passcode'] = (int)$stmt->fetch()['count'];

    // View count distribution
    $stmt = $db->query("
        SELECT max_views, COUNT(*) as count 
        FROM notes 
        GROUP BY max_views 
        ORDER BY max_views ASC
    ");
    $viewCounts = $stmt->fetchAll();
    $stats['view_count_distribution'] = [];
    foreach ($viewCounts as $vc) {
        $stats['view_count_distribution'][$vc['max_views']] = (int)$vc['count'];
    }

    // Daily creation stats for last 30 days
    $stmt = $db->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM notes 
        WHERE created_at >= ? 
        GROUP BY DATE(created_at) 
        ORDER BY date DESC 
        LIMIT 30
    ");
    $stmt->execute([$last30d]);
    $dailyStats = $stmt->fetchAll();
    $stats['daily_creation_stats'] = [];
    foreach ($dailyStats as $day) {
        $stats['daily_creation_stats'][$day['date']] = (int)$day['count'];
    }

    // Calculate success rate
    if ($stats['total_access_attempts'] > 0) {
        $stats['success_rate'] = round(
            ($stats['successful_accesses'] / $stats['total_access_attempts']) * 100,
            2
        );
    } else {
        $stats['success_rate'] = 0;
    }

    // Calculate destruction rate
    if ($stats['total_notes_created'] > 0) {
        $stats['destruction_rate'] = round(
            ($stats['destroyed_notes'] / $stats['total_notes_created']) * 100,
            2
        );
    } else {
        $stats['destruction_rate'] = 0;
    }

    // Calculate passcode usage rate
    if ($stats['total_notes_created'] > 0) {
        $stats['passcode_usage_rate'] = round(
            ($stats['notes_with_passcode'] / $stats['total_notes_created']) * 100,
            2
        );
    } else {
        $stats['passcode_usage_rate'] = 0;
    }

    // Server information (non-sensitive)
    $stats['server_info'] = [
        'php_version' => PHP_VERSION,
        'app_version' => '1.0.0',
        'uptime_check' => true,
        'encryption_method' => 'AES-256-CBC',
        'max_note_size' => MAX_FILE_SIZE,
        'rate_limits' => [
            'create' => RATE_LIMIT_CREATE,
            'view' => RATE_LIMIT_VIEW,
            'window' => RATE_LIMIT_WINDOW
        ]
    ];

    // Add metadata
    $stats['generated_at'] = $now->format('Y-m-d H:i:s');
    $stats['timezone'] = APP_TIMEZONE;

    // Optional: Add performance metrics
    $stats['performance'] = [
        'response_time_ms' => round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2),
        'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
    ];

    // Health check
    $stats['health_status'] = [
        'database' => 'healthy',
        'encryption' => function_exists('openssl_encrypt') ? 'available' : 'unavailable',
        'email' => !empty(MAIL_HOST) ? 'configured' : 'not_configured',
        'logs_writable' => is_writable(__DIR__ . '/../logs/') ? true : false
    ];

    // Log stats request (for monitoring)
    logError('Stats API accessed', [
        'ip' => $clientIP,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'total_notes' => $stats['total_notes_created'],
        'active_notes' => $stats['active_notes']
    ]);

    // Cache headers
    header('Cache-Control: public, max-age=300'); // Cache for 5 minutes
    header('ETag: "' . md5(json_encode($stats)) . '"');

    // Return statistics
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Log error
    logError('Stats API error: ' . $e->getMessage(), [
        'ip' => $clientIP,
        'stack_trace' => $e->getTraceAsString()
    ]);

    http_response_code(500);
    echo json_encode([
        'error' => 'Unable to generate statistics at this time'
    ]);
}
