/* ============================================
   EasyBuy Uganda - Cart JavaScript
   Version: 2.0.0
   ============================================ */

$(document).ready(function() {
    initCartEvents();
    initCartSummary();
    initShippingCalculator();
});

function initCartEvents() {
    // Update quantity
    $(document).on('click', '.update-qty', function() {
        const cartId = $(this).data('cart-id');
        const change = parseInt($(this).data('change'));
        const qtyInput = $(this).closest('.quantity-selector').find('.cart-qty');
        let newQty = parseInt(qtyInput.val()) + change;
        
        if (newQty < 1) newQty = 1;
        
        updateCartQuantity(cartId, newQty);
    });
    
    // Manual quantity input
    $(document).on('change', '.cart-qty', function() {
        const cartId = $(this).data('cart-id');
        let newQty = parseInt($(this).val());
        
        if (isNaN(newQty) || newQty < 1) {
            newQty = 1;
            $(this).val(1);
        }
        
        updateCartQuantity(cartId, newQty);
    });
    
    // Remove item
    $(document).on('click', '.remove-item', function() {
        const cartId = $(this).data('cart-id');
        confirmDelete('Remove this item from cart?', function() {
            removeFromCart(cartId);
        });
    });
    
    // Clear cart
    $(document).on('click', '#clear-cart', function() {
        confirmDelete('Clear your entire cart? This action cannot be undone.', function() {
            clearCart();
        });
    });
    
    // Apply coupon
    $(document).on('click', '#apply-coupon', function() {
        applyCoupon();
    });
    
    // Remove coupon
    $(document).on('click', '#remove-coupon', function() {
        removeCoupon();
    });
}

function initCartSummary() {
    updateCartSummary();
}

function updateCartSummary() {
    let subtotal = 0;
    let itemCount = 0;
    
    $('.cart-item').each(function() {
        const price = parseFloat($(this).data('price'));
        const quantity = parseInt($(this).find('.cart-qty').val());
        const itemTotal = price * quantity;
        
        subtotal += itemTotal;
        itemCount += quantity;
        
        $(this).find('.item-total').text(formatCurrency(itemTotal));
    });
    
    const deliveryFee = calculateDeliveryFee(subtotal);
    const total = subtotal + deliveryFee;
    
    $('#cart-subtotal').text(formatCurrency(subtotal));
    $('#cart-delivery').text(formatCurrency(deliveryFee));
    $('#cart-total').text(formatCurrency(total));
    $('#cart-count').text(itemCount);
    
    if (subtotal >= 200000) {
        $('#free-delivery-badge').show();
    } else {
        $('#free-delivery-badge').hide();
        const remaining = 200000 - subtotal;
        $('#remaining-for-free').text(formatCurrency(remaining));
    }
}

function calculateDeliveryFee(subtotal) {
    if (subtotal >= 200000) {
        return 0;
    }
    
    const deliveryFee = 15000;
    return deliveryFee;
}

function updateCartQuantity(cartId, quantity) {
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
}

function clearCart() {
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
}

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

function initShippingCalculator() {
    $('#district-select').on('change', function() {
        const district = $(this).val();
        calculateShippingFee(district);
    });
}

function calculateShippingFee(district) {
    $.ajax({
        url: '/api/shipping.php',
        method: 'GET',
        data: { district: district },
        success: function(response) {
            if (response.success) {
                const deliveryFee = response.fee;
                $('#shipping-fee').text(formatCurrency(deliveryFee));
                updateCartTotal(deliveryFee);
            }
        }
    });
}

function updateCartTotal(deliveryFee) {
    const subtotal = parseFloat($('#cart-subtotal').data('value'));
    const total = subtotal + deliveryFee;
    $('#cart-total').text(formatCurrency(total));
}