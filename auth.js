/* ============================================
   EasyBuy Uganda - Authentication JavaScript
   Version: 2.0.0
   ============================================ */

$(document).ready(function() {
    initLoginForm();
    initRegisterForm();
    initForgotPassword();
    initResetPassword();
    initSocialLogin();
});

function initLoginForm() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        const remember = $('#remember').is(':checked');
        
        if (!email || !password) {
            showNotification('Please enter email and password', 'warning');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/auth.php',
            method: 'POST',
            data: {
                action: 'login',
                email: email,
                password: password,
                remember: remember
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Login successful!', 'success');
                    
                    // Store user data
                    localStorage.setItem('user', JSON.stringify(response.user));
                    
                    // Redirect to previous page or account
                    const redirect = getQueryParam('redirect') || 'account.php';
                    setTimeout(() => {
                        window.location.href = redirect;
                    }, 1000);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Login failed. Please try again.', 'error');
            }
        });
    });
}

function initRegisterForm() {
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        
        const fullName = $('#full_name').val();
        const email = $('#email').val();
        const phone = $('#phone').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const terms = $('#terms').is(':checked');
        
        // Validation
        if (!fullName || !email || !phone || !password) {
            showNotification('Please fill in all fields', 'warning');
            return;
        }
        
        if (!isValidEmail(email)) {
            showNotification('Please enter a valid email address', 'warning');
            return;
        }
        
        if (!isValidUgandanPhone(phone)) {
            showNotification('Please enter a valid Ugandan phone number (e.g., 07XXXXXXXX or +256XXXXXXXXX)', 'warning');
            return;
        }
        
        if (password.length < 6) {
            showNotification('Password must be at least 6 characters', 'warning');
            return;
        }
        
        if (password !== confirmPassword) {
            showNotification('Passwords do not match', 'warning');
            return;
        }
        
        if (!terms) {
            showNotification('Please accept the Terms & Conditions', 'warning');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/auth.php',
            method: 'POST',
            data: {
                action: 'register',
                full_name: fullName,
                email: email,
                phone: phone,
                password: password
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Registration successful! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Registration failed. Please try again.', 'error');
            }
        });
    });
}

function initForgotPassword() {
    $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        
        if (!email || !isValidEmail(email)) {
            showNotification('Please enter a valid email address', 'warning');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/auth.php',
            method: 'POST',
            data: {
                action: 'forgot_password',
                email: email
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Password reset link sent to your email!', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error sending reset link. Please try again.', 'error');
            }
        });
    });
}

function initResetPassword() {
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const token = getQueryParam('token');
        
        if (password.length < 6) {
            showNotification('Password must be at least 6 characters', 'warning');
            return;
        }
        
        if (password !== confirmPassword) {
            showNotification('Passwords do not match', 'warning');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/auth.php',
            method: 'POST',
            data: {
                action: 'reset_password',
                token: token,
                password: password
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Password reset successful! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error resetting password. Please try again.', 'error');
            }
        });
    });
}

function initSocialLogin() {
    $('.social-login-btn').on('click', function() {
        const provider = $(this).data('provider');
        
        // Redirect to social login provider
        window.location.href = `/api/auth.php?action=social&provider=${provider}`;
    });
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidUgandanPhone(phone) {
    const ugandaPhoneRegex = /^(?:(?:\+256|0)[1-9][0-9]{8})$/;
    return ugandaPhoneRegex.test(phone);
}

function logout() {
    Swal.fire({
        title: 'Logout',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            
            $.ajax({
                url: '/api/auth.php',
                method: 'POST',
                data: {
                    action: 'logout'
                },
                success: function(response) {
                    hideLoading();
                    localStorage.removeItem('user');
                    window.location.href = 'index.php';
                },
                error: function() {
                    hideLoading();
                    window.location.href = 'logout.php';
                }
            });
        }
    });
}

// Check if user is logged in
function checkAuth() {
    const user = localStorage.getItem('user');
    if (user) {
        try {
            return JSON.parse(user);
        } catch(e) {
            return null;
        }
    }
    return null;
}

// Update UI based on auth status
function updateAuthUI() {
    const user = checkAuth();
    
    if (user) {
        $('.logged-out-only').hide();
        $('.logged-in-only').show();
        $('.user-name').text(user.full_name);
    } else {
        $('.logged-out-only').show();
        $('.logged-in-only').hide();
    }
}

// Auto logout on token expiry
setInterval(function() {
    const token = localStorage.getItem('auth_token');
    if (token) {
        $.ajax({
            url: '/api/auth.php',
            method: 'GET',
            data: {
                action: 'verify_token',
                token: token
            },
            error: function() {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                window.location.href = 'login.php';
            }
        });
    }
}, 300000); // Check every 5 minutes