<?php

/**
 * Daily Report Email
 * Sends automated daily statistics report via email
 * Run via cron: 0 9 * * * /usr/bin/php /path/to/daily_report.php
 */

require_once 'config/config.php';

// Email configuration - adjust these as needed
define('ADMIN_EMAIL', 'alexm.pvt@gmail.com'); // Change to your email
define('FROM_EMAIL', 'admin@securenotes.net'); // Change to your from email
define('FROM_NAME', APP_NAME . ' System Report');



/** 
 * Check if this script was already run today
 */
function wasReportSentToday()
{
    $logFile = __DIR__ . '/../logs/daily_reports.log';
    $today = date('Y-m-d');

    if (!file_exists($logFile)) {
        return false;
    }

    $lastLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($lastLines)) {
        return false;
    }

    $lastLine = end($lastLines);
    return strpos($lastLine, $today) !== false;
}

/**
 * Log that report was sent
 */
function logReportSent($stats)
{
    $logFile = __DIR__ . '/../logs/daily_reports.log';
    $logEntry = date('Y-m-d H:i:s') . " - Daily report sent - Notes: {$stats['total_notes_created']}, Active: {$stats['active_notes']}\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Generate statistics for the report
 */
function generateStats()
{
    try {
        $db = Database::getInstance()->getConnection();

        // Get time periods for statistics
        $now = new DateTime();
        $yesterday = (clone $now)->sub(new DateInterval('P1D'));
        $last7d = (clone $now)->sub(new DateInterval('P7D'));
        $last30d = (clone $now)->sub(new DateInterval('P30D'));

        $yesterdayStr = $yesterday->format('Y-m-d H:i:s');
        $last7dStr = $last7d->format('Y-m-d H:i:s');
        $last30dStr = $last30d->format('Y-m-d H:i:s');

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

        // Notes created yesterday
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE DATE(created_at) = DATE(?)");
        $stmt->execute([$yesterdayStr]);
        $stats['notes_yesterday'] = (int)$stmt->fetch()['count'];

        // Notes created in last 7 days
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE created_at >= ?");
        $stmt->execute([$last7dStr]);
        $stats['notes_7d'] = (int)$stmt->fetch()['count'];

        // Notes created in last 30 days
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE created_at >= ?");
        $stmt->execute([$last30dStr]);
        $stats['notes_30d'] = (int)$stmt->fetch()['count'];

        // Access attempts yesterday
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM access_logs WHERE DATE(access_time) = DATE(?)");
        $stmt->execute([$yesterdayStr]);
        $stats['access_attempts_yesterday'] = (int)$stmt->fetch()['count'];

        // Successful accesses yesterday
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM access_logs WHERE DATE(access_time) = DATE(?) AND success = TRUE");
        $stmt->execute([$yesterdayStr]);
        $stats['successful_accesses_yesterday'] = (int)$stmt->fetch()['count'];

        // Failed accesses yesterday
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM access_logs WHERE DATE(access_time) = DATE(?) AND success = FALSE");
        $stmt->execute([$yesterdayStr]);
        $stats['failed_accesses_yesterday'] = (int)$stmt->fetch()['count'];

        // Notes destroyed yesterday
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE DATE(destroyed_at) = DATE(?)");
        $stmt->execute([$yesterdayStr]);
        $stats['notes_destroyed_yesterday'] = (int)$stmt->fetch()['count'];

        // Most popular expiry types (last 30 days)
        $stmt = $db->prepare("
            SELECT expiry_type, COUNT(*) as count 
            FROM notes 
            WHERE created_at >= ?
            GROUP BY expiry_type 
            ORDER BY count DESC
            LIMIT 5
        ");
        $stmt->execute([$last30dStr]);
        $expiryTypes = $stmt->fetchAll();
        $stats['popular_expiry_types'] = [];
        foreach ($expiryTypes as $type) {
            $stats['popular_expiry_types'][$type['expiry_type']] = (int)$type['count'];
        }

        // Notes with passcode (last 30 days)
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notes WHERE has_passcode = TRUE AND created_at >= ?");
        $stmt->execute([$last30dStr]);
        $stats['notes_with_passcode_30d'] = (int)$stmt->fetch()['count'];

        // Calculate rates
        if ($stats['access_attempts_yesterday'] > 0) {
            $stats['success_rate_yesterday'] = round(
                ($stats['successful_accesses_yesterday'] / $stats['access_attempts_yesterday']) * 100,
                1
            );
        } else {
            $stats['success_rate_yesterday'] = 0;
        }

        if ($stats['total_notes_created'] > 0) {
            $stats['destruction_rate'] = round(
                ($stats['destroyed_notes'] / $stats['total_notes_created']) * 100,
                1
            );
        } else {
            $stats['destruction_rate'] = 0;
        }

        // System health
        $stats['health'] = [
            'database' => 'healthy',
            'encryption' => function_exists('openssl_encrypt') ? 'available' : 'unavailable',
            'email' => !empty(MAIL_HOST) ? 'configured' : 'not_configured',
            'logs_writable' => is_writable(__DIR__ . '/../logs/') ? 'yes' : 'no'
        ];

        $stats['generated_at'] = $now->format('Y-m-d H:i:s');
        $stats['yesterday_date'] = $yesterday->format('Y-m-d');

        return $stats;
    } catch (Exception $e) {
        logError('Daily report stats error: ' . $e->getMessage(), [
            'stack_trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Generate HTML email content
 */
function generateEmailContent($stats)
{
    $yesterdayFormatted = date('F j, Y', strtotime($stats['yesterday_date']));
    $todayFormatted = date('F j, Y');

    // Generate trend indicators
    $notesTrend = '';
    if ($stats['notes_yesterday'] > 0) {
        $weeklyAvg = round($stats['notes_7d'] / 7, 1);
        if ($stats['notes_yesterday'] > $weeklyAvg) {
            $notesTrend = ' 📈 (above weekly average)';
        } elseif ($stats['notes_yesterday'] < $weeklyAvg) {
            $notesTrend = ' 📉 (below weekly average)';
        } else {
            $notesTrend = ' ➡️ (at weekly average)';
        }
    }

    // Popular expiry types section
    $expiryTypesHtml = '';
    if (!empty($stats['popular_expiry_types'])) {
        $expiryTypesHtml = '<h3>📊 Popular Expiry Types (Last 30 Days):</h3><ul>';
        foreach ($stats['popular_expiry_types'] as $type => $count) {
            $expiryTypesHtml .= "<li><strong>{$type}:</strong> {$count} notes</li>";
        }
        $expiryTypesHtml .= '</ul>';
    }

    // System health indicators
    $healthIndicators = [];
    foreach ($stats['health'] as $component => $status) {
        $icon = ($status === 'healthy' || $status === 'available' || $status === 'configured' || $status === 'yes') ? '✅' : '❌';
        $healthIndicators[] = "<li>{$icon} <strong>" . ucfirst(str_replace('_', ' ', $component)) . ":</strong> {$status}</li>";
    }
    $healthHtml = '<ul>' . implode('', $healthIndicators) . '</ul>';

    return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Daily System Report - ' . $todayFormatted . '</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px 20px; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
                .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
                .stat-card { background-color: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #667eea; }
                .stat-number { font-size: 24px; font-weight: bold; color: #667eea; display: block; }
                .stat-label { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
                .alert { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
                .danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
                .security-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .section { margin: 25px 0; }
                h3 { color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 8px; }
                ul { padding-left: 20px; }
                li { margin: 5px 0; }
                @media (max-width: 600px) { .stats-grid { grid-template-columns: 1fr; } }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📊 Daily System Report</h1>
                    <p style="margin: 10px 0 0 0; opacity: 0.9;">' . APP_NAME . ' - ' . $todayFormatted . '</p>
                </div>
                
                <div class="content">
                    <div class="alert">
                        <strong>📅 Yesterday\'s Summary (' . $yesterdayFormatted . '):</strong><br>
                        <strong>' . $stats['notes_yesterday'] . '</strong> new notes created' . $notesTrend . '<br>
                        <strong>' . $stats['access_attempts_yesterday'] . '</strong> access attempts with <strong>' . $stats['success_rate_yesterday'] . '%</strong> success rate
                    </div>

                    <div class="section">
                        <h3>📈 Key Metrics</h3>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <span class="stat-number">' . number_format($stats['total_notes_created']) . '</span>
                                <span class="stat-label">Total Notes Created</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-number">' . number_format($stats['active_notes']) . '</span>
                                <span class="stat-label">Active Notes</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-number">' . number_format($stats['notes_7d']) . '</span>
                                <span class="stat-label">Notes (7 Days)</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-number">' . number_format($stats['notes_30d']) . '</span>
                                <span class="stat-label">Notes (30 Days)</span>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <h3>🎯 Yesterday\'s Activity</h3>
                        <ul>
                            <li><strong>New Notes:</strong> ' . $stats['notes_yesterday'] . '</li>
                            <li><strong>Notes Destroyed:</strong> ' . $stats['notes_destroyed_yesterday'] . '</li>
                            <li><strong>Access Attempts:</strong> ' . $stats['access_attempts_yesterday'] . '</li>
                            <li><strong>Successful Access:</strong> ' . $stats['successful_accesses_yesterday'] . '</li>
                            <li><strong>Failed Access:</strong> ' . $stats['failed_accesses_yesterday'] . '</li>
                        </ul>
                    </div>

                    ' . ($expiryTypesHtml ? '<div class="section">' . $expiryTypesHtml . '</div>' : '') . '

                    <div class="section">
                        <h3>🔒 Security Stats</h3>
                        <ul>
                            <li><strong>Notes with Passcode (30d):</strong> ' . $stats['notes_with_passcode_30d'] . '</li>
                            <li><strong>Overall Destruction Rate:</strong> ' . $stats['destruction_rate'] . '%</li>
                            <li><strong>Total Destroyed Notes:</strong> ' . number_format($stats['destroyed_notes']) . '</li>
                        </ul>
                    </div>

                    <div class="security-info">
                        <h3>🛡️ System Health Check</h3>
                        ' . $healthHtml . '
                    </div>

                    <p style="font-size: 12px; color: #6c757d; margin-top: 30px;">
                        <strong>Report generated:</strong> ' . $stats['generated_at'] . ' (' . APP_TIMEZONE . ')<br>
                        <strong>Server:</strong> PHP ' . PHP_VERSION . ' | ' . APP_NAME . ' v1.0.0
                    </p>
                </div>
                
                <div class="footer">
                    <p>Automated daily report from ' . APP_NAME . '</p>
                    <p>Visit: <a href="' . APP_URL . '">' . APP_URL . '</a></p>
                </div>
            </div>
        </body>
        </html>';
}

/**
 * Send email using the configured mail settings
 */
function sendReportEmail($htmlContent, $stats)
{
    $subject = APP_NAME . ' Daily Report - ' . date('M j, Y') . ' (' . $stats['notes_yesterday'] . ' new notes)';

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    ];

    // Use the same email configuration as your existing system
    if (function_exists('mail')) {
        return mail(ADMIN_EMAIL, $subject, $htmlContent, implode("\r\n", $headers));
    } else {
        // Log that mail function is not available
        logError('Daily report: mail() function not available', [
            'stats' => $stats
        ]);
        return false;
    }
}

// Main execution
try {
    // Check if report was already sent today (prevent duplicate runs)
    if (wasReportSentToday()) {
        echo "Daily report already sent today. Skipping.\n";
        exit(0);
    }

    // Generate statistics
    echo "Generating daily statistics...\n";
    $stats = generateStats();

    if ($stats === false) {
        throw new Exception('Failed to generate statistics');
    }

    // Generate email content
    echo "Generating email content...\n";
    $emailContent = generateEmailContent($stats);

    // Send email
    echo "Sending daily report email to " . ADMIN_EMAIL . "...\n";
    $emailSent = sendReportEmail($emailContent, $stats);

    if ($emailSent) {
        // Log successful send
        logReportSent($stats);
        echo "Daily report sent successfully!\n";

        // Log the event
        logError('Daily report sent successfully', [
            'recipient' => ADMIN_EMAIL,
            'notes_yesterday' => $stats['notes_yesterday'],
            'total_notes' => $stats['total_notes_created'],
            'active_notes' => $stats['active_notes']
        ]);
    } else {
        throw new Exception('Failed to send email');
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";

    // Log error
    logError('Daily report error: ' . $e->getMessage(), [
        'script' => __FILE__,
        'line' => $e->getLine(),
        'stack_trace' => $e->getTraceAsString()
    ]);

    exit(1);
}
