function setActiveLink() {
    // Get current page path
    const path = window.location.pathname;
    let currentPage = path.split('/').pop() || 'index.html';

    // Remove .html if present to handle both cases
    currentPage = currentPage.replace(/\.html$/, '');

    // Map page URLs to data-page values
    const pageMap = {
        'index': 'home',
        '': 'home',
        'service': 'service',
        'projects': 'projects',
        'about': 'about',
        'blog': 'blog',
        'contact-us': 'contact',
        'ai-automation': 'ai-automation'
    };

    // Get the current page identifier
    const pageId = pageMap[currentPage] || '';

    if (pageId) {
        // Remove active class from all links first
        document.querySelectorAll('[data-page]').forEach(link => {
            link.classList.remove('active');
        });

        // Add active class to matching links
        const activeLinks = document.querySelectorAll(`[data-page="${pageId}"]`);
        activeLinks.forEach(link => link.classList.add('active'));
    }
}
// Load header and then set active link
fetch('/./shared/header.html')
    .then(res => res.text())
    .then(data => {
        document.getElementById('header').innerHTML = data;
        setActiveLink(); // highlight active nav item (optional)

        // Scroll effect for header
        const header = document.getElementById('mainHeader');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 0) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // ✅ Add dropdown toggle functionality AFTER header is loaded
        document.querySelectorAll('.menu__dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.parentElement.classList.toggle('open');
            });
        });
    })
    .catch(error => console.error('Error loading header:', error));

// Load review
fetch('./shared/review.html')
    .then(res => res.text())
    .then(data => {
        document.getElementById('review').innerHTML = data;

        // Now that the content is loaded, initialize Swiper
        new Swiper('.reviewSwiper', {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            // autoplay: {
            //   delay: 3000,
            //   disableOnInteraction: false,
            // },
            navigation: {
                nextEl: '.review__right',
                prevEl: '.review__left',
            },
            pagination: {
                el: '.review__dots',
                clickable: true,
                type: 'bullets', // Ensures bullet-style pagination
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    });

// Load chat
fetch('/./shared/chat.html')
    .then(res => res.text())
    .then(data => document.getElementById('chat').innerHTML = data);

// Load footer
fetch('/./shared/footer.html')
    .then(res => res.text())
    .then(data => {
        document.getElementById('footer').innerHTML = data;

        // Manually add Tawk.to script if it didn't execute
        if (typeof Tawk_API === 'undefined') {
            var s1 = document.createElement('script');
            s1.async = true;
            s1.src = 'https://embed.tawk.to/659edc748d261e1b5f51a2a8/1hjq8qa2u';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            document.body.appendChild(s1);
        }
    });

// Load contact form HTML
fetch('./shared/contact.html')
    .then(res => res.text())
    .then(data => {
        document.getElementById('contact').innerHTML = data;
        initContactForm();
    });

function initContactForm() {
    const steps = document.querySelectorAll('.contact-step');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const form = document.getElementById('multiStepForm');
    const contactBtnWrapper = document.querySelector('.contact__btn');
    const submitBtnOriginalHTML = nextBtn.innerHTML;

    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = i === index ? 'block' : 'none';
        });

        if (index === steps.length - 1) {
            // Last step → hide buttons
            contactBtnWrapper.style.display = 'none';
        } else {
            contactBtnWrapper.style.display = 'block';
            prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
            nextBtn.innerHTML = index === steps.length - 2
                ? '<span>Submit</span>'
                : '<span>Next</span><img src="./assets/img/icons/right-arrow.svg" alt="">';
            nextBtn.style.display = 'inline-block';
        }
    }

    prevBtn.addEventListener('click', function () {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentStep === steps.length - 2) {
            // Show loader
            nextBtn.innerHTML = '<span class="loader"></span>';
            nextBtn.disabled = true;

            // Collect form data
            const formData = new FormData(form);

            fetch('sendmail.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.text())
                .then(result => {
                    console.log("Form submitted:", result);

                    if (result.includes("success")) {
                        currentStep++;
                        showStep(currentStep);
                    } else {
                        alert("Submission failed: " + result);
                        nextBtn.innerHTML = submitBtnOriginalHTML;
                        nextBtn.disabled = false;
                    }
                })
                .catch(err => {
                    alert("Submission failed. Try again.");
                    console.error(err);
                    nextBtn.innerHTML = submitBtnOriginalHTML;
                    nextBtn.disabled = false;
                });
        } else if (currentStep < steps.length - 2) {
            currentStep++;
            showStep(currentStep);
        }
    });

    showStep(currentStep);
}


// Initialize Lenis
const lenis = new Lenis();

// Use requestAnimationFrame to continuously update the scroll
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);



// Load contact form HTML
// fetch('./shared/contact.html')
//     .then(res => res.text())
//     .then(data => {
//         document.getElementById('contact').innerHTML = data;

//         // Run after HTML is injected
//         initContactForm();
//     });

// function initContactForm() {
//     const steps = document.querySelectorAll('.contact-step');
//     const prevBtn = document.getElementById('prevBtn');
//     const nextBtn = document.getElementById('nextBtn');
//     const form = document.getElementById('multiStepForm');
//     const contactBtnWrapper = document.querySelector('.contact__btn');
//     const submitBtnOriginalHTML = nextBtn.innerHTML; // Store original button HTML

//     let currentStep = 0;

//     function showStep(index) {
//         steps.forEach((step, i) => {
//             step.style.display = i === index ? 'block' : 'none';
//         });

//         if (index === steps.length - 1) {
//             contactBtnWrapper.style.display = 'none';
//         } else {
//             contactBtnWrapper.style.display = 'block';
//             prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
//             nextBtn.innerHTML = index === steps.length - 2
//                 ? '<span>Submit</span>'
//                 : '<span>Next</span><img src="./assets/img/icons/right-arrow.svg" alt="">';
//             nextBtn.style.display = 'inline-block';
//         }
//     }

//     prevBtn.addEventListener('click', function () {
//         if (currentStep > 0) {
//             currentStep--;
//             showStep(currentStep);
//         }
//     });

//     nextBtn.addEventListener('click', function () {
//         if (currentStep === steps.length - 2) {
//             // Show loader inside button
//             nextBtn.innerHTML = '<span class="loader"></span>';
//             nextBtn.disabled = true;

//             // Submit form data to Google Sheets
//             const formData = new FormData(form);
//             const data = {};
//             formData.forEach((value, key) => data[key] = value);

//             fetch('https://script.google.com/macros/s/AKfycbxdyHsQbfCGLHNp1YY5l5vcKkDx9PBUL6vO7UzqBmBS1kuH3hu8igNinjcpLygh_wbOOw/exec', {
//                 method: 'POST',
//                 body: JSON.stringify(data),
//             })
//                 .then(res => res.json())
//                 .then(result => {
//                     console.log("Form submitted:", result);
//                     currentStep++;
//                     showStep(currentStep);
//                 })
//                 .catch(err => {
//                     alert("Submission failed. Try again.");
//                     console.error(err);
//                     // Restore original button state
//                     nextBtn.innerHTML = submitBtnOriginalHTML;
//                     nextBtn.disabled = false;
//                 });
//         } else if (currentStep < steps.length - 2) {
//             currentStep++;
//             showStep(currentStep);
//         }
//     });

//     showStep(currentStep);
// }
