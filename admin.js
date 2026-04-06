/* ============================================
   EasyBuy Uganda - Admin JavaScript
   Version: 2.0.0
   ============================================ */

$(document).ready(function() {
    initAdminDashboard();
    initDataTables();
    initCharts();
    initImageUpload();
    initProductManagement();
    initOrderManagement();
    initUserManagement();
    initSettingsForm();
});

function initAdminDashboard() {
    // Load dashboard stats
    loadDashboardStats();
    
    // Refresh data every 30 seconds
    setInterval(loadDashboardStats, 30000);
}

function loadDashboardStats() {
    $.ajax({
        url: '/admin/api/dashboard.php',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateStats(response.data);
                updateCharts(response.data);
            }
        }
    });
}

function updateStats(data) {
    $('#total-orders').text(formatNumber(data.total_orders));
    $('#total-users').text(formatNumber(data.total_users));
    $('#total-products').text(formatNumber(data.total_products));
    $('#total-revenue').text(formatCurrency(data.total_revenue));
    $('#pending-orders').text(formatNumber(data.pending_orders));
    $('#low-stock').text(formatNumber(data.low_stock));
}

function initDataTables() {
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
}

function initCharts() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (salesCtx) {
        window.salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales (UGX)',
                    data: [],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Orders',
                    data: [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart')?.getContext('2d');
    if (paymentCtx) {
        window.paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['MTN Mobile Money', 'Airtel Money'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['#f59e0b', '#2563eb']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Category Sales Chart
    const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (categoryCtx) {
        window.categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales by Category',
                    data: [],
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }
}

function updateCharts(data) {
    if (window.salesChart && data.sales_data) {
        window.salesChart.data.labels = data.sales_data.dates;
        window.salesChart.data.datasets[0].data = data.sales_data.amounts;
        window.salesChart.data.datasets[1].data = data.sales_data.counts;
        window.salesChart.update();
    }
    
    if (window.paymentChart && data.payment_data) {
        window.paymentChart.data.datasets[0].data = [
            data.payment_data.mtn,
            data.payment_data.airtel
        ];
        window.paymentChart.update();
    }
    
    if (window.categoryChart && data.category_data) {
        window.categoryChart.data.labels = data.category_data.labels;
        window.categoryChart.data.datasets[0].data = data.category_data.values;
        window.categoryChart.update();
    }
}

function initImageUpload() {
    $('.image-upload').on('change', function() {
        const input = this;
        const preview = $(this).siblings('.image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
}

function initProductManagement() {
    // Add product
    $('#add-product-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/products.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Product added successfully!', 'success');
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error adding product', 'error');
            }
        });
    });
    
    // Update product
    $(document).on('submit', '#edit-product-form', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/products.php',
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Product updated successfully!', 'success');
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error updating product', 'error');
            }
        });
    });
    
    // Delete product
    $(document).on('click', '.delete-product', function() {
        const productId = $(this).data('id');
        
        confirmDelete('Delete this product? This action cannot be undone.', function() {
            showLoading();
            
            $.ajax({
                url: '/admin/api/products.php',
                method: 'DELETE',
                data: { id: productId },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        showNotification('Product deleted', 'success');
                        location.reload();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('Error deleting product', 'error');
                }
            });
        });
    });
}

function initOrderManagement() {
    // Update order status
    $(document).on('change', '.order-status-select', function() {
        const orderId = $(this).data('id');
        const status = $(this).val();
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/orders.php',
            method: 'PUT',
            data: {
                id: orderId,
                status: status
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Order status updated', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error updating order', 'error');
            }
        });
    });
    
    // Update payment status
    $(document).on('change', '.payment-status-select', function() {
        const orderId = $(this).data('id');
        const status = $(this).val();
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/orders.php',
            method: 'PUT',
            data: {
                id: orderId,
                payment_status: status
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Payment status updated', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error updating payment', 'error');
            }
        });
    });
}

function initUserManagement() {
    // Update user role
    $(document).on('change', '.user-role-select', function() {
        const userId = $(this).data('id');
        const role = $(this).val();
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/users.php',
            method: 'PUT',
            data: {
                id: userId,
                role: role
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('User role updated', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error updating user', 'error');
            }
        });
    });
    
    // Delete user
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        
        confirmDelete('Delete this user? This action cannot be undone.', function() {
            showLoading();
            
            $.ajax({
                url: '/admin/api/users.php',
                method: 'DELETE',
                data: { id: userId },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        showNotification('User deleted', 'success');
                        location.reload();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('Error deleting user', 'error');
                }
            });
        });
    });
}

function initSettingsForm() {
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        showLoading();
        
        $.ajax({
            url: '/admin/api/settings.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Settings saved successfully!', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error saving settings', 'error');
            }
        });
    });
}

function exportData(type) {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    
    window.location.href = `/admin/export.php?type=${type}&start=${startDate}&end=${endDate}`;
}

function backupDatabase() {
    confirmDelete('Create a database backup?', function() {
        showLoading();
        
        $.ajax({
            url: '/admin/api/backup.php',
            method: 'POST',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Backup created successfully!', 'success');
                    window.location.href = response.download_url;
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error creating backup', 'error');
            }
        });
    });
}

function clearCache() {
    confirmDelete('Clear system cache? This may temporarily slow down the site.', function() {
        showLoading();
        
        $.ajax({
            url: '/admin/api/cache.php',
            method: 'DELETE',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification('Cache cleared successfully!', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error clearing cache', 'error');
            }
        });
    });
}

function formatNumber(number) {
    return new Intl.NumberFormat('en-US').format(number);
}

function formatCurrency(amount) {
    return 'UGX ' + formatNumber(amount);
}