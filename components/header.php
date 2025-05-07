<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand" href="../index.html"><img src="https://placehold.co/150x50/3c5fe2/FFFFFF?text=LearnPac" alt="Logo" style="width: 150px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'about-us.html') ? 'active' : ''; ?>" 
             href="../html/about-us.html">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'Services.php') ? 'active' : ''; ?>" 
             href="../html/Services.php">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../index.html#courses">Courses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contact">Contact</a>
        </li>
        <li class="nav-item">
          <form class="d-flex align-items-center position-relative" id="navbar-search-form" autocomplete="off" style="margin-right: 8px;">
            <input
              type="text"
              class="form-control search-input"
              placeholder="Search courses..."
              aria-label="Search"
              id="search-input"
              style="width: 0; transition: width 0.4s cubic-bezier(0.4,0,0.2,1); padding-right: 32px; overflow: hidden; border-radius: 20px;"
              onfocus="this.style.width='200px';"
              onblur="if(this.value===''){this.style.width='0';}"
            />
            <button type="submit" class="btn btn-link position-absolute end-0 top-50 translate-middle-y px-2" style="color: #198754;">
              <i class="fas fa-search"></i>
            </button>
          </form>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-primary me-2" href="https://app.learnpac.co.uk/log-in/" target="_blank">Login</a>
        </li>
        <li class="nav-item position-relative">
          <button class="btn btn-link position-relative cart-btn">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav> 