document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-contacto');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            //alert personalizado
            const alertDiv = document.createElement('div');
            alertDiv.className = 'custom-alert fixed inset-0 flex items-center justify-center z-50';
            alertDiv.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm mx-auto border border-gray-200">
                    <p class="text-lg font-medium text-gray-800 mb-4">Mensaje enviado, se responderá a la brevedad</p>
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                        Aceptar
                    </button>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Cerrar alert
            const closeAlert = function() {
                document.body.removeChild(alertDiv);
            };
            
            alertDiv.querySelector('button').addEventListener('click', closeAlert);
            alertDiv.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAlert();
                }
            });
        });
    }
});