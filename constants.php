<?php
/**
 * EasyBuy Uganda - Constants Configuration
 * Version: 2.0.0
 */

// ============================================
// USER ROLES
// ============================================
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');
define('ROLE_VENDOR', 'vendor');

// ============================================
// ORDER STATUSES
// ============================================
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_CONFIRMED', 'confirmed');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_OUT_FOR_DELIVERY', 'out_for_delivery');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');
define('ORDER_STATUS_REFUNDED', 'refunded');

// ============================================
// PAYMENT STATUSES
// ============================================
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PROCESSING', 'processing');
define('PAYMENT_STATUS_COMPLETED', 'completed');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// ============================================
// PAYMENT METHODS
// ============================================
define('PAYMENT_METHOD_MTN', 'mtn');
define('PAYMENT_METHOD_AIRTEL', 'airtel');

// ============================================
// PRODUCT STATUSES
// ============================================
define('PRODUCT_STATUS_ACTIVE', 'active');
define('PRODUCT_STATUS_INACTIVE', 'inactive');
define('PRODUCT_STATUS_PENDING', 'pending');

// ============================================
// COUPON TYPES
// ============================================
define('COUPON_TYPE_PERCENTAGE', 'percentage');
define('COUPON_TYPE_FIXED', 'fixed');

// ============================================
// REVIEW STATUSES
// ============================================
define('REVIEW_STATUS_PENDING', 'pending');
define('REVIEW_STATUS_APPROVED', 'approved');
define('REVIEW_STATUS_REJECTED', 'rejected');

// ============================================
// BLOG POST STATUSES
// ============================================
define('BLOG_STATUS_DRAFT', 'draft');
define('BLOG_STATUS_PUBLISHED', 'published');
define('BLOG_STATUS_ARCHIVED', 'archived');

// ============================================
// SUPPORT TICKET PRIORITIES
// ============================================
define('TICKET_PRIORITY_LOW', 'low');
define('TICKET_PRIORITY_MEDIUM', 'medium');
define('TICKET_PRIORITY_HIGH', 'high');
define('TICKET_PRIORITY_URGENT', 'urgent');

// ============================================
// SUPPORT TICKET STATUSES
// ============================================
define('TICKET_STATUS_OPEN', 'open');
define('TICKET_STATUS_IN_PROGRESS', 'in_progress');
define('TICKET_STATUS_WAITING', 'waiting');
define('TICKET_STATUS_CLOSED', 'closed');

// ============================================
// VENDOR STATUSES
// ============================================
define('VENDOR_STATUS_PENDING', 'pending');
define('VENDOR_STATUS_ACTIVE', 'active');
define('VENDOR_STATUS_SUSPENDED', 'suspended');

// ============================================
// AFFILIATE STATUSES
// ============================================
define('AFFILIATE_STATUS_ACTIVE', 'active');
define('AFFILIATE_STATUS_INACTIVE', 'inactive');
define('AFFILIATE_STATUS_SUSPENDED', 'suspended');

// ============================================
// GIFT CARD STATUSES
// ============================================
define('GIFT_CARD_ACTIVE', 'active');
define('GIFT_CARD_USED', 'used');
define('GIFT_CARD_EXPIRED', 'expired');
define('GIFT_CARD_CANCELLED', 'cancelled');

// ============================================
// FLASH SALE STATUSES
// ============================================
define('FLASH_SALE_ACTIVE', 'active');
define('FLASH_SALE_INACTIVE', 'inactive');
define('FLASH_SALE_EXPIRED', 'expired');

// ============================================
// NEWSLETTER STATUSES
// ============================================
define('NEWSLETTER_ACTIVE', 'active');
define('NEWSLETTER_UNSUBSCRIBED', 'unsubscribed');
define('NEWSLETTER_BOUNCED', 'bounced');