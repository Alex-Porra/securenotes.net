<?php
require_once './config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Terms and Conditions</title>
    <meta name="description" content="Terms and conditions for SecureNotes - Your legal agreement for using our secure note sharing service.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - Terms and Conditions">
    <meta property="og:description" content="Terms and conditions for SecureNotes - Your legal agreement for using our secure note sharing service.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/terms/">
    <meta property="og:type" content="website">
    <?php include "./includes/head.php" ?>



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

        .warning-box {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%) !important;
            border-left: 4px solid #dc3545 !important;
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

        .section-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            width: 2rem !important;
            height: 2rem !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: bold !important;
            font-size: 0.9rem !important;
            margin-right: 0.5rem !important;
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
                            <i class="bi bi-file-text display-4 text-primary mb-3"></i>
                            <h1 class="h2 fw-bold">Terms and Conditions</h1>
                            <p class="text-muted">Your legal agreement for using <?php echo APP_NAME; ?> service.</p>
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
                                <p class="mb-2"><strong>Use responsibly:</strong> Don't use our service for illegal activities or to harm others.</p>
                                <p class="mb-2"><strong>No warranties:</strong> We provide the service "as is" - use at your own risk.</p>
                                <p class="mb-2"><strong>No liability:</strong> We're not responsible for lost notes or any damages.</p>
                                <p class="mb-0"><strong>Fair use:</strong> Don't abuse our service or try to break it.</p>
                            </div>

                            <h2><span class="section-number">1</span>Acceptance of Terms</h2>
                            <p>By accessing and using <?php echo APP_NAME; ?> ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>

                            <h2><span class="section-number">2</span>Description of Service</h2>
                            <p><?php echo APP_NAME; ?> is a web-based service that allows users to create encrypted, self-destructing notes for secure information sharing. The service includes:</p>
                            <ul>
                                <li>End-to-end encryption of note content</li>
                                <li>Configurable expiration settings (time-based or view-based)</li>
                                <li>Optional passcode protection</li>
                                <li>Email notifications for note access</li>
                                <li>Automatic destruction of notes after viewing or expiration</li>
                            </ul>

                            <h2><span class="section-number">3</span>User Responsibilities</h2>

                            <h3>Acceptable Use</h3>
                            <p>You agree to use <?php echo APP_NAME; ?> only for lawful purposes and in accordance with these Terms. You agree NOT to use the service:</p>
                            <ul>
                                <li>For any unlawful purpose or to solicit others to engage in unlawful activities</li>
                                <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
                                <li>To transmit, or procure the sending of, any advertising or promotional material, or any other form of similar solicitation</li>
                                <li>To transmit any material that is defamatory, offensive, or otherwise objectionable</li>
                                <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
                                <li>To submit false or misleading information</li>
                                <li>To upload or transmit viruses or any other type of malicious code</li>
                                <li>To attempt to damage, disable, overburden, or impair the service</li>
                            </ul>

                            <h3>Security Responsibilities</h3>
                            <ul>
                                <li>You are responsible for maintaining the confidentiality of any URLs generated by the service</li>
                                <li>You must not share note URLs with unintended recipients</li>
                                <li>You should verify the identity of recipients before sharing sensitive information</li>
                                <li>You must use strong passcodes when required for sensitive content</li>
                            </ul>

                            <h2><span class="section-number">4</span>Content and Data</h2>

                            <h3>Your Content</h3>
                            <p>You retain full ownership of any content you create using <?php echo APP_NAME; ?>. However, you grant us a limited, non-exclusive license to:</p>
                            <ul>
                                <li>Store your encrypted content for the duration specified by you</li>
                                <li>Transmit your content to intended recipients</li>
                                <li>Delete your content according to your specified expiration settings</li>
                            </ul>

                            <h3>Content Guidelines</h3>
                            <p>While we cannot read your encrypted content, you agree not to use the service to share:</p>
                            <ul>
                                <li>Illegal content including child exploitation material</li>
                                <li>Content that violates intellectual property rights</li>
                                <li>Malware, viruses, or other malicious code</li>
                                <li>Content intended to harm, harass, or threaten others</li>
                                <li>Spam or unsolicited commercial communications</li>
                            </ul>

                            <div class="warning-box">
                                <h4 class="mb-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Important Limitation</h4>
                                <p class="mb-0">Due to our zero-knowledge encryption, we cannot monitor content for compliance. However, we reserve the right to terminate service for users who violate these terms, even if we cannot see the specific content.</p>
                            </div>

                            <h2><span class="section-number">5</span>Service Availability and Limitations</h2>

                            <h3>No Uptime Guarantee</h3>
                            <p>While we strive to maintain high availability, <?php echo APP_NAME; ?> is provided "as is" without any guarantee of uptime or availability. We may experience downtime for:</p>
                            <ul>
                                <li>Scheduled maintenance</li>
                                <li>Emergency security updates</li>
                                <li>Technical difficulties</li>
                                <li>Infrastructure failures</li>
                            </ul>

                            <h3>Rate Limiting</h3>
                            <p>To ensure fair usage and prevent abuse, we implement rate limiting:</p>
                            <ul>
                                <li>Maximum <?php echo RATE_LIMIT_CREATE; ?> notes created per hour per IP address</li>
                                <li>Maximum <?php echo RATE_LIMIT_VIEW; ?> note views per hour per IP address</li>
                                <li>Additional limits may apply during high usage periods</li>
                            </ul>

                            <h3>Content Size Limits</h3>
                            <ul>
                                <li>Maximum note size: 10,000 characters</li>
                                <li>Maximum expiration time: 30 days</li>
                                <li>Maximum view count: 100 views</li>
                            </ul>

                            <h2><span class="section-number">6</span>Privacy and Data Protection</h2>
                            <p>Your privacy is important to us. Our data handling practices are described in detail in our <a href="/privacy" class="text-decoration-none">Privacy Policy</a>, which forms part of these Terms. Key points include:</p>
                            <ul>
                                <li>We use zero-knowledge encryption - we cannot read your notes</li>
                                <li>Notes are automatically deleted according to your settings</li>
                                <li>We collect minimal personal data</li>
                                <li>We do not sell or share your data with third parties</li>
                            </ul>

                            <h2><span class="section-number">7</span>Intellectual Property</h2>

                            <h3>Our Rights</h3>
                            <p>The <?php echo APP_NAME; ?> service, including its original content, features, and functionality, is and will remain the exclusive property of <?php echo APP_NAME; ?> and its licensors. The service is protected by copyright, trademark, and other laws.</p>

                            <h3>Your Rights</h3>
                            <p>You retain all rights to the content you create using our service. We do not claim ownership of your notes or any intellectual property contained within them.</p>

                            <h2><span class="section-number">8</span>Disclaimers and Limitations of Liability</h2>

                            <div class="warning-box">
                                <h4 class="mb-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Important Legal Notice</h4>
                                <p class="mb-0">Please read this section carefully as it limits our liability and describes your rights.</p>
                            </div>

                            <h3>Service Disclaimer</h3>
                            <p>THE SERVICE IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. <?php echo strtoupper(APP_NAME); ?> EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS, IMPLIED, OR STATUTORY, INCLUDING:</p>
                            <ul>
                                <li>WARRANTIES OF MERCHANTABILITY</li>
                                <li>FITNESS FOR A PARTICULAR PURPOSE</li>
                                <li>NON-INFRINGEMENT</li>
                                <li>SECURITY OR ACCURACY</li>
                                <li>UNINTERRUPTED OR ERROR-FREE OPERATION</li>
                            </ul>

                            <h3>Limitation of Liability</h3>
                            <p>TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, <?php echo strtoupper(APP_NAME); ?> SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING:</p>
                            <ul>
                                <li>Loss of data or content</li>
                                <li>Loss of profits or business opportunities</li>
                                <li>Service interruptions</li>
                                <li>Security breaches</li>
                                <li>Any other damages arising from use of the service</li>
                            </ul>

                            <h3>Maximum Liability</h3>
                            <p>In no event shall our total liability to you for all damages exceed the amount of $100 USD or the equivalent in your local currency.</p>

                            <h2><span class="section-number">9</span>Indemnification</h2>
                            <p>You agree to defend, indemnify, and hold harmless <?php echo APP_NAME; ?> and its officers, directors, employees, and agents from and against any and all claims, damages, obligations, losses, liabilities, costs, or debt, and expenses (including but not limited to attorney's fees) arising from:</p>
                            <ul>
                                <li>Your use of and access to the service</li>
                                <li>Your violation of any term of these Terms</li>
                                <li>Your violation of any third-party right</li>
                                <li>Any content you submit through the service</li>
                            </ul>

                            <h2><span class="section-number">10</span>Termination</h2>

                            <h3>Your Right to Terminate</h3>
                            <p>You may stop using our service at any time. Your notes will continue to exist until they expire or are viewed according to your settings.</p>

                            <h3>Our Right to Terminate</h3>
                            <p>We may terminate or suspend your access immediately, without prior notice, for conduct that we believe:</p>
                            <ul>
                                <li>Violates these Terms</li>
                                <li>Is harmful to other users</li>
                                <li>Is harmful to our service or reputation</li>
                                <li>Is illegal or potentially illegal</li>
                            </ul>

                            <h3>Effect of Termination</h3>
                            <p>Upon termination, your right to use the service will cease immediately. However, notes you've already created will continue to exist until they naturally expire.</p>

                            <h2><span class="section-number">11</span>Governing Law and Jurisdiction</h2>
                            <p>These Terms shall be interpreted and governed by the laws of [Your Jurisdiction], without regard to its conflict of law provisions. Any disputes arising from these Terms shall be subject to the exclusive jurisdiction of the courts in [Your Jurisdiction].</p>

                            <h2><span class="section-number">12</span>Changes to Terms</h2>
                            <p>We reserve the right to modify or replace these Terms at any time. If a revision is material, we will:</p>
                            <ul>
                                <li>Provide at least 30 days notice prior to any new terms taking effect</li>
                                <li>Post the updated terms on this page</li>
                                <li>Update the "Last Updated" date</li>
                                <li>Notify users via email if we have their contact information</li>
                            </ul>

                            <p>Your continued use of the service after any such changes constitutes your acceptance of the new Terms.</p>

                            <h2><span class="section-number">13</span>Severability</h2>
                            <p>If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions will remain in effect. The invalid or unenforceable provision will be replaced with a valid provision that most closely matches the intent of the original.</p>

                            <h2><span class="section-number">14</span>Contact Information</h2>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-envelope text-primary me-2"></i>Questions About These Terms?</h4>
                                <p class="mb-2">If you have any questions about these Terms and Conditions:</p>
                                <p class="mb-2"><strong>Email:</strong> legal@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-2"><strong>Support:</strong> support@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-0"><strong>Website:</strong> <a href="<?php echo APP_URL; ?>" class="text-decoration-none"><?php echo APP_URL; ?></a></p>
                            </div>

                            <p class="text-muted mt-5 pt-4 border-top">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    By using <?php echo APP_NAME; ?>, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions. Thank you for using our service responsibly.
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