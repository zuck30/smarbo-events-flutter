// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Handle logout
    const logoutLinks = document.querySelectorAll('a[href*="logout"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                fetch(this.href)
                    .then(() => window.location.href = '../index.php');
            }
        });
    });
    
    // Update dashboard stats periodically
    function updateStats() {
        fetch('../api/stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats on the page
                    document.querySelectorAll('.stat-value').forEach(el => {
                        const statLabel = el.nextElementSibling.textContent;
                        if (statLabel.includes('Events')) {
                            el.textContent = data.data.total_events;
                        } else if (statLabel.includes('Owners')) {
                            el.textContent = data.data.total_owners;
                        } else if (statLabel.includes('Promised')) {
                            el.textContent = formatCurrency(data.data.total_promised);
                        } else if (statLabel.includes('Paid')) {
                            el.textContent = formatCurrency(data.data.total_paid);
                        }
                    });
                }
            });
    }
    
    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }
    
    // Update stats every 30 seconds
    setInterval(updateStats, 30000);
    
    // Handle modals
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        });
    });
    
    // Close modals
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
        
        if (e.target.classList.contains('modal-close')) {
            e.target.closest('.modal').style.display = 'none';
        }
    });
    
    // Handle form submissions with AJAX
    const forms = document.querySelectorAll('form[data-ajax]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.action;
            const method = this.method;
            
            fetch(action, {
                method: method,
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('An error occurred. Please try again.', 'error');
            });
        });
    });
    
    // Show alert function
    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '1000';
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
    
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('mobile-show');
        });
    }
});