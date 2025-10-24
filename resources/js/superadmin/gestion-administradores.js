document.addEventListener('DOMContentLoaded', () => {
    console.log('Gestion de administradores cargado correctamente.');
    const searchInput = document.querySelector('#search');
    const tableContainer = document.querySelector('#table-container');
    let timeout;

    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            fetch(`{{ route('superadmin.admins.index') }}?q=${encodeURIComponent(query)}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(data => tableContainer.innerHTML = data.html)
            .catch(console.error);
        }, 300);
    });
});