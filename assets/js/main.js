// ===== User Menu Toggle =====
document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // ===== Wishlist Toggle =====
    document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const propertyId = this.dataset.propertyId;
            const icon = this.querySelector('i');

            fetch(SITE_URL + '/pages/toggle-wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'property_id=' + propertyId
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status === 'login_required') {
                    window.location.href = SITE_URL + '/pages/login.php';
                    return;
                }
                if (data.status === 'added') {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    btn.classList.add('active');
                } else {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                    btn.classList.remove('active');
                }
            });
        });
    });

    // ===== Category Filter =====
    document.querySelectorAll('.category-item').forEach(function(item) {
        item.addEventListener('click', function() {
            const categoryId = this.dataset.categoryId;
            window.location.href = SITE_URL + '/pages/search.php?category=' + categoryId;
        });
    });

    // ===== Booking Price Calculator =====
    const checkInInput = document.getElementById('booking-checkin');
    const checkOutInput = document.getElementById('booking-checkout');
    const pricePerNight = document.getElementById('price-per-night');

    if (checkInInput && checkOutInput && pricePerNight) {
        function calculatePrice() {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);

            if (checkIn && checkOut && checkOut > checkIn) {
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                const price = parseFloat(pricePerNight.value);
                const subtotal = nights * price;
                const serviceFee = Math.round(subtotal * 0.12);
                const total = subtotal + serviceFee;

                const breakdown = document.getElementById('price-breakdown');
                if (breakdown) {
                    breakdown.innerHTML =
                        '<div class="price-row">' +
                            '<span>₹' + price.toLocaleString('en-IN') + ' x ' + nights + ' night' + (nights > 1 ? 's' : '') + '</span>' +
                            '<span>₹' + subtotal.toLocaleString('en-IN') + '</span>' +
                        '</div>' +
                        '<div class="price-row">' +
                            '<span>Service fee</span>' +
                            '<span>₹' + serviceFee.toLocaleString('en-IN') + '</span>' +
                        '</div>' +
                        '<div class="price-row total">' +
                            '<span>Total</span>' +
                            '<span>₹' + total.toLocaleString('en-IN') + '</span>' +
                        '</div>';
                    breakdown.style.display = 'block';
                }
            }
        }

        checkInInput.addEventListener('change', calculatePrice);
        checkOutInput.addEventListener('change', calculatePrice);
    }

    // ===== Alerts auto-dismiss =====
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(function() { alert.remove(); }, 500);
        }, 4000);
    });

    // ===== Mobile search toggle =====
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            const searchBar = document.querySelector('.search-bar');
            if (searchBar) {
                searchBar.style.display = searchBar.style.display === 'block' ? 'none' : 'block';
            }
        });
    }
});

var SITE_URL = document.querySelector('.logo') ? document.querySelector('.logo').href.replace(/\/$/, '') : '';
