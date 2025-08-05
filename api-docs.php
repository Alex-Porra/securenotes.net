<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - API Documentation</title>
    <meta name="description" content="Complete API documentation for SecureNotes - Learn how to integrate secure note sharing into your applications.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - API Documentation">
    <meta property="og:description" content="Complete API documentation for SecureNotes - Learn how to integrate secure note sharing into your applications.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/api-docs/">
    <meta property="og:type" content="website">
    <?php include "./includes/head.php" ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">

    <!-- Additional CSS -->
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

        .api-content h2 {
            color: #007bff !important;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .api-content h3 {
            color: #495057 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.75rem !important;
        }

        .api-content h4 {
            color: #6c757d !important;
            margin-top: 1rem !important;
            margin-bottom: 0.5rem !important;
        }

        .endpoint-card {
            background: white !important;
            border-radius: 8px !important;
            border: 1px solid #e9ecef !important;
            margin: 1rem 0 !important;
            overflow: hidden !important;
        }

        .endpoint-header {
            background: #f8f9fa !important;
            padding: 1rem !important;
            border-bottom: 1px solid #e9ecef !important;
        }

        .endpoint-body {
            padding: 1.5rem !important;
        }

        .method-badge {
            padding: 0.25rem 0.75rem !important;
            border-radius: 4px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
        }

        .method-get {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
        }

        .method-post {
            background-color: #d4edda !important;
            color: #155724 !important;
        }

        .method-put {
            background-color: #fff3cd !important;
            color: #856404 !important;
        }

        .method-delete {
            background-color: #f8d7da !important;
            color: #721c24 !important;
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

        .success-box {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%) !important;
            border-left: 4px solid #28a745 !important;
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

        .code-block {
            background: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 6px !important;
            padding: 1rem !important;
            margin: 1rem 0 !important;
            overflow-x: auto !important;
        }

        .parameter-table {
            background: white !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            margin: 1rem 0 !important;
        }

        .parameter-table th {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 1rem !important;
        }

        .parameter-table td {
            border-color: #e9ecef !important;
            padding: 0.75rem 1rem !important;
            vertical-align: top !important;
        }

        .required-badge {
            background-color: #dc3545 !important;
            color: white !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 3px !important;
            font-size: 0.7rem !important;
            font-weight: 500 !important;
        }

        .optional-badge {
            background-color: #6c757d !important;
            color: white !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 3px !important;
            font-size: 0.7rem !important;
            font-weight: 500 !important;
        }

        .response-example {
            background: #f1f3f4 !important;
            border: 1px solid #dadce0 !important;
            border-radius: 6px !important;
            padding: 1rem !important;
            margin: 1rem 0 !important;
        }

        .toc {
            background: white !important;
            border-radius: 8px !important;
            padding: 1.5rem !important;
            position: sticky !important;
            top: 2rem !important;
        }

        .toc a {
            color: #495057 !important;
            text-decoration: none !important;
            display: block !important;
            padding: 0.25rem 0 !important;
        }

        .toc a:hover {
            color: #007bff !important;
        }

        .section-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            width: 1.5rem !important;
            height: 1.5rem !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: bold !important;
            font-size: 0.8rem !important;
            margin-right: 0.5rem !important;
        }
    </style>

</head>

<body class="custom-body">
    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>


    <div class="container py-5">
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-3">
                <div class="toc">
                    <h6 class="fw-bold mb-3">Table of Contents</h6>
                    <div class="toc-list">
                        <a href="#introduction">Introduction</a>
                        <a href="#authentication">Authentication</a>
                        <a href="#rate-limits">Rate Limits</a>
                        <a href="#endpoints">API Endpoints</a>
                        <div class="ms-3">
                            <a href="#create-note">Create Note</a>
                            <a href="#view-note">View Note</a>
                            <a href="#stats">Statistics</a>
                        </div>
                        <a href="#errors">Error Handling</a>
                        <a href="#examples">Code Examples</a>
                        <a href="#sdks">SDKs & Libraries</a>
                        <a href="#webhooks">Webhooks</a>
                        <a href="#changelog">Changelog</a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card custom-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <i class="bi bi-code-slash display-4 text-primary mb-3"></i>
                            <h1 class="h2 fw-bold">API Documentation</h1>
                            <p class="text-muted">Integrate secure note sharing into your applications</p>
                        </div>

                        <div class="last-updated">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check text-muted me-2"></i>
                                <strong>Last Updated:</strong>
                                <span class="ms-2">June 3, 2025</span>
                                <span class="ms-auto">
                                    <span class="badge bg-primary">API Version 1.13</span>
                                </span>
                            </div>
                        </div>

                        <div class="api-content">
                            <div class="highlight-box">
                                <h4 class="mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Quick Start</h4>
                                <p class="mb-2"><strong>Base URL:</strong> <code><?php echo APP_URL; ?>/api/</code></p>
                                <p class="mb-2"><strong>Format:</strong> JSON requests and responses</p>
                                <p class="mb-0"><strong>Security:</strong> HTTPS required, rate limiting enabled</p>
                            </div>

                            <section id="introduction">
                                <h2><span class="section-number">1</span>Introduction</h2>
                                <p>The <?php echo APP_NAME; ?> API allows you to programmatically create and manage encrypted, self-destructing notes. Our REST API is designed with security and simplicity in mind, following industry best practices.</p>

                                <h3>Key Features</h3>
                                <ul>
                                    <li><strong>Zero-Knowledge Architecture:</strong> API cannot read note contents</li>
                                    <li><strong>End-to-End Encryption:</strong> Notes encrypted before reaching our servers</li>
                                    <li><strong>RESTful Design:</strong> Predictable resource-oriented URLs</li>
                                    <li><strong>JSON Format:</strong> All requests and responses use JSON</li>
                                    <li><strong>Rate Limited:</strong> Built-in protection against abuse</li>
                                </ul>

                                <h3>API Base URL</h3>
                                <div class="code-block">
                                    <code><?php echo APP_URL; ?>/api/</code>
                                </div>
                            </section>

                            <section id="authentication">
                                <h2><span class="section-number">2</span>Authentication</h2>
                                <p>Currently, the <?php echo APP_NAME; ?> API uses CSRF token-based authentication for creating notes and IP-based rate limiting for security.</p>

                                <div class="success-box">
                                    <h4 class="mb-3"><i class="bi bi-shield-check text-success me-2"></i>No API Keys Required</h4>
                                    <p class="mb-0">Our API is designed to be accessible without complex authentication schemes. CSRF tokens are automatically handled by our web interface.</p>
                                </div>

                                <h3>CSRF Protection</h3>
                                <p>When creating notes through the web interface, a CSRF token is required:</p>
                                <div class="code-block">
                                    <pre><code class="language-json">{
  "csrf_token": "abc123def456...",
  "content": "Your encrypted content here"
}</code></pre>
                                </div>
                            </section>

                            <section id="rate-limits">
                                <h2><span class="section-number">3</span>Rate Limits</h2>
                                <p>To ensure fair usage and prevent abuse, we implement rate limiting based on IP address:</p>

                                <div class="table-responsive parameter-table">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Limit</th>
                                                <th>Window</th>
                                                <th>Status Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Create Notes</strong></td>
                                                <td><?php echo RATE_LIMIT_CREATE; ?> requests</td>
                                                <td>1 hour</td>
                                                <td>429</td>
                                            </tr>
                                            <tr>
                                                <td><strong>View Notes</strong></td>
                                                <td><?php echo RATE_LIMIT_VIEW; ?> requests</td>
                                                <td>1 hour</td>
                                                <td>429</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Statistics</strong></td>
                                                <td>20 requests</td>
                                                <td>1 hour</td>
                                                <td>429</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="warning-box">
                                    <h4 class="mb-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Rate Limit Headers</h4>
                                    <p class="mb-0">When you hit a rate limit, you'll receive a <code>429 Too Many Requests</code> response. Wait for the time window to reset before making additional requests.</p>
                                </div>
                            </section>

                            <section id="endpoints">
                                <h2><span class="section-number">4</span>API Endpoints</h2>

                                <section id="create-note">
                                    <h3>Create Note</h3>
                                    <div class="endpoint-card">
                                        <div class="endpoint-header">
                                            <div class="d-flex align-items-center">
                                                <span class="method-badge method-post me-2">POST</span>
                                                <code>/api/create.php</code>
                                            </div>
                                        </div>
                                        <div class="endpoint-body">
                                            <p>Creates a new encrypted note with specified expiration and security settings.</p>

                                            <h4>Request Parameters</h4>
                                            <div class="table-responsive parameter-table">
                                                <table class="table table-borderless mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Type</th>
                                                            <th>Required</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><code>content</code></td>
                                                            <td>string</td>
                                                            <td><span class="required-badge">Required</span></td>
                                                            <td>The note content to encrypt (max 10,000 characters)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>expiry_type</code></td>
                                                            <td>string</td>
                                                            <td><span class="optional-badge">Optional</span></td>
                                                            <td>Expiry type: <code>view</code>, <code>time</code>, or <code>both</code> (default: <code>view</code>)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>expiry_time</code></td>
                                                            <td>integer</td>
                                                            <td><span class="optional-badge">Optional</span></td>
                                                            <td>Hours until expiry (1-8760, default: 24)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>max_views</code></td>
                                                            <td>integer</td>
                                                            <td><span class="optional-badge">Optional</span></td>
                                                            <td>Maximum view count (1-100, default: 1)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>passcode</code></td>
                                                            <td>string</td>
                                                            <td><span class="optional-badge">Optional</span></td>
                                                            <td>Optional passcode for additional security</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>notification_email</code></td>
                                                            <td>string</td>
                                                            <td><span class="optional-badge">Optional</span></td>
                                                            <td>Email address for access notifications</td>
                                                        </tr>
                                                        <tr>
                                                            <td><code>csrf_token</code></td>
                                                            <td>string</td>
                                                            <td><span class="required-badge">Required</span></td>
                                                            <td>CSRF protection token</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <h4>Example Request</h4>
                                            <div class="code-block">
                                                <pre><code class="language-bash">curl -X POST <?php echo APP_URL; ?>/api/create.php \
  -H "Content-Type: application/json" \
  -d '{
    "content": "This is a secret message",
    "expiry_type": "both",
    "expiry_time": 24,
    "max_views": 1,
    "passcode": "secret123",
    "notification_email": "user@example.com",
    "csrf_token": "your-csrf-token"
  }'</code></pre>
                                            </div>

                                            <h4>Success Response</h4>
                                            <div class="response-example">
                                                <pre><code class="language-json">{
  "success": true,
  "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "url": "<?php echo APP_URL; ?>/a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "expires_at": "2024-01-15 14:30:00",
  "max_views": 1,
  "has_passcode": true
}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section id="view-note">
                                    <h3>View Note</h3>
                                    <div class="endpoint-card">
                                        <div class="endpoint-header">
                                            <div class="d-flex align-items-center">
                                                <span class="method-badge method-get me-2">GET</span>
                                                <code>/api/view/{uuid}.php</code>
                                            </div>
                                        </div>
                                        <div class="endpoint-body">
                                            <p>Retrieves metadata about a note without decrypting it. Actual note viewing happens through the web interface for security.</p>

                                            <h4>URL Parameters</h4>
                                            <div class="table-responsive parameter-table">
                                                <table class="table table-borderless mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Type</th>
                                                            <th>Required</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><code>uuid</code></td>
                                                            <td>string</td>
                                                            <td><span class="required-badge">Required</span></td>
                                                            <td>The unique identifier of the note</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <h4>Example Request</h4>
                                            <div class="code-block">
                                                <pre><code class="language-bash">curl -X GET <?php echo APP_URL; ?>/api/view/a1b2c3d4-e5f6-7890-abcd-ef1234567890.php</code></pre>
                                            </div>

                                            <h4>Success Response</h4>
                                            <div class="response-example">
                                                <pre><code class="language-json">{
  "success": true,
  "note": {
    "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "has_passcode": true,
    "expiry_type": "both",
    "expires_at": "2024-01-15 14:30:00",
    "max_views": 1,
    "current_views": 0,
    "is_destroyed": false,
    "created_at": "2024-01-14 14:30:00"
  }
}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section id="stats">
                                    <h3>Statistics</h3>
                                    <div class="endpoint-card">
                                        <div class="endpoint-header">
                                            <div class="d-flex align-items-center">
                                                <span class="method-badge method-get me-2">GET</span>
                                                <code>/api/stats.php</code>
                                            </div>
                                        </div>
                                        <div class="endpoint-body">
                                            <p>Retrieves anonymized usage statistics and system health information.</p>

                                            <h4>Example Request</h4>
                                            <div class="code-block">
                                                <pre><code class="language-bash">curl -X GET <?php echo APP_URL; ?>/api/stats.php</code></pre>
                                            </div>

                                            <h4>Success Response</h4>
                                            <div class="response-example">
                                                <pre><code class="language-json">{
  "success": true,
  "stats": {
    "total_notes_created": 1542,
    "active_notes": 234,
    "destroyed_notes": 1308,
    "success_rate": 98.5,
    "notes_24h": 45,
    "notes_7d": 312,
    "notes_30d": 1205,
    "health_status": {
      "database": "healthy",
      "encryption": "available",
      "email": "configured"
    },
    "generated_at": "2024-01-14 14:30:00"
  }
}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </section>

                            <section id="errors">
                                <h2><span class="section-number">5</span>Error Handling</h2>
                                <p>The API uses conventional HTTP response codes to indicate success or failure. In general, codes in the 2xx range indicate success, codes in the 4xx range indicate client errors, and codes in the 5xx range indicate server errors.</p>

                                <h3>HTTP Status Codes</h3>
                                <div class="table-responsive parameter-table">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr>
                                                <th>Status Code</th>
                                                <th>Meaning</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>200</code></td>
                                                <td>OK</td>
                                                <td>Request succeeded</td>
                                            </tr>
                                            <tr>
                                                <td><code>400</code></td>
                                                <td>Bad Request</td>
                                                <td>Invalid request parameters</td>
                                            </tr>
                                            <tr>
                                                <td><code>404</code></td>
                                                <td>Not Found</td>
                                                <td>Note not found or destroyed</td>
                                            </tr>
                                            <tr>
                                                <td><code>405</code></td>
                                                <td>Method Not Allowed</td>
                                                <td>HTTP method not supported</td>
                                            </tr>
                                            <tr>
                                                <td><code>429</code></td>
                                                <td>Too Many Requests</td>
                                                <td>Rate limit exceeded</td>
                                            </tr>
                                            <tr>
                                                <td><code>500</code></td>
                                                <td>Internal Server Error</td>
                                                <td>Server error occurred</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h3>Error Response Format</h3>
                                <div class="code-block">
                                    <pre><code class="language-json">{
  "error": "Content is required",
  "code": "INVALID_CONTENT",
  "details": {
    "parameter": "content",
    "message": "Content cannot be empty"
  }
}</code></pre>
                                </div>
                            </section>

                            <section id="examples">
                                <h2><span class="section-number">6</span>Code Examples</h2>

                                <h3>JavaScript/Node.js</h3>
                                <div class="code-block">
                                    <pre><code class="language-javascript">// Create a secure note
const createNote = async (content, options = {}) => {
  const response = await fetch('<?php echo APP_URL; ?>/api/create.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      content: content,
      expiry_type: options.expiryType || 'view',
      max_views: options.maxViews || 1,
      passcode: options.passcode,
      csrf_token: await getCSRFToken()
    })
  });
  
  return await response.json();
};

// Usage
const note = await createNote('My secret message', {
  expiryType: 'both',
  maxViews: 1,
  passcode: 'secret123'
});

console.log('Note URL:', note.url);</code></pre>
                                </div>

                                <h3>Python</h3>
                                <div class="code-block">
                                    <pre><code class="language-python">import requests
import json

def create_secure_note(content, expiry_type='view', max_views=1, passcode=None):
    """Create a secure note using the SecureNotes API"""
    
    url = '<?php echo APP_URL; ?>/api/create.php'
    
    payload = {
        'content': content,
        'expiry_type': expiry_type,
        'max_views': max_views,
        'csrf_token': get_csrf_token()  # You need to implement this
    }
    
    if passcode:
        payload['passcode'] = passcode
    
    response = requests.post(url, json=payload)
    
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception(f"API Error: {response.status_code} - {response.text}")

# Usage
try:
    note = create_secure_note(
        content="My secret message",
        expiry_type="both",
        max_views=1,
        passcode="secret123"
    )
    print(f"Note URL: {note['url']}")
except Exception as e:
    print(f"Error: {e}")</code></pre>
                                </div>

                                <h3>PHP</h3>
                                <div class="code-block">
                                    <pre><code class="language-php"><?php
                                                                    class SecureNotesAPI
                                                                    {
                                                                        private $baseUrl;

                                                                        public function __construct($baseUrl = '<?php echo APP_URL; ?>')
                                                                        {
                                                                            $this->baseUrl = rtrim($baseUrl, '/');
                                                                        }

                                                                        public function createNote($content, $options = [])
                                                                        {
                                                                            $url = $this->baseUrl . '/api/create.php';

                                                                            $payload = [
                                                                                'content' => $content,
                                                                                'expiry_type' => $options['expiry_type'] ?? 'view',
                                                                                'max_views' => $options['max_views'] ?? 1,
                                                                                'csrf_token' => $this->getCSRFToken()
                                                                            ];

                                                                            if (!empty($options['passcode'])) {
                                                                                $payload['passcode'] = $options['passcode'];
                                                                            }

                                                                            $ch = curl_init();
                                                                            curl_setopt($ch, CURLOPT_URL, $url);
                                                                            curl_setopt($ch, CURLOPT_POST, true);
                                                                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                                                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                                                                'Content-Type: application/json'
                                                                            ]);

                                                                            $response = curl_exec($ch);
                                                                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                                            curl_close($ch);

                                                                            if ($httpCode === 200) {
                                                                                return json_decode($response, true);
                                                                            } else {
                                                                                throw new Exception("API Error: $httpCode - $response");
                                                                            }
                                                                        }

                                                                        private function getCSRFToken()
                                                                        {
                                                                            // Implement CSRF token retrieval
                                                                            return 'your-csrf-token';
                                                                        }
                                                                    }

                                                                    // Usage
                                                                    $api = new SecureNotesAPI();
                                                                    try {
                                                                        $note = $api->createNote('My secret message', [
                                                                            'expiry_type' => 'both',
                                                                            'max_views' => 1,
                                                                            'passcode' => 'secret123'
                                                                        ]);
                                                                        echo "Note URL: " . $note['url'] . "\n";
                                                                    } catch (Exception $e) {
                                                                        echo "Error: " . $e->getMessage() . "\n";
                                                                    }
                                                                    ?></code></pre>
                                </div>

                                <h3>cURL</h3>
                                <div class="code-block">
                                    <pre><code class="language-bash"># Create a note
curl -X POST <?php echo APP_URL; ?>/api/create.php \
  -H "Content-Type: application/json" \
  -d '{
    "content": "This is my secret message",
    "expiry_type": "view",
    "max_views": 1,
    "csrf_token": "your-csrf-token-here"
  }'

# Get statistics
curl -X GET <?php echo APP_URL; ?>/api/stats.php

# Check note metadata
curl -X GET <?php echo APP_URL; ?>/api/view/your-note-uuid.php</code></pre>
                                </div>
                            </section>

                            <section id="sdks">
                                <h2><span class="section-number">7</span>SDKs & Libraries</h2>
                                <p>We're working on official SDKs for popular programming languages. Currently, you can use the HTTP API directly or create wrapper functions as shown in the examples above.</p>

                                <h3>Community SDKs</h3>
                                <div class="table-responsive parameter-table">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr>
                                                <th>Language</th>
                                                <th>Library</th>
                                                <th>Status</th>
                                                <th>Maintainer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>JavaScript</td>
                                                <td><code>securenotes-js</code></td>
                                                <td><span class="badge bg-warning">Coming Soon</span></td>
                                                <td>Community</td>
                                            </tr>
                                            <tr>
                                                <td>Python</td>
                                                <td><code>pysecurenotes</code></td>
                                                <td><span class="badge bg-warning">Coming Soon</span></td>
                                                <td>Community</td>
                                            </tr>
                                            <tr>
                                                <td>Go</td>
                                                <td><code>go-securenotes</code></td>
                                                <td><span class="badge bg-warning">Coming Soon</span></td>
                                                <td>Community</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="highlight-box">
                                    <h4 class="mb-3"><i class="bi bi-github text-primary me-2"></i>Contribute</h4>
                                    <p class="mb-0">Want to create an SDK for your favorite language? We'd love to feature it here! Contact us at <strong>api@<?php echo str_replace(['http://', 'https://'], '', APP_URL); ?></strong></p>
                                </div>
                            </section>

                            <section id="webhooks">
                                <h2><span class="section-number">8</span>Webhooks</h2>
                                <p>Webhooks allow your application to receive real-time notifications when certain events occur with your notes.</p>

                                <div class="warning-box">
                                    <h4 class="mb-3"><i class="bi bi-construction text-warning me-2"></i>Coming Soon</h4>
                                    <p class="mb-0">Webhook functionality is currently in development. You'll be able to receive notifications for note access, expiration, and other events.</p>
                                </div>

                                <h3>Planned Webhook Events</h3>
                                <ul>
                                    <li><code>note.accessed</code> - When a note is successfully viewed</li>
                                    <li><code>note.failed_access</code> - When someone tries to access a note with wrong passcode</li>
                                    <li><code>note.expired</code> - When a note expires by time</li>
                                    <li><code>note.destroyed</code> - When a note is destroyed after max views</li>
                                </ul>

                                <h3>Webhook Payload Example</h3>
                                <div class="code-block">
                                    <pre><code class="language-json">{
  "event": "note.accessed",
  "timestamp": "2024-01-14T14:30:00Z",
  "data": {
    "note_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "access_details": {
      "ip_address": "192.168.1.1",
      "user_agent": "Mozilla/5.0...",
      "access_time": "2024-01-14T14:30:00Z"
    }
  }
}</code></pre>
                                </div>
                            </section>

                            <section id="changelog">
                                <h2><span class="section-number">9</span>Changelog</h2>

                                <h3>Version 1.0.0 - <?php echo date('F j, Y'); ?></h3>
                                <ul>
                                    <li>Initial API release</li>
                                    <li>Note creation endpoint</li>
                                    <li>Note metadata retrieval</li>
                                    <li>Statistics endpoint</li>
                                    <li>Rate limiting implementation</li>
                                    <li>CSRF protection</li>
                                </ul>

                                <div class="success-box">
                                    <h4 class="mb-3"><i class="bi bi-bell text-success me-2"></i>Stay Updated</h4>
                                    <p class="mb-0">Follow our changelog to stay informed about new features, improvements, and breaking changes. We follow semantic versioning for all API updates.</p>
                                </div>
                            </section>

                            <section id="support">
                                <h2><span class="section-number">10</span>Support & Resources</h2>



                                <h3>Additional Resources</h3>
                                <ul>
                                    <li><a href="/stats/" class="text-decoration-none">Live API Statistics</a></li>
                                    <li><a href="/privacy/" class="text-decoration-none">Privacy Policy</a></li>
                                    <li><a href="/terms" class="text-decoration-none">Terms of Service</a></li>
                                    <li><a href="/" class="text-decoration-none">Web Interface</a></li>
                                </ul>

                                <div class="highlight-box">
                                    <h4 class="mb-3"><i class="bi bi-lightbulb text-primary me-2"></i>Best Practices</h4>
                                    <ul class="mb-0">
                                        <li><strong>Always use HTTPS</strong> when making API calls</li>
                                        <li><strong>Implement proper error handling</strong> for all API responses</li>
                                        <li><strong>Respect rate limits</strong> to ensure consistent service</li>
                                        <li><strong>Never log sensitive data</strong> like note content or passcodes</li>
                                        <li><strong>Use appropriate expiry settings</strong> based on content sensitivity</li>
                                    </ul>
                                </div>
                            </section>

                            <p class="text-muted mt-5 pt-4 border-top">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    This API documentation is designed to help you integrate <?php echo APP_NAME; ?> into your applications securely and efficiently. If you have questions or suggestions, please don't hesitate to contact us.
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

    <!-- Prism.js for syntax highlighting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    <!-- Custom JavaScript for API docs -->
    <script>
        // Smooth scrolling for table of contents
        document.querySelectorAll('.toc a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Highlight current section in TOC
        function highlightCurrentSection() {
            const sections = document.querySelectorAll('section[id]');
            const tocLinks = document.querySelectorAll('.toc a');

            let currentSection = '';
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top <= 100 && rect.bottom >= 100) {
                    currentSection = section.id;
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('text-primary', 'fw-bold');
                if (link.getAttribute('href') === '#' + currentSection) {
                    link.classList.add('text-primary', 'fw-bold');
                }
            });
        }

        // Update TOC highlighting on scroll
        window.addEventListener('scroll', highlightCurrentSection);

        // Copy code blocks to clipboard
        document.querySelectorAll('.code-block code').forEach(codeBlock => {
            codeBlock.style.cursor = 'pointer';
            codeBlock.title = 'Click to copy';

            codeBlock.addEventListener('click', function() {
                const text = this.textContent;
                navigator.clipboard.writeText(text).then(() => {
                    // Show feedback
                    const originalTitle = this.title;
                    this.title = 'Copied!';
                    setTimeout(() => {
                        this.title = originalTitle;
                    }, 2000);
                });
            });
        });

        // Initial highlight
        highlightCurrentSection();
    </script>
</body>

</html>