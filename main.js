/* ============================================
   EasyBuy Uganda - Main JavaScript
   Version: 2.0.0
   Author: EasyBuy Team
   ============================================ */

// Wait for DOM to be ready
$(document).ready(function() {
    // Initialize all components
    initTooltips();
    initPopovers();
    initSmoothScroll();
    initBackToTop();
    initSearchAutocomplete();
    initProductFilters();
    initQuantitySelectors();
    initFormValidation();
    initPasswordToggle();
    initCountdownTimers();
});

/* ============================================
   Global Variables
   ============================================ */
window.EasyBuy = {
    cart: {
        items: [],
        total: 0,
        count: 0
    },
    user: {
        loggedIn: false,
        id: null,
        name: null,
        email: null
    },
    config: {
        apiUrl: '/api/',
        currency: 'UGX',
        deliveryFee: 15000,
        freeDeliveryThreshold: 200000
    }
};

/* ============================================
   Initialization Functions
   ============================================ */
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initPopovers() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function initSmoothScroll() {
    $('a[href*="#"]:not([href="#"])').on('click', function(e) {
        if (this.hash !== '') {
            e.preventDefault();
            const hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top - 70
            }, 800, function() {
                window.location.hash = hash;
            });
        }
    });
}

function initBackToTop() {
    if ($('#back-to-top').length === 0) {
        $('body').append(`
            <button id="back-to-top" class="btn btn-primary rounded-circle position-fixed" 
                    style="bottom: 20px; right: 20px; display: none; width: 50px; height: 50px; z-index: 1000;">
                <i class="fas fa-arrow-up"></i>
            </button>
        `);
    }
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    
    $('#back-to-top').click(function() {
        $('html, body').animate({ scrollTop: 0 }, 800);
    });
}

/* ============================================
   Cart Functions
   ============================================ */
function addToCart(productId, quantity = 1) {
    if (!productId) return;
    
    showLoading();
    
    $.ajax({
        url: '/api/cart.php',
        method: 'POST',
        data: {
            action: 'add',
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showNotification('Product added to cart!', 'success');
                updateCartCount(response.cart_count);
                animateCartIcon();
            } else if (response.require_login) {
                showNotification('Please login to add items to cart', 'warning');
                setTimeout(() => {
                    window.location.href = '/login.php?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1500);
            } else {
                showNotification(response.message || 'Error adding to cart', 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error adding to cart. Please try again.', 'error');
        }
    });
}

function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) quantity = 1;
    
    showLoading();
    
    $.ajax({
        url: '/api/cart.php',
        method: 'POST',
        data: {
            action: 'update',
            cart_id: cartId,
            quantity: quantity
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                location.reload();
            } else {
                showNotification(response.message || 'Error updating cart', 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error updating cart', 'error');
        }
    });
}

function removeFromCart(cartId) {
    confirmDelete('Remove this item from cart?', function() {
        showLoading();
        
        $.ajax({
            url: '/api/cart.php',
            method: 'POST',
            data: {
                action: 'remove',
                cart_id: cartId
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Item removed from cart', 'success');
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error removing item', 'error');
            }
        });
    });
}

function clearCart() {
    confirmDelete('Clear your entire cart? This action cannot be undone.', function() {
        showLoading();
        
        $.ajax({
            url: '/api/cart.php',
            method: 'POST',
            data: {
                action: 'clear'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Cart cleared', 'success');
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error clearing cart', 'error');
            }
        });
    });
}

function updateCartCount(count) {
    const cartBadge = $('.cart-badge, .fa-shopping-cart').parent().find('.badge');
    if (count > 0) {
        if (cartBadge.length) {
            cartBadge.text(count);
        } else {
            $('.fa-shopping-cart').parent().append(
                `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">${count}</span>`
            );
        }
    } else {
        cartBadge.remove();
    }
}

function animateCartIcon() {
    const cartIcon = $('.fa-shopping-cart');
    cartIcon.parent().addClass('cart-badge-animation');
    setTimeout(() => {
        cartIcon.parent().removeClass('cart-badge-animation');
    }, 300);
}

/* ============================================
   Wishlist Functions
   ============================================ */
function toggleWishlist(productId, buttonElement) {
    if (!productId) return;
    
    showLoading();
    
    $.ajax({
        url: '/api/wishlist.php',
        method: 'POST',
        data: {
            action: 'toggle',
            product_id: productId
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                if (response.added) {
                    showNotification('Added to wishlist', 'success');
                    if (buttonElement) {
                        $(buttonElement).html('<i class="fas fa-heart text-danger"></i> Remove from Wishlist');
                    }
                } else {
                    showNotification('Removed from wishlist', 'success');
                    if (buttonElement) {
                        $(buttonElement).html('<i class="fas fa-heart"></i> Add to Wishlist');
                    }
                }
            } else if (response.require_login) {
                showNotification('Please login to manage wishlist', 'warning');
                setTimeout(() => {
                    window.location.href = '/login.php?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1500);
            } else {
                showNotification(response.message || 'Error updating wishlist', 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error updating wishlist', 'error');
        }
    });
}

/* ============================================
   Checkout Functions
   ============================================ */
function applyCoupon() {
    const couponCode = $('#coupon-code').val();
    
    if (!couponCode) {
        showNotification('Please enter coupon code', 'warning');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '/api/coupon.php',
        method: 'POST',
        data: {
            action: 'apply',
            code: couponCode
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showNotification('Coupon applied successfully!', 'success');
                location.reload();
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Invalid coupon code', 'error');
        }
    });
}

function removeCoupon() {
    showLoading();
    
    $.ajax({
        url: '/api/coupon.php',
        method: 'POST',
        data: {
            action: 'remove'
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showNotification('Coupon removed', 'success');
                location.reload();
            }
        }
    });
}

/* ============================================
   Newsletter Functions
   ============================================ */
function subscribeNewsletter(email) {
    if (!email || !isValidEmail(email)) {
        showNotification('Please enter a valid email address', 'warning');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '/api/newsletter.php',
        method: 'POST',
        data: {
            action: 'subscribe',
            email: email
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showNotification(response.message, 'success');
                $('#newsletter-email').val('');
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error subscribing to newsletter', 'error');
        }
    });
}

/* ============================================
   Product Review Functions
   ============================================ */
function submitReview(productId, rating, comment) {
    if (!productId || !rating || !comment) {
        showNotification('Please provide rating and comment', 'warning');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '/api/reviews.php',
        method: 'POST',
        data: {
            action: 'add',
            product_id: productId,
            rating: rating,
            comment: comment
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showNotification('Review submitted successfully!', 'success');
                location.reload();
            } else if (response.require_login) {
                showNotification('Please login to leave a review', 'warning');
                setTimeout(() => {
                    window.location.href = '/login.php?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1500);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error submitting review', 'error');
        }
    });
}

/* ============================================
   Search Functions
   ============================================ */
let searchTimeout;

function initSearchAutocomplete() {
    $('input[name="search"]').on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        if (searchTerm.length > 2) {
            searchTimeout = setTimeout(function() {
                performSearch(searchTerm);
            }, 500);
        }
    });
}

function performSearch(query) {
    $.ajax({
        url: '/api/products.php',
        method: 'GET',
        data: { search: query, limit: 5 },
        success: function(response) {
            if (response.products && response.products.length) {
                showSearchSuggestions(response.products);
            }
        }
    });
}

function showSearchSuggestions(products) {
    let suggestionsHtml = '<div class="search-suggestions">';
    products.forEach(product => {
        suggestionsHtml += `
            <a href="/product.php?id=${product.id}" class="suggestion-item">
                <img src="/assets/images/products/${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <div>${product.name}</div>
                    <small>UGX ${formatNumber(product.price)}</small>
                </div>
            </a>
        `;
    });
    suggestionsHtml += '</div>';
    
    $('.search-suggestions').remove();
    $('.input-group').after(suggestionsHtml);
}

/* ============================================
   UI Helper Functions
   ============================================ */
function showLoading() {
    if ($('.spinner-overlay').length === 0) {
        $('body').append(`
            <div class="spinner-overlay">
                <div class="spinner"></div>
            </div>
        `);
    }
    $('.spinner-overlay').fadeIn(200);
}

function hideLoading() {
    $('.spinner-overlay').fadeOut(200);
}

function showNotification(message, type = 'success') {
    const icon = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const toast = $(`
        <div class="toast-notification toast-${type}">
            <i class="fas ${icon[type]}"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('body').append(toast);
    
    setTimeout(() => {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

function confirmDelete(message, callback) {
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function formatNumber(number) {
    return new Intl.NumberFormat('en-US').format(number);
}

function formatCurrency(amount) {
    return `UGX ${formatNumber(amount)}`;
}

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function setQueryParam(param, value) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set(param, value);
    window.location.search = urlParams.toString();
}

/* ============================================
   Event Handlers
   ============================================ */
$(document).on('click', '.add-to-cart', function() {
    const productId = $(this).data('product-id');
    const quantity = $(this).data('quantity') || $('#quantity').val() || 1;
    addToCart(productId, quantity);
});

$(document).on('click', '.update-cart', function() {
    const cartId = $(this).data('cart-id');
    const quantity = $(this).closest('.cart-item').find('.cart-qty').val();
    updateCartQuantity(cartId, quantity);
});

$(document).on('click', '.remove-from-cart', function() {
    const cartId = $(this).data('cart-id');
    removeFromCart(cartId);
});

$(document).on('click', '.clear-cart', function() {
    clearCart();
});

$(document).on('click', '.wishlist-btn', function() {
    const productId = $(this).data('product-id');
    toggleWishlist(productId, this);
});

$(document).on('click', '.apply-coupon', function() {
    applyCoupon();
});

$(document).on('click', '.remove-coupon', function() {
    removeCoupon();
});

$(document).on('submit', '#newsletter-form', function(e) {
    e.preventDefault();
    const email = $(this).find('input[type="email"]').val();
    subscribeNewsletter(email);
});

$(document).on('submit', '#review-form', function(e) {
    e.preventDefault();
    const productId = $(this).data('product-id');
    const rating = $(this).find('input[name="rating"]:checked').val();
    const comment = $(this).find('textarea[name="comment"]').val();
    submitReview(productId, rating, comment);
});

/* ============================================
   Quantity Selectors
   ============================================ */
function initQuantitySelectors() {
    $(document).on('click', '.quantity-decrement', function() {
        const input = $(this).siblings('.quantity-input');
        let value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1).trigger('change');
        }
    });
    
    $(document).on('click', '.quantity-increment', function() {
        const input = $(this).siblings('.quantity-input');
        let value = parseInt(input.val());
        const max = parseInt(input.attr('max')) || 999;
        if (value < max) {
            input.val(value + 1).trigger('change');
        }
    });
    
    $(document).on('change', '.quantity-input', function() {
        let value = parseInt($(this).val());
        const max = parseInt($(this).attr('max')) || 999;
        const min = parseInt($(this).attr('min')) || 1;
        
        if (isNaN(value)) value = min;
        if (value < min) value = min;
        if (value > max) value = max;
        
        $(this).val(value);
    });
}

/* ============================================
   Form Validation
   ============================================ */
function initFormValidation() {
    $('form[data-validate]').on('submit', function(e) {
        let isValid = true;
        
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        $(this).find('[type="email"]').each(function() {
            const email = $(this).val();
            if (email && !isValidEmail(email)) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields correctly', 'warning');
        }
    });
    
    $('form input, form select, form textarea').on('input change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });
}

/* ============================================
   Password Toggle
   ============================================ */
function initPasswordToggle() {
    $(document).on('click', '.toggle-password', function() {
        const passwordInput = $(this).siblings('input');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });
}

/* ============================================
   Countdown Timers
   ============================================ */
function initCountdownTimers() {
    $('.countdown-timer').each(function() {
        const endDate = new Date($(this).data('end')).getTime();
        updateCountdown($(this), endDate);
        
        setInterval(function() {
            updateCountdown($(this), endDate);
        }.bind(this), 1000);
    });
}

function updateCountdown(element, endDate) {
    const now = new Date().getTime();
    const distance = endDate - now;
    
    if (distance < 0) {
        location.reload();
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    element.find('.days').text(String(days).padStart(2, '0'));
    element.find('.hours').text(String(hours).padStart(2, '0'));
    element.find('.minutes').text(String(minutes).padStart(2, '0'));
    element.find('.seconds').text(String(seconds).padStart(2, '0'));
}

/* ============================================
   Product Filters
   ============================================ */
function initProductFilters() {
    $('#sort-select').on('change', function() {
        const value = $(this).val();
        if (value) {
            setQueryParam('sort', value);
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('sort');
            window.location.href = url.toString();
        }
    });
    
    $('#price-range').on('change', function() {
        const minPrice = $('#min-price').val();
        const maxPrice = $('#max-price').val();
        if (minPrice) setQueryParam('min_price', minPrice);
        if (maxPrice) setQueryParam('max_price', maxPrice);
    });
    
    $('.filter-category').on('change', function() {
        const category = $(this).val();
        if (category) {
            setQueryParam('category', category);
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('category');
            window.location.href = url.toString();
        }
    });
}

/* ============================================
   Image Gallery
   ============================================ */
function initImageGallery() {
    $('.product-thumbnail').on('click', function() {
        const imageUrl = $(this).data('image');
        $('#main-product-image').attr('src', imageUrl);
        $('.product-thumbnail').removeClass('active');
        $(this).addClass('active');
    });
}

/* ============================================
   Payment Method Selection
   ============================================ */
function initPaymentMethods() {
    $('.payment-method').on('click', function() {
        $('.payment-method').removeClass('selected border-primary bg-light');
        $(this).addClass('selected border-primary bg-light');
        const method = $(this).data('method');
        $('#payment-method-input').val(method);
        
        if (method === 'mtn' || method === 'airtel') {
            $('#mobile-money-field').slideDown();
        } else {
            $('#mobile-money-field').slideUp();
        }
    });
}

/* ============================================
   Address Autocomplete
   ============================================ */
function initAddressAutocomplete() {
    $('#district-select').on('change', function() {
        const district = $(this).val();
        // Load cities for selected district
        $.ajax({
            url: '/api/address.php',
            method: 'GET',
            data: { district: district },
            success: function(response) {
                if (response.cities && response.cities.length) {
                    const citySelect = $('#city-select');
                    citySelect.empty();
                    citySelect.append('<option value="">Select City</option>');
                    response.cities.forEach(city => {
                        citySelect.append(`<option value="${city}">${city}</option>`);
                    });
                    citySelect.prop('disabled', false);
                }
            }
        });
    });
}

/* ============================================
   Export Functions
   ============================================ */
function exportToCSV(data, filename) {
    let csv = '';
    const headers = Object.keys(data[0]);
    csv += headers.join(',') + '\n';
    
    data.forEach(row => {
        const values = headers.map(header => {
            const value = row[header] || '';
            return `"${String(value).replace(/"/g, '""')}"`;
        });
        csv += values.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

/* ============================================
   Share Functions
   ============================================ */
function shareProduct(productName, productUrl) {
    if (navigator.share) {
        navigator.share({
            title: productName,
            text: 'Check out this product on EasyBuy Uganda!',
            url: productUrl
        }).catch(() => {
            copyToClipboard(productUrl);
        });
    } else {
        copyToClipboard(productUrl);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    showNotification('Link copied to clipboard!', 'success');
}

/* ============================================
   Print Functions
   ============================================ */
function printInvoice() {
    window.print();
}

/* ============================================
   Initialize on Page Load
   ============================================ */
$(document).ready(function() {
    // Initialize AOS animations if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    }
    
    // Initialize image gallery
    initImageGallery();
    
    // Initialize payment methods
    initPaymentMethods();
    
    // Initialize address autocomplete
    initAddressAutocomplete();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
});

/* ============================================
   Service Worker for PWA
   ============================================ */
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('ServiceWorker registration successful');
    }).catch(function(err) {
        console.log('ServiceWorker registration failed: ', err);
    });
}