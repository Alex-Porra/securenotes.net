/**
 * SecureNotes Main JavaScript
 * Handles form interactions, validation, and API calls
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize components
    initializeFormHandlers();
    initializeCharacterCounter();
    initializeExpiryTypeHandler();
    initializeEmailNotificationHandler();
    initializeCopyFunctionality();
    initializeFormValidation();
});

/**
 * Initialize form submission handlers
 */
function initializeFormHandlers() {
    const createForm = document.getElementById('createNoteForm');
    if (createForm) {
        createForm.addEventListener('submit', handleNoteCreation);
    }
}

/**
 * Handle note creation form submission
 */
async function handleNoteCreation(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('createBtn');
    const successDiv = document.getElementById('successMessage');
    const errorDiv = document.getElementById('errorMessage');

    // Disable submit button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

    // Hide previous messages
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';

    try {
        // Validate form data
        const content = formData.get('content').trim();
        if (!content) {
            throw new Error('Please enter some content for your note.');
        }

        if (content.length > 10000) {
            throw new Error('Content is too long. Maximum 10,000 characters allowed.');
        }

        // Prepare data for API
        const noteData = {
            csrf_token: formData.get('csrf_token'),
            content: content,
            expiry_type: formData.get('expiry_type'),
            expiry_time: formData.get('expiry_time'),
            max_views: formData.get('max_views'),
            passcode: formData.get('passcode'),
            notification_email: formData.get('notification_email')
        };

        // Make API request
        const response = await fetch('/api/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(noteData)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Failed to create note');
        }

        // Show success message
        displaySuccess(result);

        // Reset form
        form.reset();
        updateCharacterCount();

        // Scroll to success message
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } catch (error) {
        displayError(error.message);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-shield-lock me-2"></i>Create Secure Note';
    }
}

/**
 * Display success message with note URL
 */
function displaySuccess(result) {
    const successDiv = document.getElementById('successMessage');
    const noteUrlInput = document.getElementById('noteUrl');

    noteUrlInput.value = result.url;
    successDiv.style.display = 'block';

    // Auto-select the URL for easy copying
    setTimeout(() => {
        noteUrlInput.select();
        noteUrlInput.setSelectionRange(0, 99999); // For mobile devices
    }, 100);
}

/**
 * Display error message
 */
function displayError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    errorText.textContent = message;
    errorDiv.style.display = 'block';
}

/**
 * Initialize character counter for textarea
 */
function initializeCharacterCounter() {
    const textarea = document.getElementById('noteContent');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        textarea.addEventListener('input', updateCharacterCount);
        updateCharacterCount(); // Initial count
    }
}

/**
 * Update character count display
 */
function updateCharacterCount() {
    const textarea = document.getElementById('noteContent');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        const count = textarea.value.length;
        charCount.textContent = count.toLocaleString();

        // Change color based on limit
        const maxLength = 10000;
        const percentage = (count / maxLength) * 100;

        if (percentage > 90) {
            charCount.className = 'text-danger';
        } else if (percentage > 75) {
            charCount.className = 'text-warning';
        } else {
            charCount.className = 'text-muted';
        }
    }
}

/**
 * Initialize expiry type change handler
 */
function initializeExpiryTypeHandler() {
    const expiryTypeSelect = document.getElementById('expiryType');
    const timeExpiryGroup = document.getElementById('timeExpiryGroup');

    if (expiryTypeSelect && timeExpiryGroup) {
        expiryTypeSelect.addEventListener('change', function () {
            const showTimeExpiry = this.value === 'time' || this.value === 'both';
            timeExpiryGroup.style.display = showTimeExpiry ? 'block' : 'none';

            // Make time expiry required when visible
            const timeSelect = document.getElementById('expiryTime');
            if (timeSelect) {
                timeSelect.required = showTimeExpiry;
            }
        });

        // Trigger initial state
        expiryTypeSelect.dispatchEvent(new Event('change'));
    }
}

/**
 * Initialize email notification handler
 */
function initializeEmailNotificationHandler() {
    const emailCheckbox = document.getElementById('emailNotification');
    const emailGroup = document.getElementById('emailGroup');
    const emailInput = document.getElementById('notificationEmail');

    if (emailCheckbox && emailGroup && emailInput) {
        emailCheckbox.addEventListener('change', function () {
            const showEmail = this.checked;
            emailGroup.style.display = showEmail ? 'block' : 'none';
            emailInput.required = showEmail;

            if (!showEmail) {
                emailInput.value = '';
            } else {
                emailInput.focus();
            }
        });
    }
}

/**
 * Initialize copy to clipboard functionality
 */
function initializeCopyFunctionality() {
    const copyBtn = document.getElementById('copyBtn');

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const noteUrl = document.getElementById('noteUrl');

            if (noteUrl) {
                // Highlight the URL text
                noteUrl.select();
                noteUrl.setSelectionRange(0, 99999); // For mobile devices

                // Modern clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(noteUrl.value).then(function () {
                        showCopySuccess();
                    }).catch(function (err) {
                        console.error('Failed to copy: ', err);
                        fallbackCopy(noteUrl);
                    });
                } else {
                    fallbackCopy(noteUrl);
                }
            }
        });
    }
}

/**
 * Fallback copy method for older browsers
 */
function fallbackCopy(input) {
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess();
        } else {
            showCopyError();
        }
    } catch (err) {
        console.error('Fallback copy failed: ', err);
        showCopyError();
    }
}

/**
 * Show copy success feedback
 */
function showCopySuccess() {
    const copyBtn = document.getElementById('copyBtn');
    const originalText = copyBtn.innerHTML;

    copyBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Copied!';
    copyBtn.classList.remove('btn-outline-secondary');
    copyBtn.classList.add('btn-success');

    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-outline-secondary');
    }, 2000);
}

/**
 * Show copy error feedback
 */
function showCopyError() {
    const copyBtn = document.getElementById('copyBtn');
    const originalText = copyBtn.innerHTML;

    copyBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Failed';
    copyBtn.classList.remove('btn-outline-secondary');
    copyBtn.classList.add('btn-danger');

    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.remove('btn-danger');
        copyBtn.classList.add('btn-outline-secondary');
    }, 2000);
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });

    // Real-time validation for specific fields
    const emailInput = document.getElementById('notificationEmail');
    if (emailInput) {
        emailInput.addEventListener('blur', function () {
            validateEmail(this);
        });
    }

    const passcodeInput = document.getElementById('passcode');
    if (passcodeInput) {
        passcodeInput.addEventListener('input', function () {
            validatePasscode(this);
        });
    }
}

/**
 * Validate email input
 */
function validateEmail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = !input.value || emailRegex.test(input.value);

    input.classList.toggle('is-invalid', !isValid && input.value);
    input.classList.toggle('is-valid', isValid && input.value);

    return isValid;
}

/**
 * Validate passcode input
 */
function validatePasscode(input) {
    const value = input.value;
    const minLength = 4;
    const isValid = !value || value.length >= minLength;

    input.classList.toggle('is-invalid', !isValid && value);
    input.classList.toggle('is-valid', isValid && value);

    // Show/hide feedback
    let feedback = input.parentNode.querySelector('.invalid-feedback');
    if (!feedback && !isValid && value) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = `Passcode must be at least ${minLength} characters long.`;
        input.parentNode.appendChild(feedback);
    } else if (feedback && isValid) {
        feedback.remove();
    }

    return isValid;
}

/**
 * Smooth scroll utility
 */
function smoothScrollTo(element) {
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
}

/**
 * Show loading state for buttons
 */
function showButtonLoading(button, loadingText = 'Processing...') {
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
    }
}

/**
 * Hide loading state for buttons
 */
function hideButtonLoading(button) {
    if (button && button.dataset.originalText) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
        delete button.dataset.originalText;
    }
}

/**
 * Format time remaining
 */
function formatTimeRemaining(expiresAt) {
    const now = new Date();
    const expiry = new Date(expiresAt);
    const diff = expiry - now;

    if (diff <= 0) {
        return 'Expired';
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    if (days > 0) {
        return `${days}d ${hours}h`;
    } else if (hours > 0) {
        return `${hours}h ${minutes}m`;
    } else {
        return `${minutes}m`;
    }
}

/**
 * Security warning for development
 */
if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
    console.warn('⚠️ SecureNotes should only be used over HTTPS in production!');
}



document.addEventListener('DOMContentLoaded', function () {
    // WhatsApp Share
    const whatsappBtn = document.getElementById('whatsappShare');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function () {
            const noteUrl = document.getElementById('noteUrl').value;
            const message = `🔒 Secure Note Shared\n\nI've created a secure note for you. Click the link below to view it:\n\n${noteUrl}\n\n⚠️ This note will be deleted after viewing, so save any important information.`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        });
    }

    // Email Share
    const emailBtn = document.getElementById('emailShare');
    if (emailBtn) {
        emailBtn.addEventListener('click', function () {
            const noteUrl = document.getElementById('noteUrl').value;
            const subject = 'Secure Note Shared';
            const body = `Hi,

I've created a secure note for you. Click the link below to view it:

${noteUrl}

Important: This note will be automatically deleted after you view it, so please save any important information.

Best regards`;

            const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoUrl;
        });
    }

    // Enhanced copy button with share button animations
    const copyBtn = document.getElementById('copyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const noteUrlInput = document.getElementById('noteUrl');
            const noteUrl = noteUrlInput.value;

            // Use the modern clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(noteUrl).then(function () {
                    updateCopyButton();
                }).catch(function (err) {
                    // Fallback for older browsers
                    fallbackCopyTextToClipboard(noteUrl);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyTextToClipboard(noteUrl);
            }
        });
    }

    // Function to update copy button appearance
    function updateCopyButton() {
        const copyBtn = document.getElementById('copyBtn');
        const originalHtml = copyBtn.innerHTML;

        // Update button text temporarily
        copyBtn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        copyBtn.classList.remove('btn-outline-secondary');
        copyBtn.classList.add('btn-success');

        // Animate share buttons to draw attention
        const whatsappBtn = document.getElementById('whatsappShare');
        const emailBtn = document.getElementById('emailShare');

        if (whatsappBtn) whatsappBtn.classList.add('pulse-animation');
        if (emailBtn) emailBtn.classList.add('pulse-animation');

        // Reset after 2 seconds
        setTimeout(function () {
            copyBtn.innerHTML = originalHtml;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-outline-secondary');

            if (whatsappBtn) whatsappBtn.classList.remove('pulse-animation');
            if (emailBtn) emailBtn.classList.remove('pulse-animation');
        }, 2000);
    }

    // Fallback function for older browsers
    function fallbackCopyTextToClipboard(text) {
        const noteUrlInput = document.getElementById('noteUrl');

        // Select the text
        noteUrlInput.select();
        noteUrlInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                updateCopyButton();
            } else {
                console.error('Fallback: Could not copy text');
                alert('Copy failed. Please manually select and copy the URL.');
            }
        } catch (err) {
            console.error('Fallback: Could not copy text: ', err);
            alert('Copy failed. Please manually select and copy the URL.');
        }

        // Remove selection
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        }
    }
});