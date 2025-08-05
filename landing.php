<?php
require_once 'config/config.php';

// Check maintenance mode
if (isset($_ENV['MAINTENANCE_MODE']) && $_ENV['MAINTENANCE_MODE'] === 'true') {
    http_response_code(503);
    include 'maintenance.php';
    exit;
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Secrets Securely - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Share passwords, API keys & sensitive data with military-grade AES-256 encryption. Self-destructing notes that disappear after reading. Trusted by 50,000+ users.">
    <meta name="keywords" content="secure password sharing, encrypted notes, self-destructing messages, send secrets safely">

    <!-- Open Graph -->
    <meta property="og:title" content="Send Secrets Securely - <?php echo APP_NAME; ?>">
    <meta property="og:description" content="Share sensitive data with military-grade encryption. Self-destructing notes trusted by 50,000+ users.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/landing">
    <meta property="og:type" content="website">

    <?php include "./includes/head.php" ?>

    <!-- Structured Data Schema -->
    <?php
    require_once 'includes/schema.php';
    outputPageSchemas('home');
    ?>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --success-color: #059669;
            --warning-color: #d97706;
            --text-gray: #374151;
            --text-light: #6b7280;
            --bg-gray: #f9fafb;
            --border-gray: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--text-gray);
            background: white;
        }

        /* Header */
        .header {
            background: white;
            border-bottom: 1px solid var(--border-gray);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .trust-badges {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        .hero-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        /* CTA Button */
        .cta-button {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            color: var(--primary-dark);
        }

        /* Main Form Section */
        .form-section {
            background: var(--bg-gray);
            padding: 4rem 0;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-gray);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
            color: var(--text-gray);
        }

        .form-subtitle {
            color: var(--text-light);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-gray);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            padding-right: 2.5rem;
        }

        .char-count {
            font-size: 0.875rem;
            color: var(--text-light);
            text-align: right;
            margin-top: 0.25rem;
        }

        .submit-button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .submit-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Success Message */
        .success-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--success-color);
            text-align: center;
            display: none;
        }

        .success-icon {
            width: 4rem;
            height: 4rem;
            background: var(--success-color);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .success-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--success-color);
        }

        .link-container {
            background: var(--bg-gray);
            border: 1px solid var(--border-gray);
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .link-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 0.875rem;
            color: var(--text-gray);
        }

        .copy-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .copy-button:hover {
            background: var(--primary-dark);
        }

        .warning-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #92400e;
        }

        /* Features Section */
        .features {
            padding: 4rem 0;
            background: white;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .features-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--text-gray);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            background: var(--primary-color);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-gray);
        }

        .feature-description {
            color: var(--text-light);
            line-height: 1.6;
        }

        /* Social Proof */
        .social-proof {
            background: var(--bg-gray);
            padding: 3rem 0;
            text-align: center;
        }

        .social-proof-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .social-proof-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text-gray);
        }

        .logos-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            flex-wrap: wrap;
            opacity: 0.6;
        }

        .logo-item {
            font-weight: 600;
            color: var(--text-light);
            font-size: 1.125rem;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-stats {
                gap: 1rem;
            }

            .form-card {
                padding: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .features-title {
                font-size: 2rem;
            }

            .trust-badges {
                display: none;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Error States */
        .error-message {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>


</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="<?php echo APP_URL; ?>" class="logo">
                🔒 <?php echo APP_NAME; ?>
            </a>
            <div class="trust-badges">
                <div class="trust-badge">
                    <span>🛡️</span>
                    <span>AES-256 Encrypted</span>
                </div>
                <div class="trust-badge">
                    <span>🔥</span>
                    <span>Self-Destructing</span>
                </div>
                <div class="trust-badge">
                    <span>✅</span>
                    <span>50,000+ Users</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1>Send Secrets Securely</h1>
            <p class="hero-subtitle">Share passwords, API keys & sensitive data with military-grade encryption. Your message disappears after reading.</p>

            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Trusted Users</div>
                </div>
                <div class="stat">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label">Notes Shared</div>
                </div>
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Free Forever</div>
                </div>
            </div>

            <a href="#create-note" class="cta-button">
                🚀 Share Your First Secret
            </a>
        </div>
    </section>

    <!-- Main Form Section -->
    <section class="form-section" id="create-note">
        <div class="form-container">
            <!-- Create Form -->
            <div class="form-card" id="createForm">
                <h2 class="form-title">Create Secure Note</h2>
                <p class="form-subtitle">Your message will be encrypted and self-destruct after reading</p>

                <form id="noteForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                    <div class="form-group">
                        <label for="noteContent" class="form-label">Your Secret Message</label>
                        <textarea
                            class="form-control form-textarea"
                            id="noteContent"
                            name="content"
                            placeholder="Enter your password, API key, or sensitive information..."
                            maxlength="10000"
                            required></textarea>
                        <div class="char-count">
                            <span id="charCount">0</span>/10,000 characters
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryType" class="form-label">Destroy After</label>
                            <select class="form-control form-select" id="expiryType" name="expiry_type">
                                <option value="view">First View</option>
                                <option value="time">Time Expires</option>
                                <option value="both">View OR Time</option>
                            </select>
                        </div>
                        <div class="form-group" id="timeExpiryGroup" style="display: none;">
                            <label for="expiryTime" class="form-label">Expires In</label>
                            <select class="form-control form-select" id="expiryTime" name="expiry_time">
                                <option value="1">1 hour</option>
                                <option value="24" selected>24 hours</option>
                                <option value="168">7 days</option>
                                <option value="720">30 days</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="maxViews" class="form-label">Max Views</label>
                            <select class="form-control form-select" id="maxViews" name="max_views">
                                <option value="1" selected>1 view</option>
                                <option value="3">3 views</option>
                                <option value="5">5 views</option>
                                <option value="10">10 views</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="passcode" class="form-label">Passcode (Optional)</label>
                            <input
                                type="password"
                                class="form-control"
                                id="passcode"
                                name="passcode"
                                placeholder="Extra security layer">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="emailNotification" name="email_notification" style="margin-right: 0.5rem;">
                            Email me when someone reads this note
                        </label>
                        <div style="margin-top: 0.5rem; display: none;" id="emailGroup">
                            <input
                                type="email"
                                class="form-control"
                                id="notificationEmail"
                                name="notification_email"
                                placeholder="your@email.com">
                        </div>
                    </div>

                    <button type="submit" class="submit-button" id="createBtn">
                        🔒 Create Secure Note
                    </button>

                    <div id="errorMessage" class="error-message" style="display: none;"></div>
                </form>
            </div>

            <!-- Success Card -->
            <div class="success-card" id="successCard">
                <div class="success-icon">✅</div>
                <h3 class="success-title">Secure Note Created!</h3>
                <p>Share this link with your recipient. It will self-destruct after viewing.</p>

                <div class="link-container">
                    <input type="text" class="link-input" id="noteUrl" readonly>
                    <button class="copy-button" id="copyBtn">Copy Link</button>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center; margin: 1.5rem 0;">
                    <button class="copy-button" id="whatsappShare" style="background: #25d366;">
                        WhatsApp
                    </button>
                    <button class="copy-button" id="emailShare" style="background: #ea4335;">
                        Email
                    </button>
                </div>

                <div class="warning-box">
                    ⚠️ Save this link now! Once it's viewed or expires, it cannot be recovered.
                </div>

                <button class="submit-button" onclick="resetForm()" style="margin-top: 1.5rem;">
                    Create Another Note
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-container">
            <h2 class="features-title">Why Choose SecureNotes?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3 class="feature-title">Military-Grade Encryption</h3>
                    <p class="feature-description">Your data is protected with AES-256 encryption - the same standard used by governments and banks worldwide.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔥</div>
                    <h3 class="feature-title">Self-Destructing</h3>
                    <p class="feature-description">Notes automatically disappear after being read or when they expire. No trace left behind.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👁️</div>
                    <h3 class="feature-title">Zero-Log Policy</h3>
                    <p class="feature-description">We don't track, log, or store your sensitive information. Your privacy is our top priority.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof -->
    <section class="social-proof">
        <div class="social-proof-container">
            <h3 class="social-proof-title">Trusted by companies worldwide</h3>
            <div class="logos-grid">
                <div class="logo-item">TechRadar</div>
                <div class="logo-item">Forbes</div>
                <div class="logo-item">CNN</div>
                <div class="logo-item">PCMag</div>
                <div class="logo-item">Tom's Guide</div>
            </div>
        </div>
    </section>

    <script>
        // Character counter
        const noteContent = document.getElementById('noteContent');
        const charCount = document.getElementById('charCount');

        noteContent.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Expiry type handler
        const expiryType = document.getElementById('expiryType');
        const timeExpiryGroup = document.getElementById('timeExpiryGroup');

        expiryType.addEventListener('change', function() {
            if (this.value === 'time' || this.value === 'both') {
                timeExpiryGroup.style.display = 'block';
            } else {
                timeExpiryGroup.style.display = 'none';
            }
        });

        // Email notification handler
        const emailNotification = document.getElementById('emailNotification');
        const emailGroup = document.getElementById('emailGroup');

        emailNotification.addEventListener('change', function() {
            emailGroup.style.display = this.checked ? 'block' : 'none';
        });

        // Form submission
        const noteForm = document.getElementById('noteForm');
        const createBtn = document.getElementById('createBtn');
        const errorMessage = document.getElementById('errorMessage');
        const createForm = document.getElementById('createForm');
        const successCard = document.getElementById('successCard');

        noteForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Show loading state
            createBtn.innerHTML = '<span class="spinner"></span>Creating...';
            createBtn.disabled = true;
            errorMessage.style.display = 'none';

            try {
                const formData = new FormData(noteForm);
                const response = await fetch('<?php echo APP_URL; ?>/api/create.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success
                    document.getElementById('noteUrl').value = result.url;
                    createForm.style.display = 'none';
                    successCard.style.display = 'block';

                    // Scroll to success card
                    successCard.scrollIntoView({
                        behavior: 'smooth'
                    });
                } else {
                    throw new Error(result.error || 'Failed to create note');
                }
            } catch (error) {
                errorMessage.textContent = error.message;
                errorMessage.style.display = 'block';
            } finally {
                createBtn.innerHTML = '🔒 Create Secure Note';
                createBtn.disabled = false;
            }
        });

        // Copy functionality
        const copyBtn = document.getElementById('copyBtn');
        const noteUrl = document.getElementById('noteUrl');

        copyBtn.addEventListener('click', async function() {
            try {
                await navigator.clipboard.writeText(noteUrl.value);
                copyBtn.textContent = 'Copied!';
                copyBtn.style.background = '#059669';
                setTimeout(() => {
                    copyBtn.textContent = 'Copy Link';
                    copyBtn.style.background = '';
                }, 2000);
            } catch (err) {
                // Fallback for older browsers
                noteUrl.select();
                document.execCommand('copy');
                copyBtn.textContent = 'Copied!';
            }
        });

        // Share functionality
        document.getElementById('whatsappShare').addEventListener('click', function() {
            const url = encodeURIComponent(noteUrl.value);
            const text = encodeURIComponent('I\'ve shared a secure note with you: ');
            window.open(`https://wa.me/?text=${text}${url}`, '_blank');
        });

        document.getElementById('emailShare').addEventListener('click', function() {
            const url = encodeURIComponent(noteUrl.value);
            const subject = encodeURIComponent('Secure Note Shared');
            const body = encodeURIComponent(`I've shared a secure note with you. Click this link to view it: ${noteUrl.value}\n\nThis note will self-destruct after reading.`);
            window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
        });

        // Reset form
        function resetForm() {
            createForm.style.display = 'block';
            successCard.style.display = 'none';
            noteForm.reset();
            charCount.textContent = '0';
            timeExpiryGroup.style.display = 'none';
            emailGroup.style.display = 'none';
            errorMessage.style.display = 'none';
            createForm.scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>