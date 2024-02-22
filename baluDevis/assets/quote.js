window.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector('#table-search')
    
    search.addEventListener('input', function() {
        const searchTerm = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.quote-row');
    
        rows.forEach(row => {
            const name = row.querySelector('th').textContent.trim().toLowerCase();
            if (name.includes(searchTerm)) {
                row.style.display = '';
                row.classList.remove('opacity-0');
            } else {
                row.classList.add('opacity-0');
                setTimeout(() => {
                    row.style.display = 'none';
                }, 300);
            }
        });
    });
    
})