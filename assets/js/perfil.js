document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-eliminar-viaje');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de eliminar este viaje?')) {
                    e.preventDefault();
                }
            });
        });
    }

    const pagination = document.querySelector('.pagination-container');
    if (pagination && window.innerWidth < 640) {
        pagination.classList.add('flex-wrap', 'justify-center');
        pagination.classList.remove('justify-between');
    }
});