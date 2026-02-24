// Ecommerce JavaScript
const BASE_URL = window.location.pathname.includes('/views/') ? '../../' : './';

// Toast Notification
function showToast(message, type = 'success') {
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        </div>
        <div class="toast-message">${message}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add to Cart (AJAX)
function addToCart(productId, quantity = 1) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'controllers/cart-controller.php?action=add', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Response:', xhr.responseText);
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.status === 'success') {
                    showToast('Product added to cart successfully!');
                    document.querySelectorAll('.cart-count').forEach(function(el) {
                        el.textContent = data.cart_count;
                    });
                } else {
                    showToast(data.message || 'Error adding to cart', 'error');
                }
            } catch(e) {
                showToast('Error: ' + xhr.responseText, 'error');
            }
        }
    };
    xhr.send('product_id=' + productId + '&quantity=' + quantity);
}

// Update Cart Count
function updateCartCount() {
    fetch(BASE_URL + 'controllers/cart-controller.php?action=count')
    .then(response => response.json())
    .then(data => {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(el => {
            el.textContent = data.count;
        });
    });
}

// Remove from Cart
function removeFromCart(cartItemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    fetch(BASE_URL + 'controllers/cart-controller.php?action=remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_item_id=${cartItemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('<strong>Success!</strong> Item removed from cart');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('<strong>Error!</strong> ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('<strong>Error!</strong> Failed to remove item', 'error');
    });
}

// Update Cart Quantity
function updateCartQuantity(cartItemId, quantity) {
    fetch(BASE_URL + 'controllers/cart-controller.php?action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_item_id=${cartItemId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            showToast(data.message, 'error');
        }
    });
}

// Add to Wishlist
function addToWishlist(productId) {
    fetch(BASE_URL + 'controllers/wishlist-controller.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('<strong>Success!</strong> Added to wishlist');
        } else if (data.status === 'warning') {
            showToast('<strong>Info!</strong> ' + data.message, 'error');
        } else {
            showToast('<strong>Error!</strong> ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('<strong>Error!</strong> Failed to add to wishlist', 'error');
    });
}

// Remove from Wishlist
function removeFromWishlist(productId) {
    fetch('controllers/wishlist-controller.php?action=remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            location.reload();
        }
    });
}

// Quick View Modal
function quickView(productId) {
    const modal = document.getElementById('quickViewModal');
    const modalContent = modal.querySelector('.modal-content');
    
    modal.style.display = 'block';
    modalContent.classList.add('loading');
    
    fetch(BASE_URL + `controllers/product-controller.php?action=quick-view&id=${productId}`)
    .then(response => response.json())
    .then(data => {
        modalContent.classList.remove('loading');
        document.getElementById('quickViewContent').innerHTML = data.html;
    })
    .catch(error => {
        showToast('<strong>Error!</strong> Failed to load product', 'error');
    });
}

// Close Modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Countdown Timer
function startCountdown(endDate, elementId) {
    const countdown = document.getElementById(elementId);
    if (!countdown) return;
    
    function update() {
        const now = new Date().getTime();
        const distance = new Date(endDate).getTime() - now;
        
        if (distance < 0) {
            countdown.innerHTML = 'Expired';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        countdown.innerHTML = `<span>${days}d</span><span>${hours}h</span><span>${minutes}m</span><span>${seconds}s</span>`;
    }
    
    setInterval(update, 1000);
    update();
}

// Product Tabs
function filterProducts(category) {
    const buttons = document.querySelectorAll('.product-tabs button');
    const products = document.querySelectorAll('.products-grid .product-card');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    products.forEach(product => {
        if (category === 'all' || product.dataset.category === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Search Form Submit
document.querySelector('.search-box')?.addEventListener('submit', function(e) {
    const searchInput = this.querySelector('input[type="text"]');
    if (!searchInput.value.trim()) {
        e.preventDefault();
        showToast('Please enter a search term', 'error');
    }
});

// Quantity Input Handler
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function() {
        const cartItemId = this.dataset.cartItemId;
        const quantity = parseInt(this.value);
        if (quantity > 0) {
            updateCartQuantity(cartItemId, quantity);
        }
    });
});

// Coupon Validation
function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value;
    if (!couponCode) {
        showToast('Please enter a coupon code', 'error');
        return;
    }
    
    fetch(BASE_URL + 'controllers/coupon-controller.php?action=validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `code=${couponCode}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            showToast(`Coupon applied! You save $${data.discount}`);
            document.getElementById('discountAmount').textContent = `$${data.discount}`;
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('couponCode').disabled = true;
        } else {
            showToast(data.message, 'error');
        }
    });
}

// Checkout Form Validation
document.querySelector('.checkout-form')?.addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let valid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            valid = false;
        } else {
            field.style.borderColor = '';
        }
    });
    
    if (!valid) {
        e.preventDefault();
        showToast('Please fill in all required fields', 'error');
    }
});

// Close modals on outside click
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Mobile Menu Toggle
function toggleMobileMenu() {
    document.querySelector('.nav-menu')?.classList.toggle('active');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-countdown]').forEach(el => {
        startCountdown(el.dataset.countdown, el.id);
    });
    
    updateCartCount();
});
