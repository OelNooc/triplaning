document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let actividades = [];
    let totalGastado = 0;
    
    // Elementos del DOM
    const formActividades = document.getElementById('form-actividades');
    const actividadesContainer = document.getElementById('actividades-container');
    const btnAddActividad = document.getElementById('btn-add-actividad');
    const btnConfirmar = document.getElementById('btn-confirmar');
    const categoriaSelect = document.getElementById('categoria-select');
    const atraccionSelect = document.getElementById('atraccion-select');
    const totalActividades = document.getElementById('total-actividades');
    const presupuestoRestante = document.getElementById('presupuesto-restante');
    
    // Inicialización
    if (categoriaSelect) {
        categoriaSelect.addEventListener('change', actualizarAtracciones);
    }
    
    if (btnAddActividad) {
        btnAddActividad.addEventListener('click', agregarActividad);
    }
    
    //Actualizar las atracciones según categoría seleccionada
    function actualizarAtracciones() {
        const categoriaId = categoriaSelect.value;
        
        atraccionSelect.innerHTML = '<option value="">Seleccione una atracción</option>';
        atraccionSelect.disabled = !categoriaId;
        
        if (window.atraccionesData) {
            // Filtrar atracciones
            const atraccionesFiltradas = categoriaId === '' 
                ? window.atraccionesData 
                : window.atraccionesData.filter(a => a.categoria === categoriaSelect.options[categoriaSelect.selectedIndex].text);
                        
            if (atraccionesFiltradas.length === 0) {
                const msg = categoriaId === '' 
                    ? 'No hay atracciones disponibles para este destino' 
                    : 'No hay atracciones en esta categoría';
                atraccionSelect.innerHTML = `<option value="">${msg}</option>`;
            } else {
                atraccionesFiltradas.forEach(atraccion => {
                    const option = document.createElement('option');
                    option.value = atraccion.id;
                    option.textContent = `${atraccion.nombre} ($${atraccion.costo})`;
                    option.dataset.costo = atraccion.costo;
                    atraccionSelect.appendChild(option);
                });
            }
        }
    }
    
    function agregarActividad() {
        const fecha = document.getElementById('actividad-fecha').value;
        const hora = document.getElementById('actividad-hora').value;
        const atraccionId = atraccionSelect.value;
        const atraccionNombre = atraccionSelect.options[atraccionSelect.selectedIndex].text.split(' (')[0];
        const atraccionCosto = parseFloat(atraccionSelect.options[atraccionSelect.selectedIndex].dataset.costo || 0);
        const categoriaNombre = categoriaSelect.options[categoriaSelect.selectedIndex].text;
        
        if (!fecha || !hora || !atraccionId) {
            alert('Complete todos los campos de la actividad');
            return;
        }
        
        const nuevoTotal = totalGastado + atraccionCosto;
        if (nuevoTotal > window.presupuestoMaximo) {
            alert(`No puedes superar tu presupuesto ${window.presupuestoNivel} (máximo $${window.presupuestoMaximo})`);
            return;
        }
        
        // Crear objeto actividad
        const actividad = {
            id_atraccion: atraccionId,
            fecha: fecha,
            hora: hora,
            nombre: atraccionNombre,
            categoria: categoriaNombre,
            costo: atraccionCosto
        };
        
        actividades.push(actividad);
        totalGastado = nuevoTotal;
        renderizarActividades();
        actualizarResumen();
        
        // Habilitar botón de confirmar
        if (actividades.length > 0) {
            btnConfirmar.disabled = false;
        }
    }
    
    function renderizarActividades() {
        actividadesContainer.innerHTML = '';
        
        if (actividades.length === 0) {
            actividadesContainer.innerHTML = '<p class="text-gray-500">No hay actividades añadidas aún.</p>';
            return;
        }
        
        const table = document.createElement('table');
        table.className = 'min-w-full bg-white rounded-lg overflow-hidden';
        
        // Cabecera
        const thead = document.createElement('thead');
        thead.className = 'bg-gray-100';
        thead.innerHTML = `
            <tr>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Hora</th>
                <th class="px-4 py-2 text-left">Actividad</th>
                <th class="px-4 py-2 text-left">Categoría</th>
                <th class="px-4 py-2 text-left">Costo</th>
                <th class="px-4 py-2 text-left">Acciones</th>
            </tr>
        `;
        table.appendChild(thead);
        
        // Cuerpo
        const tbody = document.createElement('tbody');
        actividades.forEach((act, index) => {
            const tr = document.createElement('tr');
            tr.className = 'border-t';
            tr.innerHTML = `
                <td class="px-4 py-2">${act.fecha}</td>
                <td class="px-4 py-2">${act.hora}</td>
                <td class="px-4 py-2">${act.nombre}</td>
                <td class="px-4 py-2">${act.categoria}</td>
                <td class="px-4 py-2">$${act.costo.toFixed(2)}</td>
                <td class="px-4 py-2">
                    <button 
                        type="button" 
                        onclick="eliminarActividad(${index})"
                        class="text-red-500 hover:text-red-700"
                    >
                        Eliminar
                    </button>
                </td>
                <input type="hidden" name="actividades[${index}][id_atraccion]" value="${act.id_atraccion}">
                <input type="hidden" name="actividades[${index}][fecha]" value="${act.fecha}">
                <input type="hidden" name="actividades[${index}][hora]" value="${act.hora}">
            `;
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        actividadesContainer.appendChild(table);
    }
    
    function actualizarResumen() {
        totalActividades.textContent = `$${totalGastado.toFixed(2)}`;
        
        const restante = window.presupuestoMaximo - totalGastado;
        presupuestoRestante.textContent = `Presupuesto restante: $${restante.toFixed(2)}`;
        
        if (restante < 0) {
            presupuestoRestante.classList.add('text-red-600');
            presupuestoRestante.classList.remove('text-gray-600');
        } else {
            presupuestoRestante.classList.add('text-gray-600');
            presupuestoRestante.classList.remove('text-red-600');
        }
    }
    
    window.eliminarActividad = function(index) {
        totalGastado -= actividades[index].costo;
        actividades.splice(index, 1);
        renderizarActividades();
        actualizarResumen();
        
        if (actividades.length === 0) {
            btnConfirmar.disabled = true;
        }
    };
    
    // Validación del formulario antes de enviar
    if (formActividades) {
        formActividades.addEventListener('submit', function(e) {
            if (actividades.length === 0) {
                e.preventDefault();
                alert('Debe añadir al menos una actividad al itinerario');
            }
        });
    }
});