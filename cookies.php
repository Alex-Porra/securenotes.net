<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Cookie Policy</title>
    <meta name="description" content="Cookie policy for SecureNotes - Learn about our minimal cookie usage and privacy practices.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - Cookie Policy">
    <meta property="og:description" content="Cookie policy for SecureNotes - Learn about our minimal cookie usage and privacy practices.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/cookies/">
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

        .cookie-table {
            background: white !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .cookie-table th {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 600 !important;
            border: none !important;
        }

        .cookie-table td {
            border-color: #e9ecef !important;
            vertical-align: middle !important;
        }

        .cookie-category {
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
        }

        .cookie-essential {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
        }

        .cookie-functional {
            background-color: #d4edda !important;
            color: #155724 !important;
        }

        .cookie-analytics {
            background-color: #fff3cd !important;
            color: #856404 !important;
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
                            <i class="bi bi-gear display-4 text-primary mb-3"></i>
                            <h1 class="h2 fw-bold">Cookie Policy</h1>
                            <p class="text-muted">We use minimal cookies to provide a secure and functional service.</p>
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
                                <p class="mb-2"><strong>We use very few cookies</strong> - only what's essential for security and functionality.</p>
                                <p class="mb-2"><strong>No tracking cookies</strong> - We don't track you across websites or build advertising profiles.</p>
                                <p class="mb-0"><strong>Essential only</strong> - Session management, CSRF protection, and rate limiting.</p>
                            </div>

                            <h2>1. What Are Cookies?</h2>
                            <p>Cookies are small text files that are placed on your device when you visit a website. They help websites remember information about your visit, which can make your next visit easier and the site more useful to you.</p>

                            <h2>2. Our Cookie Philosophy</h2>
                            <p><?php echo APP_NAME; ?> follows a privacy-first approach to cookies. We only use cookies that are absolutely necessary for the service to function securely. We do not use:</p>
                            <ul>
                                <li>Advertising cookies</li>
                                <li>Social media tracking cookies</li>
                                <li>Analytics cookies (Google Analytics, etc.)</li>
                                <li>Cross-site tracking cookies</li>
                                <li>Marketing or retargeting cookies</li>
                            </ul>

                            <h2>3. Cookies We Use</h2>

                            <div class="table-responsive mt-4">
                                <table class="table cookie-table">
                                    <thead>
                                        <tr>
                                            <th>Cookie Name</th>
                                            <th>Purpose</th>
                                            <th>Category</th>
                                            <th>Duration</th>
                                            <th>Necessary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>PHPSESSID</code></td>
                                            <td>Maintains your session and enables CSRF protection</td>
                                            <td><span class="cookie-category cookie-essential">Essential</span></td>
                                            <td>Session (deleted when browser closes)</td>
                                            <td><i class="bi bi-check-circle text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td><code>csrf_token</code></td>
                                            <td>Prevents cross-site request forgery attacks</td>
                                            <td><span class="cookie-category cookie-essential">Essential</span></td>
                                            <td>Session (deleted when browser closes)</td>
                                            <td><i class="bi bi-check-circle text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td><code>rate_limit</code></td>
                                            <td>Prevents abuse and ensures fair usage</td>
                                            <td><span class="cookie-category cookie-essential">Essential</span></td>
                                            <td>1 hour</td>
                                            <td><i class="bi bi-check-circle text-success"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h2>4. Cookie Categories</h2>

                            <h3><span class="cookie-category cookie-essential me-2">Essential Cookies</span></h3>
                            <p>These cookies are strictly necessary for the website to function and cannot be switched off. They are usually only set in response to actions made by you which amount to a request for services, such as:</p>
                            <ul>
                                <li>Creating secure notes</li>
                                <li>Protecting against security threats</li>
                                <li>Maintaining your session</li>
                                <li>Preventing abuse of our service</li>
                            </ul>

                            <h3><span class="cookie-category cookie-functional me-2">Functional Cookies</span></h3>
                            <p>We currently do not use any functional cookies. If we introduce features that require them in the future, they will be optional and require your consent.</p>

                            <h3><span class="cookie-category cookie-analytics me-2">Analytics Cookies</span></h3>
                            <p>We do not use analytics cookies. We don't track your behavior, create user profiles, or analyze your usage patterns.</p>

                            <h2>5. Third-Party Cookies</h2>
                            <p><?php echo APP_NAME; ?> does not use third-party cookies. We don't integrate with:</p>
                            <ul>
                                <li>Google Analytics or other analytics services</li>
                                <li>Social media plugins (Facebook, Twitter, etc.)</li>
                                <li>Advertising networks</li>
                                <li>Marketing platforms</li>
                                <li>Customer support chat widgets</li>
                            </ul>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-shield-check text-primary me-2"></i>External Content</h4>
                                <p class="mb-0">We do load Bootstrap CSS and JavaScript from CDNs (jsdelivr.net), but these services do not set cookies through our website. Our Content Security Policy prevents unauthorized third-party scripts from running.</p>
                            </div>

                            <h2>6. Cookie Security</h2>
                            <p>All cookies set by <?php echo APP_NAME; ?> are configured with the highest security standards:</p>
                            <ul>
                                <li><strong>HttpOnly:</strong> Prevents JavaScript access to cookies</li>
                                <li><strong>Secure:</strong> Cookies only sent over HTTPS connections</li>
                                <li><strong>SameSite=Strict:</strong> Prevents cross-site request attacks</li>
                                <li><strong>Short Expiration:</strong> Most cookies expire when you close your browser</li>
                            </ul>

                            <h2>7. Managing Cookies</h2>

                            <h3>Browser Settings</h3>
                            <p>You can control and/or delete cookies as you wish through your browser settings. However, please note that disabling essential cookies may prevent <?php echo APP_NAME; ?> from functioning properly.</p>

                            <h4>Popular Browser Instructions:</h4>
                            <ul>
                                <li><strong>Chrome:</strong> Settings → Privacy and security → Cookies and other site data</li>
                                <li><strong>Firefox:</strong> Options → Privacy & Security → Cookies and Site Data</li>
                                <li><strong>Safari:</strong> Preferences → Privacy → Manage Website Data</li>
                                <li><strong>Edge:</strong> Settings → Cookies and site permissions → Cookies and site data</li>
                            </ul>

                            <h3>What Happens If You Disable Cookies?</h3>
                            <p>If you disable cookies, the following features will not work:</p>
                            <ul>
                                <li>Creating secure notes (CSRF protection required)</li>
                                <li>Session management</li>
                                <li>Rate limiting protection</li>
                                <li>Security features that prevent abuse</li>
                            </ul>

                            <h2>8. Do Not Track</h2>
                            <p><?php echo APP_NAME; ?> respects Do Not Track (DNT) signals. However, since we don't track users anyway, enabling DNT won't change our behavior - we already don't track you!</p>

                            <h2>9. International Compliance</h2>

                            <h3>GDPR (European Union)</h3>
                            <p>Under GDPR, we only use cookies that are strictly necessary for the service to function. No consent is required for essential cookies that are technically necessary.</p>

                            <h3>CCPA (California)</h3>
                            <p>We do not sell personal information to third parties, and our minimal cookie usage does not constitute data selling under CCPA.</p>

                            <h3>ePrivacy Directive</h3>
                            <p>We comply with the EU ePrivacy Directive by only using technically necessary cookies without requiring consent.</p>

                            <h2>10. Future Changes</h2>
                            <p>If we ever decide to use additional cookies, we will:</p>
                            <ul>
                                <li>Update this cookie policy</li>
                                <li>Implement a cookie consent banner if required</li>
                                <li>Provide clear opt-in/opt-out controls</li>
                                <li>Maintain our privacy-first approach</li>
                            </ul>

                            <h2>11. Technical Details</h2>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-code text-primary me-2"></i>For Developers</h4>
                                <p class="mb-2"><strong>Session Configuration:</strong></p>
                                <ul class="mb-0">
                                    <li><code>session.cookie_httponly = 1</code></li>
                                    <li><code>session.cookie_secure = 1</code></li>
                                    <li><code>session.cookie_samesite = 'Strict'</code></li>
                                    <li><code>session.use_strict_mode = 1</code></li>
                                </ul>
                            </div>

                            <h3>Cookie Inspection</h3>
                            <p>You can inspect the cookies we set using your browser's developer tools:</p>
                            <ol>
                                <li>Press F12 to open developer tools</li>
                                <li>Go to the "Application" or "Storage" tab</li>
                                <li>Look under "Cookies" for our domain</li>
                                <li>Verify the security settings and content</li>
                            </ol>

                            <h2>12. Contact Us</h2>

                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-envelope text-primary me-2"></i>Questions About Cookies?</h4>
                                <p class="mb-2">If you have any questions about our cookie policy:</p>
                                <p class="mb-2"><strong>Email:</strong> privacy@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></p>
                                <p class="mb-0"><strong>Website:</strong> <a href="<?php echo APP_URL; ?>" class="text-decoration-none"><?php echo APP_URL; ?></a></p>
                            </div>

                            <h2>13. Transparency Report</h2>
                            <p>We believe in complete transparency. Here's what we commit to:</p>
                            <ul>
                                <li>No hidden cookies or tracking</li>
                                <li>Open-source code available for inspection</li>
                                <li>Regular security audits</li>
                                <li>Clear documentation of all data practices</li>
                            </ul>

                            <p class="text-muted mt-5 pt-4 border-top">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    This cookie policy reflects our commitment to minimal data collection and maximum privacy protection. We only use what's absolutely necessary to keep your data secure.
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