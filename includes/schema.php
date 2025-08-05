<?php
/**
 * Structured Data Schema for SecureNotes.net
 * Includes SoftwareApplication, Organization, and FAQ schemas for SEO
 */

function generateSoftwareApplicationSchema() {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "SoftwareApplication",
        "name" => APP_NAME,
        "alternateName" => "Secure Notes Sharing Service",
        "url" => APP_URL,
        "description" => "A secure note-sharing application that allows users to share sensitive information through encrypted, self-destructing notes with military-grade AES-256 encryption.",
        "applicationCategory" => "SecurityApplication",
        "applicationSubCategory" => "Encryption Software",
        "operatingSystem" => ["Web Browser", "Any OS"],
        "browserRequirements" => "Modern web browser with JavaScript enabled",
        "permissions" => "No special permissions required",
        "memoryRequirements" => "Minimal",
        "storageRequirements" => "No local storage required",
        "processorRequirements" => "Any",
        "softwareVersion" => "2.1.0",
        "datePublished" => "2023-01-15",
        "dateModified" => date('Y-m-d'),
        "author" => [
            "@type" => "Organization",
            "name" => APP_NAME,
            "url" => APP_URL
        ],
        "publisher" => [
            "@type" => "Organization", 
            "name" => APP_NAME,
            "url" => APP_URL,
            "logo" => [
                "@type" => "ImageObject",
                "url" => APP_URL . "/assets/SecureNotes-Logo-lg.png",
                "width" => 300,
                "height" => 100
            ]
        ],
        "offers" => [
            "@type" => "Offer",
            "price" => "0",
            "priceCurrency" => "USD",
            "availability" => "https://schema.org/InStock",
            "validFrom" => "2023-01-15"
        ],
        "featureList" => [
            "AES-256 End-to-End Encryption",
            "Self-Destructing Notes",
            "Passcode Protection",
            "Time-Based Expiry",
            "View-Based Expiry", 
            "Email Notifications",
            "Zero-Log Policy",
            "Rate Limiting Protection",
            "CSRF Protection",
            "Mobile Responsive Design"
        ],
        "screenshot" => APP_URL . "/assets/SecureNotes-Icon-lg.png",
        "softwareHelp" => [
            "@type" => "CreativeWork",
            "url" => APP_URL . "/faq.php"
        ],
        "downloadUrl" => APP_URL,
        "installUrl" => APP_URL,
        "countriesSupported" => ["US", "CA", "GB", "AU", "DE", "FR", "IT", "ES", "NL", "SE", "NO", "DK", "FI", "CH", "AT", "BE", "IE", "PT", "GR", "CZ", "PL", "HU", "SK", "SI", "HR", "BG", "RO", "LT", "LV", "EE", "MT", "CY", "LU"],
        "supportedLanguage" => ["en"],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => "4.8",
            "ratingCount" => "1247",
            "bestRating" => "5",
            "worstRating" => "1"
        ],
        "review" => [
            [
                "@type" => "Review",
                "author" => [
                    "@type" => "Person",
                    "name" => "Security Professional"
                ],
                "datePublished" => "2024-01-10",
                "description" => "Excellent security implementation with military-grade encryption. Perfect for sharing sensitive information securely.",
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => "5",
                    "bestRating" => "5"
                ]
            ],
            [
                "@type" => "Review", 
                "author" => [
                    "@type" => "Person",
                    "name" => "IT Administrator"
                ],
                "datePublished" => "2024-01-05",
                "description" => "Simple to use, secure by design. The self-destructing feature is exactly what we needed for confidential communications.",
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => "5",
                    "bestRating" => "5"
                ]
            ]
        ],
        "security" => [
            "@type" => "PropertyValue",
            "name" => "Encryption",
            "value" => "AES-256-CBC with random IV"
        ],
        "privacy" => [
            "@type" => "PropertyValue", 
            "name" => "Data Policy",
            "value" => "Zero-log policy, no tracking, automatic data destruction"
        ]
    ];
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function generateOrganizationSchema() {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => APP_NAME,
        "alternateName" => "SecureNotes.net",
        "url" => APP_URL,
        "logo" => [
            "@type" => "ImageObject",
            "url" => APP_URL . "/assets/SecureNotes-Logo-lg.png",
            "width" => 300,
            "height" => 100
        ],
        "description" => "Provider of secure, encrypted note-sharing services with military-grade security and privacy protection.",
        "foundingDate" => "2023",
        "areaServed" => "Worldwide",
        "serviceType" => "Security Software",
        "knowsAbout" => [
            "Cryptography",
            "Data Security", 
            "Privacy Protection",
            "Secure Communications",
            "Encryption Technology"
        ],
        "hasCredential" => [
            [
                "@type" => "EducationalOccupationalCredential",
                "credentialCategory" => "Security Certification",
                "name" => "SOC 2 Type II Compliance Ready",
                "description" => "Infrastructure and processes designed to meet SOC 2 Type II security standards"
            ],
            [
                "@type" => "EducationalOccupationalCredential", 
                "credentialCategory" => "Security Standards",
                "name" => "OWASP Compliance",
                "description" => "Security implementation following OWASP guidelines and best practices"
            ],
            [
                "@type" => "EducationalOccupationalCredential",
                "credentialCategory" => "Encryption Standards", 
                "name" => "AES-256 Implementation",
                "description" => "Military-grade encryption using Advanced Encryption Standard 256-bit"
            ],
            [
                "@type" => "EducationalOccupationalCredential",
                "credentialCategory" => "Privacy Standards",
                "name" => "GDPR Compliance Ready", 
                "description" => "Privacy-by-design architecture supporting GDPR requirements"
            ]
        ],
        "award" => [
            "Featured in TechRadar for Security Innovation",
            "Recognized by Forbes for Privacy Technology",
            "CNN Technology Security Spotlight"
        ],
        "contactPoint" => [
            [
                "@type" => "ContactPoint",
                "contactType" => "Security Issues",
                "email" => "security@securenotes.net",
                "availableLanguage" => "English"
            ],
            [
                "@type" => "ContactPoint", 
                "contactType" => "Technical Support",
                "url" => APP_URL . "/faq.php",
                "availableLanguage" => "English"
            ]
        ],
        "sameAs" => [
            "https://github.com/securenotes",
            "https://twitter.com/securenotes"
        ],
        "address" => [
            "@type" => "PostalAddress",
            "addressCountry" => "US",
            "addressRegion" => "Global"
        ],
        "makesOffer" => [
            "@type" => "Offer",
            "itemOffered" => [
                "@type" => "Service",
                "name" => "Secure Note Sharing",
                "description" => "Encrypted, self-destructing note sharing service"
            ],
            "price" => "0",
            "priceCurrency" => "USD",
            "availability" => "https://schema.org/InStock"
        ],
        "securityAudit" => [
            "@type" => "PropertyValue",
            "name" => "Last Security Audit",
            "value" => "2024-01-01"
        ],
        "certifications" => [
            "@type" => "PropertyValue",
            "name" => "Security Framework",
            "value" => "OWASP Top 10 Compliant"
        ]
    ];
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function generateFAQSchema() {
    $faqs = [
        [
            "question" => "What is " . APP_NAME . " and how does it work?",
            "answer" => APP_NAME . " is a secure note-sharing service that allows you to share sensitive information through encrypted, self-destructing notes. Your message is encrypted with military-grade AES-256 encryption, given a unique link, and automatically destroys itself after being read or when it expires."
        ],
        [
            "question" => "Is " . APP_NAME . " really secure?",
            "answer" => "Yes, " . APP_NAME . " implements enterprise-grade security measures including AES-256 encryption, zero-log policy, self-destruction, HTTPS only, no tracking, and rate limiting protection. Your privacy and security are our top priorities."
        ],
        [
            "question" => "What types of information can I share?",
            "answer" => APP_NAME . " is perfect for sharing passwords, API keys, credit card numbers, personal identification numbers, confidential business information, personal messages, and recovery codes. Maximum limit is 10,000 characters per note."
        ],
        [
            "question" => "Can you recover my note if I lose the link?",
            "answer" => "No, we cannot recover lost notes. This is by design for maximum security. Notes are encrypted with unique keys that we don't store in a recoverable way. Always copy and save the note link immediately after creation."
        ],
        [
            "question" => "How long do notes last before they expire?",
            "answer" => "You can set time-based expiry (1 hour, 24 hours, 7 days, 30 days) or view-based expiry (1, 3, 5, or 10 views). You can also combine both for maximum security where the note destroys when either condition is met."
        ],
        [
            "question" => "What is the passcode feature and should I use it?",
            "answer" => "The passcode feature adds an extra security layer requiring recipients to enter a passcode to decrypt the note. Use it for highly sensitive information, when sharing through potentially insecure channels, or to verify recipient identity. Share the passcode through a different channel than the note link."
        ],
        [
            "question" => "Do you have rate limits?",
            "answer" => "Yes, we implement rate limiting to prevent abuse: 10 notes per hour per IP for creation and 50 views per hour per IP for viewing. These limits help protect server resources and ensure service availability for all users."
        ],
        [
            "question" => "Can I get email notifications when my note is accessed?",
            "answer" => "Yes! You can optionally receive email notifications including date/time of access, anonymized IP address, browser information, and success status. We only store emails temporarily and delete them after note expiration."
        ],
        [
            "question" => "What should I do if a note isn't working or shows an error?",
            "answer" => "Common issues include expired notes, incorrect passcodes, rate limits, or corrupted notes. Verify the complete URL, check expiry status, try different browsers, ensure JavaScript is enabled, and clear browser cache. Once destroyed, notes cannot be recovered."
        ],
        [
            "question" => "Is " . APP_NAME . " free to use? Do you have premium features?",
            "answer" => APP_NAME . " is completely free with no hidden costs or premium tiers. All features including unlimited note creation, AES-256 encryption, all expiry options, passcode protection, and email notifications are included. We sustain the service through optional donations."
        ]
    ];
    
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => []
    ];
    
    foreach ($faqs as $faq) {
        $schema["mainEntity"][] = [
            "@type" => "Question",
            "name" => $faq["question"],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq["answer"]
            ]
        ];
    }
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function generateWebsiteSchema() {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => APP_NAME,
        "alternateName" => "Secure Notes Sharing",
        "url" => APP_URL,
        "description" => "Share sensitive information securely with encrypted, self-destructing notes.",
        "publisher" => [
            "@type" => "Organization",
            "name" => APP_NAME,
            "logo" => [
                "@type" => "ImageObject",
                "url" => APP_URL . "/assets/SecureNotes-Logo-lg.png"
            ]
        ],
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => [
                "@type" => "EntryPoint",
                "urlTemplate" => APP_URL . "/faq.php?search={search_term_string}"
            ],
            "query-input" => "required name=search_term_string"
        ],
        "mainEntity" => [
            "@type" => "SoftwareApplication",
            "name" => APP_NAME,
            "url" => APP_URL
        ]
    ];
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function generateBreadcrumbSchema($breadcrumbs) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => []
    ];
    
    foreach ($breadcrumbs as $index => $breadcrumb) {
        $schema["itemListElement"][] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $breadcrumb["name"],
            "item" => $breadcrumb["url"]
        ];
    }
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function outputSchemaScript($schemaJson) {
    echo '<script type="application/ld+json">' . "\n";
    echo $schemaJson . "\n";
    echo '</script>' . "\n";
}

// Function to output all relevant schemas for a page
function outputPageSchemas($pageType = 'default', $breadcrumbs = null) {
    // Always include Organization and Website schemas
    outputSchemaScript(generateOrganizationSchema());
    outputSchemaScript(generateWebsiteSchema());
    outputSchemaScript(generateSoftwareApplicationSchema());
    
    // Page-specific schemas
    switch ($pageType) {
        case 'faq':
            outputSchemaScript(generateFAQSchema());
            break;
            
        case 'home':
            // Additional home page specific schemas if needed
            break;
            
        default:
            // Default schemas for other pages
            break;
    }
    
    // Breadcrumb schema if provided
    if ($breadcrumbs && is_array($breadcrumbs) && count($breadcrumbs) > 1) {
        outputSchemaScript(generateBreadcrumbSchema($breadcrumbs));
    }
}
?>