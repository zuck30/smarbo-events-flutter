// Owner Dashboard JavaScript

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
    
    // Handle modal forms
    const modalForms = document.querySelectorAll('form[data-ajax]');
    modalForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const apiEndpoint = this.id === 'addContributionForm' ? '../api/contributions.php' : '../api/invitations.php';
            
            fetch(apiEndpoint, {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        });
    });
    
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
    
    // Update statistics
    function updateStats() {
        fetch('../api/stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update owner stats
                    if (data.data.total_events !== undefined) {
                        const eventElements = document.querySelectorAll('.stat-value');
                        eventElements.forEach(el => {
                            const parentText = el.closest('.stat-card').textContent;
                            if (parentText.includes('Events')) {
                                el.textContent = data.data.total_events;
                            } else if (parentText.includes('Promised')) {
                                el.textContent = formatCurrency(data.data.contributions?.total_promised || 0);
                            } else if (parentText.includes('Paid')) {
                                el.textContent = formatCurrency(data.data.contributions?.total_paid || 0);
                            }
                        });
                    }
                }
            });
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }
    
    // Update stats every 30 seconds
    setInterval(updateStats, 30000);
});