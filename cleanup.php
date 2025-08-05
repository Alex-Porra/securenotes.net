<?php
/**
 * Cleanup Script for SecureNotes
 * This script should be run as a cron job to clean up expired notes
 * Add to crontab: */5 * * * * /usr/bin/php /path/to/securenotes/cleanup.php
 */

require_once 'config/config.php';

// Prevent web access
if (isset($_SERVER['HTTP_HOST'])) {
    http_response_code(403);
    die('Access denied. This script can only be run from command line.');
}

try {
    $db = Database::getInstance()->getConnection();
    
    echo "[" . date('Y-m-d H:i:s') . "] Starting cleanup process...\n";
    
    // 1. Clean up expired notes by time
    $stmt = $db->prepare("
        UPDATE notes 
        SET is_destroyed = TRUE, destroyed_at = NOW() 
        WHERE expires_at IS NOT NULL 
        AND expires_at < NOW() 
        AND is_destroyed = FALSE
    ");
    $stmt->execute();
    $expiredByTime = $stmt->rowCount();
    
    if ($expiredByTime > 0) {
        echo "Expired $expiredByTime notes by time\n";
    }
    
    // 2. Clean up notes that have exceeded max views
    $stmt = $db->prepare("
        UPDATE notes 
        SET is_destroyed = TRUE, destroyed_at = NOW() 
        WHERE current_views >= max_views 
        AND is_destroyed = FALSE
    ");
    $stmt->execute();
    $expiredByViews = $stmt->rowCount();
    
    if ($expiredByViews > 0) {
        echo "Expired $expiredByViews notes by view count\n";
    }
    
    // 3. Clean up old destroyed notes (keep for 7 days for audit purposes)
    $stmt = $db->prepare("
        DELETE FROM notes 
        WHERE is_destroyed = TRUE 
        AND destroyed_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $deletedOld = $stmt->rowCount();
    
    if ($deletedOld > 0) {
        echo "Deleted $deletedOld old destroyed notes\n";
    }
    
    // 4. Clean up old access logs (keep for 30 days)
    $stmt = $db->prepare("
        DELETE FROM access_logs 
        WHERE access_time < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $deletedLogs = $stmt->rowCount();
    
    if ($deletedLogs > 0) {
        echo "Deleted $deletedLogs old access logs\n";
    }
    
    // 5. Clean up old rate limit entries
    $stmt = $db->prepare("
        DELETE FROM rate_limits 
        WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute();
    $deletedRateLimits = $stmt->rowCount();
    
    if ($deletedRateLimits > 0) {
        echo "Deleted $deletedRateLimits old rate limit entries\n";
    }
    
    // 6. Clean up old email notifications
    $stmt = $db->prepare("
        DELETE FROM email_notifications 
        WHERE sent_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $deletedEmails = $stmt->rowCount();
    
    if ($deletedEmails > 0) {
        echo "Deleted $deletedEmails old email notifications\n";
    }
    
    // 7. Get statistics
    $stmt = $db->query("SELECT COUNT(*) as total FROM notes WHERE is_destroyed = FALSE");
    $activeNotes = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM notes WHERE is_destroyed = TRUE");
    $destroyedNotes = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM access_logs WHERE access_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $recentAccess = $stmt->fetch()['total'];
    
    echo "Statistics:\n";
    echo "- Active notes: $activeNotes\n";
    echo "- Destroyed notes: $destroyedNotes\n";
    echo "- Access attempts (24h): $recentAccess\n";
    
    // 8. Optimize tables (optional, can be resource intensive)
    if (isset($argv[1]) && $argv[1] === '--optimize') {
        echo "Optimizing database tables...\n";
        $tables = ['notes', 'access_logs', 'rate_limits', 'email_notifications'];
        
        foreach ($tables as $table) {
            $stmt = $db->query("OPTIMIZE TABLE $table");
            echo "Optimized table: $table\n";
        }
    }
    
    $totalCleaned = $expiredByTime + $expiredByViews + $deletedOld + $deletedLogs + $deletedRateLimits + $deletedEmails;
    
    echo "[" . date('Y-m-d H:i:s') . "] Cleanup completed. Total records processed: $totalCleaned\n";
    
    // Log cleanup statistics
    logError('Cleanup completed', [
        'expired_by_time' => $expiredByTime,
        'expired_by_views' => $expiredByViews,
        'deleted_old' => $deletedOld,
        'deleted_logs' => $deletedLogs,
        'deleted_rate_limits' => $deletedRateLimits,
        'deleted_emails' => $deletedEmails,
        'active_notes' => $activeNotes,
        'destroyed_notes' => $destroyedNotes,
        'recent_access' => $recentAccess
    ]);
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    logError('Cleanup failed: ' . $e->getMessage());
    exit(1);
}

// Health check function
function performHealthCheck() {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Check database connection
        $stmt = $db->query("SELECT 1");
        if (!$stmt) {
            throw new Exception("Database connection failed");
        }
        
        // Check if tables exist
        $tables = ['notes', 'access_logs', 'rate_limits', 'email_notifications'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if (!$stmt->fetch()) {
                throw new Exception("Table $table does not exist");
            }
        }
        
        // Check disk space (if on Unix-like system)
        if (function_exists('disk_free_bytes')) {
            $freeBytes = disk_free_bytes('.');
            $totalBytes = disk_total_space('.');
            $usedPercent = (1 - ($freeBytes / $totalBytes)) * 100;
            
            if ($usedPercent > 90) {
                echo "[WARNING] Disk usage is high: " . number_format($usedPercent, 1) . "%\n";
            }
        }
        
        // Check log file sizes
        $logFiles = ['logs/app.log', 'logs/error.log'];
        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                $size = filesize($logFile);
                if ($size > 100 * 1024 * 1024) { // 100MB
                    echo "[WARNING] Log file $logFile is large: " . formatBytes($size) . "\n";
                }
            }
        }
        
        echo "[INFO] Health check passed\n";
        return true;
        
    } catch (Exception $e) {
        echo "[ERROR] Health check failed: " . $e->getMessage() . "\n";
        return false;
    }
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = floor(log($size, 1024));
    return round($size / pow(1024, $power), $precision) . ' ' . $units[$power];
}

// Run health check if requested
if (isset($argv[1]) && $argv[1] === '--health') {
    performHealthCheck();
}

// Rotate log files if they get too large
function rotateLogs() {
    $logFiles = ['logs/app.log', 'logs/error.log'];
    $maxSize = 50 * 1024 * 1024; // 50MB
    
    foreach ($logFiles as $logFile) {
        if (file_exists($logFile) && filesize($logFile) > $maxSize) {
            $rotatedFile = $logFile . '.' . date('Y-m-d-H-i-s');
            if (rename($logFile, $rotatedFile)) {
                echo "Rotated log file: $logFile -> $rotatedFile\n";
                
                // Compress the rotated file if gzip is available
                if (function_exists('gzencode')) {
                    $data = file_get_contents($rotatedFile);
                    $compressed = gzencode($data, 9);
                    file_put_contents($rotatedFile . '.gz', $compressed);
                    unlink($rotatedFile);
                    echo "Compressed rotated log: $rotatedFile.gz\n";
                }
            }
        }
    }
}

// Rotate logs if requested
if (isset($argv[1]) && $argv[1] === '--rotate-logs') {
    rotateLogs();
}

exit(0);
?>