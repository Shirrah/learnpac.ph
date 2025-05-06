document.addEventListener('DOMContentLoaded', function () {
  const cartBtn = document.querySelector('.cart-btn');
  const cartCount = document.querySelector('.cart-count');
  let count = 0;

  function updateCartCount(newCount) {
    count = newCount;
    cartCount.textContent = count;
    cartBtn.classList.add('item-added');
    setTimeout(() => {
      cartBtn.classList.remove('item-added');
    }, 300);
  }

  document.querySelectorAll('.cta-btn').forEach(btn => {
    if (btn.textContent.includes('Browse Courses')) {
      btn.addEventListener('click', () => {
        updateCartCount(count + 1);
      });
    }
  });

  // Expanding search bar logic
  const searchInput = document.querySelector('.search-input');
  const searchForm = document.getElementById('navbar-search-form');
  if (searchInput) {
    searchInput.addEventListener('focus', function () {
      searchInput.classList.add('expanded');
    });
    searchInput.addEventListener('blur', function () {
      if (!searchInput.value) {
        searchInput.classList.remove('expanded');
      }
    });
    searchInput.addEventListener('click', function () {
      searchInput.classList.add('expanded');
      searchInput.focus();
    });
  }
});