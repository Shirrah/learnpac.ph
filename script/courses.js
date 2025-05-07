// Courses data
const coursesData = {
  courses: [
    {
      "id": "HC001",
      "title": "Healthcare Assistant Training",
      "category": "Healthcare",
      "badge": "primary",
      "duration": "8 weeks",
      "price": 299,
      "image": "https://placehold.co/600x400?text=Healthcare+Assistant",
      "description": "Essential skills and knowledge for healthcare assistants, including patient care, safety protocols, and professional development.",
      "modules": [
        "Introduction to Healthcare",
        "Patient Care Fundamentals",
        "Safety and Hygiene",
        "Communication Skills",
        "Professional Development"
      ],
      "features": [
        "Accredited certification",
        "24/7 access to materials",
        "Expert support",
        "Practical assessments",
        "Career guidance"
      ]
    },
    {
      "id": "SC001",
      "title": "Social Care Management",
      "category": "Social Care",
      "badge": "success",
      "duration": "6 weeks",
      "price": 399,
      "image": "https://placehold.co/600x400?text=Social+Care",
      "description": "Advanced management skills for social care professionals, including leadership, policy implementation, and team management.",
      "modules": [
        "Leadership in Social Care",
        "Policy and Compliance",
        "Team Management",
        "Resource Planning",
        "Quality Assurance"
      ],
      "features": [
        "Professional certification",
        "Flexible learning schedule",
        "Industry expert support",
        "Case studies",
        "Management toolkit"
      ]
    },
    {
      "id": "MH001",
      "title": "Mental Health First Aid",
      "category": "Mental Health",
      "badge": "info",
      "duration": "10 weeks",
      "price": 349,
      "image": "https://placehold.co/600x400?text=Mental+Health",
      "description": "Comprehensive training in mental health awareness, crisis intervention, and support strategies for healthcare professionals.",
      "modules": [
        "Mental Health Awareness",
        "Crisis Intervention",
        "Support Strategies",
        "Self-Care",
        "Professional Boundaries"
      ],
      "features": [
        "Certified qualification",
        "Interactive learning",
        "Expert guidance",
        "Practical scenarios",
        "Ongoing support"
      ]
    }
  ],
  features: [
    {
      "id": "certification",
      "name": "Accredited Certification",
      "icon": "certificate",
      "description": "All courses provide recognized industry certifications"
    },
    {
      "id": "support",
      "name": "24/7 Support",
      "icon": "headset",
      "description": "Round-the-clock access to expert support and guidance"
    },
    {
      "id": "flexible",
      "name": "Flexible Learning",
      "icon": "laptop",
      "description": "Learn at your own pace with 24/7 course access"
    },
    {
      "id": "practical",
      "name": "Practical Training",
      "icon": "hands-helping",
      "description": "Hands-on experience with real-world scenarios"
    }
  ],
  categories: [
    {
      "id": "healthcare",
      "name": "Healthcare",
      "badge": "primary",
      "description": "Essential healthcare training programs for medical professionals"
    },
    {
      "id": "social-care",
      "name": "Social Care",
      "badge": "success",
      "description": "Comprehensive social care and support training courses"
    },
    {
      "id": "mental-health",
      "name": "Mental Health",
      "badge": "info",
      "description": "Specialized mental health and wellbeing training programs"
    }
  ]
};

// Function to display courses
function displayCourses(courses) {
  const coursesContainer = document.getElementById('coursesContainer');
  if (!coursesContainer) {
    console.error('Courses container not found');
    return;
  }

  coursesContainer.innerHTML = courses.map(course => `
    <div class="col-lg-4 col-md-6">
      <div class="card h-100 course-card">
        <img src="${course.image}" class="card-img-top" alt="${course.title}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-${course.badge}">${course.category}</span>
            <span class="text-muted"><i class="fas fa-clock"></i> ${course.duration}</span>
          </div>
          <h5 class="card-title">${course.title}</h5>
          <p class="card-text">${course.description}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span class="h5 mb-0">£${course.price}</span>
            <button class="btn btn-outline-primary" onclick="showCourseDetails('${course.id}')">Learn More</button>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

// Function to display features
function displayFeatures(features) {
  const featuresContainer = document.getElementById('featuresContainer');
  if (!featuresContainer) return;

  featuresContainer.innerHTML = features.map(feature => `
    <div class="col-md-3">
      <div class="feature-card text-center p-4">
        <div class="feature-icon mb-3">
          <i class="fas fa-${feature.icon} fa-3x text-primary"></i>
        </div>
        <h4>${feature.name}</h4>
        <p class="text-muted">${feature.description}</p>
      </div>
    </div>
  `).join('');
}

// Function to display categories
function displayCategories(categories) {
  const categoriesContainer = document.getElementById('categoriesContainer');
  if (!categoriesContainer) return;

  categoriesContainer.innerHTML = categories.map(category => `
    <div class="col-md-4">
      <div class="testimonial-card p-4 bg-white rounded shadow-sm">
        <div class="feature-icon mb-3">
          <i class="fas fa-graduation-cap fa-3x text-${category.badge}"></i>
        </div>
        <h4>${category.name}</h4>
        <p class="text-muted">${category.description}</p>
        <a href="#" class="btn btn-outline-${category.badge} mt-3" onclick="filterByCategory('${category.name}')">View Courses</a>
      </div>
    </div>
  `).join('');
}

// Show course details in modal
function showCourseDetails(courseId) {
  const course = coursesData.courses.find(c => c.id === courseId);
  if (!course) {
    console.error('Course not found:', courseId);
    return;
  }

  document.getElementById('courseModalTitle').textContent = course.title;
  document.getElementById('courseModalImage').src = course.image;
  document.getElementById('courseModalDescription').textContent = course.description;
  
  document.getElementById('courseModalModules').innerHTML = course.modules
    .map(module => `<li>${module}</li>`).join('');
  
  document.getElementById('courseModalFeatures').innerHTML = course.features
    .map(feature => `<li>${feature}</li>`).join('');
  
  document.getElementById('courseModalEnroll').href = `#enroll-${course.id}`;
  
  new bootstrap.Modal(document.getElementById('courseModal')).show();
}

// Filter courses by category
function filterByCategory(category) {
  const filteredCourses = category === 'All Categories'
    ? coursesData.courses
    : coursesData.courses.filter(course => course.category === category);
  displayCourses(filteredCourses);
}

// Initialize courses page
function initializeCoursesPage() {
  console.log('Initializing courses page');
  displayCourses(coursesData.courses);
  displayFeatures(coursesData.features);
  displayCategories(coursesData.categories);

  // Setup search
  const searchInput = document.getElementById('courseSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const filteredCourses = coursesData.courses.filter(course => 
        course.title.toLowerCase().includes(searchTerm) || 
        course.description.toLowerCase().includes(searchTerm) || 
        course.category.toLowerCase().includes(searchTerm)
      );
      displayCourses(filteredCourses);
    });
  }

  // Setup category filter
  const categoryFilter = document.getElementById('categoryFilter');
  if (categoryFilter) {
    categoryFilter.addEventListener('change', function(e) {
      filterByCategory(e.target.value);
    });
  }
}

// Export the initialization function
window.initializeCoursesPage = initializeCoursesPage; 