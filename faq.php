<?php
require_once 'config/config.php';

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Frequently Asked Questions</title>
    <meta name="description" content="Get answers to frequently asked questions about SecureNotes - secure note sharing, encryption, privacy, and how our service works.">
    <meta name="keywords" content="secure notes faq, encrypted sharing questions, self-destructing messages help, securenotes support">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - Frequently Asked Questions">
    <meta property="og:description" content="Get answers to frequently asked questions about SecureNotes - secure note sharing and encryption.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/faq">
    <meta property="og:type" content="website">

    <?php include "./includes/head.php" ?>

    <!-- Structured Data Schema -->
    <?php
    require_once 'includes/schema.php';
    $breadcrumbs = [
        ["name" => "Home", "url" => APP_URL],
        ["name" => "FAQ", "url" => APP_URL . "/faq"]
    ];
    outputPageSchemas('faq', $breadcrumbs);
    ?>

    <style>
        .faq-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .faq-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .faq-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            padding: 1.25rem;
            font-weight: 600;
            display: flex;
            justify-content: between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-header:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .faq-header .icon {
            transition: transform 0.3s ease;
            margin-left: auto;
        }

        .faq-header[aria-expanded="true"] .icon {
            transform: rotate(180deg);
        }

        .faq-body {
            padding: 1.5rem;
            background: white;
            border-top: 3px solid #667eea;
        }

        .faq-category {
            background: rgba(102, 126, 234, 0.1);
            border-left: 4px solid #667eea;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 8px 8px 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #667eea;
        }

        .search-box {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-box input {
            border-radius: 50px;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .search-box .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
    </style>
</head>

<body class="faq-section">
    <!-- Navigation -->
    <?php include "includes/nav.php" ?>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">FAQ</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">Frequently Asked Questions</h1>
            <p class="lead text-muted">Get answers to common questions about <?php echo APP_NAME; ?></p>
        </div>

        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6">
                <div class="search-box">
                    <input type="text" class="form-control" id="faqSearch" placeholder="Search FAQs...">
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">

                <!-- General Questions -->
                <div class="faq-category">
                    <h3 class="h5 mb-0"><i class="bi bi-info-circle me-2"></i>General Questions</h3>
                </div>

                <div class="accordion" id="generalAccordion">
                    <!-- FAQ 1 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false">
                            <span>What is <?php echo APP_NAME; ?> and how does it work?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq1" class="collapse" data-bs-parent="#generalAccordion">
                            <div class="faq-body">
                                <p><?php echo APP_NAME; ?> is a secure note-sharing service that allows you to share sensitive information through encrypted, self-destructing notes. Here's how it works:</p>
                                <ol>
                                    <li><strong>Create:</strong> Write your sensitive message and set expiry options</li>
                                    <li><strong>Encrypt:</strong> Your message is automatically encrypted with military-grade AES-256 encryption</li>
                                    <li><strong>Share:</strong> Get a unique, one-time link to share with your recipient</li>
                                    <li><strong>Self-Destruct:</strong> The note automatically destroys itself after being read or when it expires</li>
                                </ol>
                                <p>This ensures your sensitive information remains private and secure, with no permanent storage of your data.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                            <span>Is <?php echo APP_NAME; ?> really secure?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq2" class="collapse" data-bs-parent="#generalAccordion">
                            <div class="faq-body">
                                <p>Yes, <?php echo APP_NAME; ?> implements enterprise-grade security measures:</p>
                                <ul>
                                    <li><strong>AES-256 Encryption:</strong> The same encryption standard used by governments and financial institutions</li>
                                    <li><strong>Zero-Log Policy:</strong> We don't log, track, or store your note contents</li>
                                    <li><strong>Self-Destruction:</strong> Notes are permanently deleted after viewing or expiration</li>
                                    <li><strong>HTTPS Only:</strong> All communications are encrypted in transit</li>
                                    <li><strong>No Tracking:</strong> We don't use cookies, analytics, or tracking mechanisms</li>
                                    <li><strong>Rate Limiting:</strong> Protection against abuse and spam attempts</li>
                                </ul>
                                <p>Your privacy and security are our top priorities.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                            <span>What types of information can I share?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq3" class="collapse" data-bs-parent="#generalAccordion">
                            <div class="faq-body">
                                <p><?php echo APP_NAME; ?> is perfect for sharing any sensitive information, including:</p>
                                <ul>
                                    <li>Passwords and login credentials</li>
                                    <li>API keys and tokens</li>
                                    <li>Credit card numbers and financial information</li>
                                    <li>Personal identification numbers (SSN, etc.)</li>
                                    <li>Confidential business information</li>
                                    <li>Personal messages that need to remain private</li>
                                    <li>Recovery codes and backup phrases</li>
                                </ul>
                                <p><strong>Maximum limit:</strong> 10,000 characters per note. Please don't share illegal content or use the service for harmful purposes.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Privacy -->
                <div class="faq-category mt-5">
                    <h3 class="h5 mb-0"><i class="bi bi-shield-lock me-2"></i>Security & Privacy</h3>
                </div>

                <div class="accordion" id="securityAccordion">
                    <!-- FAQ 4 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                            <span>Can you recover my note if I lose the link?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq4" class="collapse" data-bs-parent="#securityAccordion">
                            <div class="faq-body">
                                <p><strong>No, we cannot recover lost notes.</strong> This is by design for maximum security:</p>
                                <ul>
                                    <li>Notes are encrypted with unique keys</li>
                                    <li>We don't store the decryption keys in a way that allows recovery</li>
                                    <li>There's no user account system or backup mechanism</li>
                                    <li>Once a note is destroyed, it's permanently gone</li>
                                </ul>
                                <p><strong>Important:</strong> Always copy and save the note link immediately after creation. We recommend sharing it as soon as possible to avoid accidental loss.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                            <span>How long do notes last before they expire?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq5" class="collapse" data-bs-parent="#securityAccordion">
                            <div class="faq-body">
                                <p>You can set different expiry options when creating a note:</p>

                                <h6>Time-based expiry options:</h6>
                                <ul>
                                    <li>1 hour</li>
                                    <li>24 hours (default)</li>
                                    <li>7 days</li>
                                    <li>30 days</li>
                                </ul>

                                <h6>View-based expiry options:</h6>
                                <ul>
                                    <li>1 view (most secure)</li>
                                    <li>3 views</li>
                                    <li>5 views</li>
                                    <li>10 views</li>
                                </ul>

                                <h6>Expiry types:</h6>
                                <ul>
                                    <li><strong>After being viewed:</strong> Destroys after reaching view limit</li>
                                    <li><strong>After specific time:</strong> Destroys when time expires</li>
                                    <li><strong>After viewed OR time expires:</strong> Destroys when either condition is met</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 6 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false">
                            <span>What is the passcode feature and should I use it?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq6" class="collapse" data-bs-parent="#securityAccordion">
                            <div class="faq-body">
                                <p>The passcode feature adds an extra layer of security to your notes:</p>

                                <h6>How it works:</h6>
                                <ul>
                                    <li>You set a custom passcode when creating the note</li>
                                    <li>Recipients must enter the passcode to decrypt and view the note</li>
                                    <li>Wrong passcode attempts are logged for security</li>
                                    <li>Passcodes are hashed using Argon2ID for maximum security</li>
                                </ul>

                                <h6>When to use passcodes:</h6>
                                <ul>
                                    <li><strong>Highly sensitive information:</strong> Financial data, passwords, personal documents</li>
                                    <li><strong>Shared channels:</strong> When sending links through potentially insecure channels</li>
                                    <li><strong>Multiple recipients:</strong> To ensure only intended recipients can access</li>
                                    <li><strong>Extra verification:</strong> To confirm the recipient's identity</li>
                                </ul>

                                <p><strong>Tip:</strong> Share the passcode through a different communication channel than the note link for maximum security.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Questions -->
                <div class="faq-category mt-5">
                    <h3 class="h5 mb-0"><i class="bi bi-gear me-2"></i>Technical Questions</h3>
                </div>

                <div class="accordion" id="technicalAccordion">
                    <!-- FAQ 7 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false">
                            <span>Do you have rate limits?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq7" class="collapse" data-bs-parent="#technicalAccordion">
                            <div class="faq-body">
                                <p>Yes, we implement rate limiting to prevent abuse and ensure service quality:</p>

                                <h6>Current limits:</h6>
                                <ul>
                                    <li><strong>Note Creation:</strong> 10 notes per hour per IP address</li>
                                    <li><strong>Note Viewing:</strong> 50 views per hour per IP address</li>
                                </ul>

                                <h6>Why we have rate limits:</h6>
                                <ul>
                                    <li>Prevent spam and abuse</li>
                                    <li>Protect server resources</li>
                                    <li>Ensure service availability for all users</li>
                                    <li>Defend against automated attacks</li>
                                </ul>

                                <p>If you consistently hit rate limits for legitimate use, please contact us to discuss your needs.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 8 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq8" aria-expanded="false">
                            <span>Can I get email notifications when my note is accessed?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq8" class="collapse" data-bs-parent="#technicalAccordion">
                            <div class="faq-body">
                                <p>Yes! You can optionally receive email notifications when your notes are accessed:</p>

                                <h6>What's included in notifications:</h6>
                                <ul>
                                    <li>Date and time of access</li>
                                    <li>IP address of the viewer (anonymized for privacy)</li>
                                    <li>Browser/device information</li>
                                    <li>Whether the note was successfully decrypted</li>
                                </ul>

                                <h6>Privacy considerations:</h6>
                                <ul>
                                    <li>We only store your email temporarily for notification purposes</li>
                                    <li>Email addresses are deleted after the note expires</li>
                                    <li>We never use emails for marketing or other purposes</li>
                                    <li>Notification emails don't contain the note content</li>
                                </ul>

                                <p><strong>Note:</strong> Email notifications are optional and can be enabled when creating a note.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="faq-category mt-5">
                    <h3 class="h5 mb-0"><i class="bi bi-tools me-2"></i>Troubleshooting</h3>
                </div>

                <div class="accordion" id="troubleshootingAccordion">
                    <!-- FAQ 9 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq9" aria-expanded="false">
                            <span>What should I do if a note isn't working or shows an error?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq9" class="collapse" data-bs-parent="#troubleshootingAccordion">
                            <div class="faq-body">
                                <p>If you're experiencing issues with a note, here are common causes and solutions:</p>

                                <h6>Common issues:</h6>
                                <ul>
                                    <li><strong>"Note not found":</strong> The note may have expired, been viewed maximum times, or the link is incorrect</li>
                                    <li><strong>"Invalid passcode":</strong> Double-check the passcode for typos or case sensitivity</li>
                                    <li><strong>"Rate limit exceeded":</strong> You've hit viewing limits, wait an hour and try again</li>
                                    <li><strong>"Failed to decrypt":</strong> The note may be corrupted or tampered with</li>
                                </ul>

                                <h6>Troubleshooting steps:</h6>
                                <ol>
                                    <li>Verify the complete note URL is copied correctly</li>
                                    <li>Check if the note has expired by time or view count</li>
                                    <li>Try accessing from a different browser or device</li>
                                    <li>Ensure JavaScript is enabled in your browser</li>
                                    <li>Clear your browser cache and cookies</li>
                                </ol>

                                <p>If problems persist, the note may have been destroyed or corrupted. Unfortunately, once a note is destroyed, it cannot be recovered.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 10 -->
                    <div class="faq-card">
                        <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq10" aria-expanded="false">
                            <span>Is <?php echo APP_NAME; ?> free to use? Do you have premium features?</span>
                            <i class="bi bi-chevron-down icon"></i>
                        </div>
                        <div id="faq10" class="collapse" data-bs-parent="#troubleshootingAccordion">
                            <div class="faq-body">
                                <p><?php echo APP_NAME; ?> is completely free to use with no hidden costs or premium tiers:</p>

                                <h6>What's included for free:</h6>
                                <ul>
                                    <li>Unlimited note creation (within rate limits)</li>
                                    <li>AES-256 encryption</li>
                                    <li>All expiry options (time and view-based)</li>
                                    <li>Passcode protection</li>
                                    <li>Email notifications</li>
                                    <li>Up to 10,000 characters per note</li>
                                    <li>No ads or tracking</li>
                                </ul>

                                <h6>How we sustain the service:</h6>
                                <ul>
                                    <li>Optional donations from users who find value in the service</li>
                                    <li>Efficient infrastructure that keeps costs low</li>
                                    <li>Community support and contributions</li>
                                </ul>

                                <p>We believe secure communication should be accessible to everyone. If you find <?php echo APP_NAME; ?> valuable, consider supporting us through our donation page.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Still Have Questions? -->
                <div class="text-center mt-5 pt-4 border-top">
                    <h4 class="mb-3">Still Have Questions?</h4>
                    <p class="text-muted mb-4">Can't find what you're looking for? We're here to help!</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="<?php echo APP_URL; ?>/#create-note" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Note
                        </a>
                        <a href="<?php echo APP_URL; ?>/blog/" class="btn btn-outline-secondary">
                            <i class="bi bi-journal-text me-2"></i>Read Our Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "includes/footer.php" ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // FAQ Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faqSearch');
            const faqCards = document.querySelectorAll('.faq-card');
            const categories = document.querySelectorAll('.faq-category');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                faqCards.forEach(card => {
                    const header = card.querySelector('.faq-header span').textContent.toLowerCase();
                    const body = card.querySelector('.faq-body').textContent.toLowerCase();

                    if (header.includes(searchTerm) || body.includes(searchTerm)) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide categories based on visible FAQs
                categories.forEach(category => {
                    const nextElement = category.nextElementSibling;
                    if (nextElement && nextElement.classList.contains('accordion')) {
                        const visibleInCategory = Array.from(nextElement.querySelectorAll('.faq-card'))
                            .some(card => card.style.display !== 'none');

                        category.style.display = visibleInCategory ? 'block' : 'none';
                    }
                });

                // Show "no results" message if needed
                if (visibleCount === 0 && searchTerm.length > 0) {
                    showNoResults();
                } else {
                    hideNoResults();
                }
            });

            function showNoResults() {
                const existingMessage = document.getElementById('noResults');
                if (!existingMessage) {
                    const message = document.createElement('div');
                    message.id = 'noResults';
                    message.className = 'text-center py-5';
                    message.innerHTML = `
                        <i class="bi bi-search display-4 text-muted mb-3"></i>
                        <h5>No FAQs found</h5>
                        <p class="text-muted">Try different keywords or browse all questions above.</p>
                    `;
                    document.querySelector('.col-lg-8').appendChild(message);
                }
            }

            function hideNoResults() {
                const existingMessage = document.getElementById('noResults');
                if (existingMessage) {
                    existingMessage.remove();
                }
            }
        });

        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>