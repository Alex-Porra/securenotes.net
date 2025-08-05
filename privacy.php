<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Privacy Policy</title>
    <meta name="description" content="Privacy policy for SecureNotes - Learn how we protect your data and maintain your privacy.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - Privacy Policy">
    <meta property="og:description" content="Privacy policy for SecureNotes - Learn how we protect your data and maintain your privacy.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/privacy/">
    <meta property="og:type" content="website">
    <?php include "./includes/head.php" ?>
    
    <!-- Structured Data Schema -->
    <?php 
    require_once 'includes/schema.php';
    $breadcrumbs = [
        ["name" => "Home", "url" => APP_URL],
        ["name" => "Privacy Policy", "url" => APP_URL . "/privacy"]
    ];
    outputPageSchemas('default', $breadcrumbs);
    ?>

    <!-- Additional CSS overrides for Bootstrap -->
    <style>
        body.custom-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
            min-height: 100vh !important;
        }

        .custom-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .legal-content h2 {
            color: #007bff !important;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .legal-content h3 {
            color: #495057 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.75rem !important;
        }

        .legal-content ul {
            padding-left: 1.5rem !important;
        }

        .legal-content li {
            margin-bottom: 0.5rem !important;
        }

        .highlight-box {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%) !important;
            border-left: 4px solid #007bff !important;
            padding: 1rem !important;
            margin: 1.5rem 0 !important;
            border-radius: 0 8px 8px 0 !important;
        }

        .last-updated {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            padding: 1rem !important;
            margin-bottom: 2rem !important;
        }
    </style>

</head>

<body class="custom-body">
    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>


    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                            <h1 class="h2 fw-bold">Privacy Policy</h1>
                            <p class="text-muted">Your privacy is our priority. Learn how we protect your data.</p>
                        </div>

                        <div class="last-updated">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check text-muted me-2"></i>
                                <strong>Last Updated:</strong>
                                <span class="ms-2">June 3, 2025</span>
                            </div>
                        </div>

                        <div class="legal-content">
                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-info-circle text-primary me-2"></i>TL;DR - The Short Version</h4>
                                <p class="mb-2"><strong>We don't store your notes in readable form.</strong> Everything is encrypted before it reaches our servers.</p>
                                <p class="mb-2"><strong>We can't read your notes</strong> even if we wanted to - they're encrypted with keys we don't have.</p>
                                <p class="mb-0"><strong>Notes self-destruct</strong> after being read or expiring, leaving no trace behind.</p>
                            </div>

                            <h2>1. Information We Collect</h2>

                            <h3>Data You Provide</h3>
                            <ul>
                                <li><strong>Note Content:</strong> Your notes are encrypted client-side before transmission and stored in encrypted form</li>
                                <li><strong>Configuration Settings:</strong> Expiry times, view limits, and passcode settings (passcodes are hashed)</li>
                                <li><strong>Email Addresses:</strong> Only if you choose to receive access notifications (optional)</li>
                            </ul>

                            <h3>Automatically Collected Data</h3>
                            <ul>
                                <li><strong>IP Addresses:</strong> Used for rate limiting and security (not linked to note content)</li>
                                <li><strong>Browser Information:</strong> User agent strings for security and compatibility</li>
                                <li><strong>Access Timestamps:</strong> When notes are created and accessed</li>
                                <li><strong>Technical Data:</strong> Error logs and performance metrics (anonymized)</li>
                            </ul>

                            <h2>2. How We Use Your Information</h2>

                            <h3>Primary Uses</h3>
                            <ul>
                                <li><strong>Service Delivery:</strong> To create, store, and deliver your encrypted notes</li>
                                <li><strong>Security:</strong> Rate limiting, abuse prevention, and fraud detection</li>
                                <li><strong>Notifications:</strong> Sending access alerts if you've opted in</li>
                                <li><strong>Service Improvement:</strong> Analyzing usage patterns to improve performance</li>
                            </ul>

                            <h3>What We DON'T Do</h3>
                            <ul>
                                <li>We don't read, analyze, or process your note content</li>
                                <li>We don't sell, rent, or share your data with third parties</li>
                                <li>We don't use your data for advertising or marketing</li>
                                <li>We don't create user profiles or track you across sessions</li>
                            </ul>

                            <h2>3. Data Security & Encryption</h2>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-lock-fill text-primary me-2"></i>Zero-Knowledge Architecture</h4>
                                <p class="mb-0">We employ a zero-knowledge architecture meaning we cannot access your note content even if legally compelled to do so. Your notes are encrypted with keys that never leave your browser.</p>
                            </div>

                            <h3>Technical Safeguards</h3>
                            <ul>
                                <li><strong>AES-256 Encryption:</strong> Military-grade encryption for all note content</li>
                                <li><strong>Unique Keys:</strong> Each note uses a unique encryption key</li>
                                <li><strong>Secure Transmission:</strong> All data transmitted over HTTPS/TLS</li>
                                <li><strong>Secure Storage:</strong> Encrypted database storage with restricted access</li>
                                <li><strong>Regular Security Audits:</strong> Ongoing security assessments and updates</li>
                            </ul>

                            <h2>4. Data Retention & Deletion</h2>

                            <h3>Automatic Deletion</h3>
                            <ul>
                                <li><strong>Note Content:</strong> Automatically deleted when viewed or expired</li>
                                <li><strong>Access Logs:</strong> Retained for 30 days for security purposes</li>
                                <li><strong>Email Notifications:</strong> Deleted after 30 days</li>
                                <li><strong>Rate Limiting Data:</strong> Cleared every hour</li>
                            </ul>

                            <h3>Manual Deletion</h3>
                            <p>You can request immediate deletion of any data associated with your IP address by contacting us. Note that this may affect security features like rate limiting.</p>

                            <h2>5. Third-Party Services</h2>

                            <h3>Email Delivery</h3>
                            <p>We use email service providers to send access notifications. These providers may process your email address but do not have access to note content.</p>

                            <h3>Infrastructure Providers</h3>
                            <p>Our hosting and database providers may have access to encrypted data but cannot decrypt note content due to our zero-knowledge architecture.</p>

                            <h2>6. International Data Transfers</h2>
                            <p>Your data may be processed and stored in countries different from your residence. We ensure appropriate safeguards are in place for all international transfers, including:</p>
                            <ul>
                                <li>Standard Contractual Clauses (SCCs) with service providers</li>
                                <li>Adequacy decisions where applicable</li>
                                <li>Additional security measures for sensitive data</li>
                            </ul>

                            <h2>7. Your Rights</h2>

                            <h3>Data Subject Rights (GDPR/CCPA)</h3>
                            <ul>
                                <li><strong>Right to Access:</strong> Request information about data we hold</li>
                                <li><strong>Right to Rectification:</strong> Correct inaccurate personal data</li>
                                <li><strong>Right to Erasure:</strong> Request deletion of your data</li>
                                <li><strong>Right to Portability:</strong> Receive your data in a structured format</li>
                                <li><strong>Right to Object:</strong> Object to processing for legitimate interests</li>
                                <li><strong>Right to Restrict:</strong> Limit how we process your data</li>
                            </ul>

                            <h3>Exercising Your Rights</h3>
                            <p>To exercise any of these rights, contact us at <strong>privacy@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></strong>. We'll respond within 30 days.</p>

                            <h2>8. Children's Privacy</h2>
                            <p><?php echo APP_NAME; ?> is not intended for use by children under 13 years of age. We do not knowingly collect personal information from children under 13. If you become aware that a child has provided us with personal information, please contact us immediately.</p>

                            <h2>9. Changes to This Policy</h2>
                            <p>We may update this privacy policy from time to time. When we do, we'll:</p>
                            <ul>
                                <li>Post the updated policy on this page</li>
                                <li>Update the "Last Updated" date</li>
                                <li>Notify users of material changes via email (if we have your email)</li>
                                <li>Provide 30 days notice for significant changes</li>
                            </ul>

                            <h2>10. Contact Information</h2>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-envelope text-primary me-2"></i>Get in Touch</h4>
                                <p class="mb-2"><strong>Privacy Questions:</strong> privacy@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-2"><strong>Security Issues:</strong> security@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-2"><strong>General Support:</strong> support@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-0"><strong>Website:</strong> <a href="<?php echo APP_URL; ?>" class="text-decoration-none"><?php echo APP_URL; ?></a></p>
                            </div>

                            <h2>11. Legal Basis for Processing (GDPR)</h2>
                            <p>We process your personal data based on the following legal grounds:</p>
                            <ul>
                                <li><strong>Legitimate Interests:</strong> Security, fraud prevention, and service improvement</li>
                                <li><strong>Contract Performance:</strong> Providing the note-sharing service you requested</li>
                                <li><strong>Consent:</strong> Email notifications and optional features</li>
                                <li><strong>Legal Obligations:</strong> Compliance with applicable laws and regulations</li>
                            </ul>

                            <p class="text-muted mt-5 pt-4 border-top">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    This privacy policy is designed to be transparent and comprehensive. If you have any questions or concerns about our privacy practices, please don't hesitate to contact us.
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "./includes/footer.php" ?>

</body>

</html>