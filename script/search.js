// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Search functionality initializing...');
    
    const searchForm = document.getElementById('navbar-search-form');
    const searchInput = document.getElementById('search-input');
    
    if (!searchForm || !searchInput) {
        console.error('Search form elements not found!');
        return;
    }
    
    let searchTimeout;

    // Create search results dropdown
    const searchResults = document.createElement('div');
    searchResults.className = 'search-results-dropdown';
    searchResults.style.display = 'none';
    searchForm.appendChild(searchResults);

    // Handle search input
    searchInput.addEventListener('input', function() {
        console.log('Search input changed:', this.value);
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        // Show loading state
        searchResults.innerHTML = '<div class="search-loading">Searching courses...</div>';
        searchResults.style.display = 'block';

        // Debounce search
        searchTimeout = setTimeout(() => {
            console.log('Executing search for:', query);
            searchCourses(query);
        }, 300);
    });

    // Handle form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            console.log('Form submitted with query:', query);
            window.location.href = `?content=Courses&search=${encodeURIComponent(query)}`;
        }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    console.log('Search functionality initialized successfully');

    // Initialize cart
    loadCart();
});

// Load cart contents
async function loadCart() {
    try {
        const response = await fetch('data/cart_operations.php?action=get_cart');
        const data = await response.json();
        
        if (data.success) {
            updateCartCount(data.cart.length);
            updateCartDisplay(data.cart);
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

// Update cart count in navbar
function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        // Add animation class
        cartCount.classList.add('cart-count-update');
        setTimeout(() => {
            cartCount.classList.remove('cart-count-update');
        }, 300);
    }
}

// Update cart display
function updateCartDisplay(cartItems) {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    
    if (!cartItemsContainer || !cartTotal) return;
    
    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Your cart is empty</p>
            </div>
        `;
        cartTotal.textContent = '0.00';
        return;
    }

    let total = 0;
    cartItemsContainer.innerHTML = '';

    cartItems.forEach(item => {
        total += item.price;
        cartItemsContainer.innerHTML += `
            <div class="cart-item d-flex align-items-center p-3 border-bottom">
                <img src="${item.image}" alt="${item.title}" class="cart-item-image me-3" 
                     onerror="this.src='https://placehold.co/50x50?text=${encodeURIComponent(item.title)}'">
                <div class="cart-item-info flex-grow-1">
                    <h6 class="cart-item-title mb-1">${item.title}</h6>
                    <div class="cart-item-price text-primary">£${item.price.toFixed(2)}</div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });

    cartTotal.textContent = total.toFixed(2);
}

// Remove from cart
async function removeFromCart(courseId) {
    try {
        const response = await fetch('data/cart_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                course_id: courseId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadCart(); // Reload cart after removing item
        } else {
            throw new Error(data.error || 'Failed to remove item from cart');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        alert('Failed to remove item from cart. Please try again.');
    }
}

// Search courses function
async function searchCourses(query) {
    console.log('Searching courses with query:', query);
    try {
        const url = `data/search_courses.php?q=${encodeURIComponent(query)}`;
        console.log('Fetching from URL:', url);
        
        const response = await fetch(url);
        console.log('Search response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Search response data:', data);

        const searchResults = document.querySelector('.search-results-dropdown');
        if (!searchResults) {
            console.error('Search results container not found!');
            return;
        }
        
        if (!data.success) {
            console.error('Search failed:', data.error);
            searchResults.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-circle"></i><p>Error searching courses</p></div>';
            return;
        }

        if (data.courses.length === 0) {
            console.log('No courses found');
            searchResults.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><p>No courses found</p></div>';
            return;
        }

        console.log('Found courses:', data.courses.length);
        
        // Display search results with highlighted matching text
        searchResults.innerHTML = `
            <div class="search-results-content">
                ${data.courses.map(course => {
                    // Highlight matching text in title and description
                    const title = highlightMatch(course.title, query);
                    const description = highlightMatch(course.description.substring(0, 100) + '...', query);
                    
                    return `
                        <div class="search-result-item" onclick="window.location.href='?content=Courses&course=${course.id}'">
                            <img src="${course.image}" alt="${course.title}" onerror="this.src='https://placehold.co/50x50?text=${encodeURIComponent(course.title)}'">
                            <div class="course-info">
                                <div class="course-title">${title}</div>
                                <div class="course-description">${description}</div>
                                <div class="course-price">£${course.price.toFixed(2)}</div>
                            </div>
                            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(${course.id}, this)">
                                <i class="fas fa-cart-plus"></i> Add
                            </button>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    } catch (error) {
        console.error('Error in searchCourses:', error);
        const searchResults = document.querySelector('.search-results-dropdown');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error searching courses: ${error.message}</p>
                </div>
            `;
        }
    }
}

// Helper function to highlight matching text
function highlightMatch(text, query) {
    if (!query) return text;
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

// Add to cart function
async function addToCart(courseId, button) {
    try {
        // Disable button and show loading state
        button.disabled = true;
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

        const response = await fetch('data/cart_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                course_id: courseId
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Reload cart to update both count and contents
            await loadCart();
            
            // Show success message
            const searchResults = document.querySelector('.search-results-dropdown');
            const successMessage = document.createElement('div');
            successMessage.className = 'alert alert-success m-2';
            
            // Check if course was added or already in cart
            if (data.message === 'Course already in cart') {
                successMessage.innerHTML = '<i class="fas fa-info-circle me-2"></i>Course is already in your cart';
                button.innerHTML = '<i class="fas fa-check"></i> In Cart';
                button.classList.add('in-cart');
            } else {
                successMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>Course added to cart!';
                button.innerHTML = '<i class="fas fa-check"></i> Added';
                button.classList.add('added-to-cart');
            }
            
            searchResults.insertBefore(successMessage, searchResults.firstChild);
            
            // Remove success message after 2 seconds
            setTimeout(() => {
                successMessage.remove();
            }, 2000);

            // Reset button after 1 second if it was just added
            if (data.message !== 'Course already in cart') {
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    button.classList.remove('added-to-cart');
                }, 1000);
            }
        } else {
            throw new Error(data.error || 'Failed to add course to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        // Show error message
        const searchResults = document.querySelector('.search-results-dropdown');
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-danger m-2';
        errorMessage.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Failed to add course to cart. Please try again.';
        searchResults.insertBefore(errorMessage, searchResults.firstChild);
        
        // Reset button
        button.innerHTML = originalContent;
        button.disabled = false;
        
        // Remove error message after 2 seconds
        setTimeout(() => {
            errorMessage.remove();
        }, 2000);
    }
} 