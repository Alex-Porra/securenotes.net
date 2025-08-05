<?php

/**
 * reCAPTCHA Helper Functions
 * 
 * Add this to your existing helper/utility file or create a new file: includes/recaptcha.php
 */

/**
 * Verify reCAPTCHA token with Google's API
 * 
 * @param string $token The reCAPTCHA token from the frontend
 * @param string $action The action name (optional, for v3)
 * @return array Returns verification result with success status and score
 */
function verifyRecaptcha($token, $action = 'create_note')
{
    // Get secret key from environment
    $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
    $scoreThreshold = floatval($_ENV['RECAPTCHA_SCORE_THRESHOLD'] ?? 0.5);

    // Debug mode (set RECAPTCHA_DEBUG=true in .env for detailed logging)
    $debug = ($_ENV['RECAPTCHA_DEBUG'] ?? 'false') === 'true';

    if ($debug) {
        error_log("reCAPTCHA Debug - Secret Key: " . (empty($secretKey) ? "MISSING" : "Present"));
        error_log("reCAPTCHA Debug - Token: " . (empty($token) ? "MISSING" : substr($token, 0, 50) . "..."));
        error_log("reCAPTCHA Debug - Score Threshold: {$scoreThreshold}");
    }

    if (empty($secretKey)) {
        if ($debug) error_log("reCAPTCHA Error: Secret key not configured");
        return [
            'success' => false,
            'error' => 'reCAPTCHA secret key not configured'
        ];
    }

    if (empty($token)) {
        if ($debug) error_log("reCAPTCHA Error: Token is missing");
        return [
            'success' => false,
            'error' => 'reCAPTCHA token is missing'
        ];
    }

    // Validate token format (basic check)
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $token)) {
        if ($debug) error_log("reCAPTCHA Error: Invalid token format");
        return [
            'success' => false,
            'error' => 'Invalid reCAPTCHA token format'
        ];
    }

    // Prepare the request data
    $postData = [
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];

    if ($debug) {
        error_log("reCAPTCHA Debug - Making request to Google API");
        error_log("reCAPTCHA Debug - Remote IP: " . $postData['remoteip']);
    }

    // Make request to Google's verification endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Increased timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SecureNotes/1.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    if ($debug) {
        error_log("reCAPTCHA Debug - HTTP Code: {$httpCode}");
        error_log("reCAPTCHA Debug - cURL Error: " . ($curlError ?: "None"));
        error_log("reCAPTCHA Debug - Response: " . substr($response, 0, 200));
    }

    // Check for cURL errors
    if ($curlError) {
        if ($debug) error_log("reCAPTCHA Error - cURL: {$curlError}");
        return [
            'success' => false,
            'error' => 'Network error: ' . $curlError
        ];
    }

    // Check HTTP status
    if ($httpCode !== 200) {
        if ($debug) error_log("reCAPTCHA Error - HTTP {$httpCode}");
        return [
            'success' => false,
            'error' => 'HTTP error: ' . $httpCode
        ];
    }

    // Parse the response
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        if ($debug) error_log("reCAPTCHA Error - JSON parse error: " . json_last_error_msg());
        return [
            'success' => false,
            'error' => 'Invalid response from reCAPTCHA service'
        ];
    }

    if ($debug) {
        error_log("reCAPTCHA Debug - Parsed result: " . json_encode($result));
    }

    // Check if verification was successful
    if (!isset($result['success']) || !$result['success']) {
        $errorCodes = $result['error-codes'] ?? ['unknown-error'];
        if ($debug) error_log("reCAPTCHA Error - Failed verification: " . implode(', ', $errorCodes));

        // Map error codes to user-friendly messages
        $errorMessages = [
            'missing-input-secret' => 'Configuration error',
            'invalid-input-secret' => 'Configuration error',
            'missing-input-response' => 'Verification token missing',
            'invalid-input-response' => 'Invalid verification token',
            'bad-request' => 'Invalid request',
            'timeout-or-duplicate' => 'Token expired or already used'
        ];

        $userError = 'Security verification failed';
        foreach ($errorCodes as $code) {
            if (isset($errorMessages[$code])) {
                $userError = $errorMessages[$code];
                break;
            }
        }

        return [
            'success' => false,
            'error' => $userError,
            'error_codes' => $errorCodes,
            'debug_error' => 'reCAPTCHA verification failed: ' . implode(', ', $errorCodes)
        ];
    }

    // For reCAPTCHA v3, check the score and action
    $score = floatval($result['score'] ?? 0);
    $resultAction = $result['action'] ?? '';

    if ($debug) {
        error_log("reCAPTCHA Debug - Score: {$score}, Threshold: {$scoreThreshold}");
        error_log("reCAPTCHA Debug - Action: '{$resultAction}', Expected: '{$action}'");
    }

    // Verify the action matches (more lenient check)
    if (!empty($action) && !empty($resultAction) && $resultAction !== $action) {
        if ($debug) error_log("reCAPTCHA Warning - Action mismatch but continuing");
        // Don't fail on action mismatch, just log it
    }

    // Check if score meets threshold
    if ($score < $scoreThreshold) {
        if ($debug) error_log("reCAPTCHA Error - Score too low: {$score} < {$scoreThreshold}");
        return [
            'success' => false,
            'error' => 'Security verification failed. Please try again.',
            'score' => $score,
            'threshold' => $scoreThreshold,
            'debug_error' => 'reCAPTCHA score too low'
        ];
    }

    if ($debug) {
        error_log("reCAPTCHA Success - Score: {$score}, Action: {$resultAction}");
    }

    return [
        'success' => true,
        'score' => $score,
        'action' => $resultAction,
        'challenge_ts' => $result['challenge_ts'] ?? null,
        'hostname' => $result['hostname'] ?? null
    ];
}

/**
 * Simple wrapper function for basic verification
 * 
 * @param string $token The reCAPTCHA token
 * @return bool Returns true if verification passes
 */
function isRecaptchaValid($token)
{
    $result = verifyRecaptcha($token);
    return $result['success'];
}

/**
 * Log reCAPTCHA verification attempts (optional)
 * 
 * @param array $result The verification result
 * @param string $ip The user's IP address
 */
function logRecaptchaAttempt($result, $ip = null)
{
    if (!isset($_ENV['LOG_RECAPTCHA_ATTEMPTS']) || $_ENV['LOG_RECAPTCHA_ATTEMPTS'] !== 'true') {
        return;
    }

    $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $ip,
        'success' => $result['success'],
        'score' => $result['score'] ?? null,
        'error' => $result['error'] ?? null
    ];

    // Log to file or your preferred logging system
    error_log('reCAPTCHA: ' . json_encode($logEntry));
}
