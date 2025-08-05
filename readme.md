# SecureNotes.net

<div align="center">
  <img src="assets/SecureNotes-Logo-lg.png" alt="SecureNotes Logo" width="300"/>
  
  **Share Secrets Securely**
  
  A modern, secure note-sharing application that allows users to share sensitive information through encrypted, self-destructing notes.
</div>

---

## Features

### **Security First**
- **End-to-End Encryption**: Military-grade AES-256 encryption for all notes
- **Self-Destructing**: Notes automatically destroy after being viewed or expiring
- **Zero-Log Policy**: No tracking, logging, or permanent storage of note contents
- **Passcode Protection**: Optional additional security layer with Argon2ID hashing
- **CSRF Protection**: Comprehensive protection against cross-site request forgery
- **Rate Limiting**: Prevents abuse and spam attacks

### **Flexible Expiry Options**
- **Time-Based**: 1 hour, 24 hours, 7 days, or 30 days
- **View-Based**: 1, 3, 5, or 10 views maximum
- **Combined**: Both time AND view limits for maximum security
 
### **Smart Notifications**
- **Email Alerts**: Get notified when your notes are accessed
- **Access Logs**: See who, when, and from where your notes were viewed
- **Privacy Focused**: Emails are deleted after note expiration

### **Modern Experience**
- **Responsive Design**: Works perfectly on all devices
- **Apple-Inspired UI**: Clean, modern interface with smooth animations
- **One-Click Sharing**: Easy sharing via WhatsApp, email, and direct links
- **Copy Protection**: Built-in clipboard functionality

---



### Core Security Features
- **AES-256-CBC Encryption** with random IV generation
- **Secure UUID Generation** using cryptographically secure random bytes
- **Argon2ID Password Hashing** for passcode protection
- **SQL Injection Prevention** with prepared statements
- **XSS Protection** with comprehensive input sanitization
- **Secure Headers** (HSTS, CSP, X-Frame-Options)
- **IP-Based Rate Limiting** with automatic cleanup

---

## Requirements

### System Requirements
- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache/Nginx with SSL certificate
- **Composer**: For dependency management

### Required PHP Extensions
```bash
php -m | grep -E "(pdo_mysql|openssl|json|curl)"
```
- `PDO MySQL` - Database connectivity
- `OpenSSL` - Encryption operations  
- `JSON` - Data serialization
- `cURL` - Email notifications

---

## Documentation


### Database Schema
```sql
-- Notes table
CREATE TABLE notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    encrypted_content LONGTEXT NOT NULL,
    encryption_key VARCHAR(64) NOT NULL,
    has_passcode BOOLEAN DEFAULT FALSE,
    passcode_hash VARCHAR(255),
    expiry_type ENUM('view', 'time', 'both') DEFAULT 'view',
    expires_at DATETIME,
    max_views INT DEFAULT 1,
    current_views INT DEFAULT 0,
    is_destroyed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accessed_at TIMESTAMP NULL,
    destroyed_at TIMESTAMP NULL,
    creator_ip VARCHAR(45),
    user_agent TEXT
);

-- Rate limiting table
CREATE TABLE rate_limits (
    ip_address VARCHAR(45) PRIMARY KEY,
    action_type ENUM('create', 'view') NOT NULL,
    count INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Access logs table
CREATE TABLE access_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    note_uuid VARCHAR(36),
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN DEFAULT TRUE,
    failure_reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Email notifications table
CREATE TABLE email_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    note_uuid VARCHAR(36),
    recipient_email VARCHAR(255),
    email_status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## <Version History

### v2.1.0 (Latest)
- Added FAQ page with comprehensive Q&A 
- Improved error handling and user feedback
- Enhanced UI with better animations
- Fixed minor security improvements

### v2.0.0
- Complete rewrite with modern PHP practices
- Enhanced security with Argon2ID password hashing
- Email notification system
- Fully responsive design
- API endpoints for programmatic access

### v1.0.0
- Initial release
- AES-256 encryption
- Time and view-based expiry
- Passcode protection
- Basic statistics

