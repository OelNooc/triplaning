document.addEventListener('DOMContentLoaded', function() {
    const btnNuevoViaje = document.getElementById('btn-nuevo-viaje');
    const formNuevoViaje = document.getElementById('nuevo-viaje-form');
    
    if (btnNuevoViaje && formNuevoViaje) {
        btnNuevoViaje.addEventListener('click', function() {
            formNuevoViaje.classList.toggle('hidden');
        });
    }

    function actualizarDestinos() {
        const paisSelect = document.getElementById('pais-select');
        const destinoSelect = document.getElementById('destino-select');
        
        if (!paisSelect || !destinoSelect) return;
        
        const pais = paisSelect.value;
        destinoSelect.innerHTML = '<option value="">Seleccione un destino</option>';
        
        if (pais && window.destinosData) {
            const destinosFiltrados = window.destinosData.filter(d => d.pais === pais);
            destinosFiltrados.forEach(destino => {
                const option = document.createElement('option');
                option.value = destino.id;
                option.textContent = destino.nombre;
                destinoSelect.appendChild(option);
            });
        }
    }

    const paisSelect = document.getElementById('pais-select');
    if (paisSelect) {
        paisSelect.addEventListener('change', actualizarDestinos);
    }

    const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
    const fechaFin = document.querySelector('input[name="fecha_fin"]');
    
    if (fechaInicio && fechaFin) {
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && new Date(fechaFin.value) < new Date(this.value)) {
                alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                this.value = '';
            }
        });
        
        fechaFin.addEventListener('change', function() {
            if (fechaInicio.value && new Date(this.value) < new Date(fechaInicio.value)) {
                alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                this.value = '';
            }
        });
    }

    if (window.ultimoViaje) {
        function initializeMap() {
            // Verificar si la API de Google Maps está cargada
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                setTimeout(initializeMap, 100);
                return;
            }

            const mapaElement = document.getElementById('mapa');
            if (!mapaElement) return;

            const rutaContainer = document.createElement('div');
            rutaContainer.id = 'ruta-container';
            rutaContainer.className = 'mt-4 p-4 bg-gray-50 rounded-lg';
            mapaElement.parentNode.insertBefore(rutaContainer, mapaElement.nextSibling);

            const geocoder = new google.maps.Geocoder();
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();

            // Geocodificar el destino principal
            geocoder.geocode({ address: window.ultimoViaje.destino }, (ciudadResults, ciudadStatus) => {
                if (ciudadStatus !== 'OK' || !ciudadResults[0]) {
                    rutaContainer.innerHTML = '<p class="text-red-500">No se pudo encontrar la ubicación principal</p>';
                    return;
                }

                const ciudadLocation = ciudadResults[0].geometry.location;
                const mapa = new google.maps.Map(mapaElement, {
                    center: ciudadLocation,
                    zoom: 12
                });
                directionsRenderer.setMap(mapa);

                // Procesar actividades si existen
                if (window.ultimoViaje.actividades && window.ultimoViaje.actividades.length > 0) {
                    const geocodePromises = window.ultimoViaje.actividades.map(actividad => {
                        return new Promise((resolve) => {
                            geocoder.geocode({ 
                                address: `${actividad.nombre}, ${window.ultimoViaje.destino}` 
                            }, (results, status) => {
                                resolve(status === 'OK' && results[0] ? {
                                    location: results[0].geometry.location,
                                    nombre: actividad.nombre
                                } : null);
                            });
                        });
                    });

                    Promise.all(geocodePromises).then(locations => {
                        const waypoints = locations
                            .filter(loc => loc !== null)
                            .map(loc => ({
                                location: loc.location,
                                stopover: true
                            }));

                        if (waypoints.length === 0) {
                            rutaContainer.innerHTML = '<p class="text-yellow-500">No se pudieron ubicar las atracciones</p>';
                            return;
                        }

                        // Calcular y mostrar la ruta
                        directionsService.route({
                            origin: ciudadLocation,
                            destination: ciudadLocation,
                            waypoints: waypoints,
                            travelMode: google.maps.TravelMode.WALKING,
                            optimizeWaypoints: true
                        }, (response, status) => {
                            if (status === 'OK') {
                                directionsRenderer.setDirections(response);
                                
                                const waypointsParam = waypoints.map(w => 
                                    `${w.location.lat()},${w.location.lng()}`
                                ).join('|');
                                
                                const mapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${ciudadLocation.lat()},${ciudadLocation.lng()}&destination=${ciudadLocation.lat()},${ciudadLocation.lng()}&waypoints=${waypointsParam}&travelmode=walking`;
                                
                                rutaContainer.innerHTML = `
                                    <h4 class="font-medium text-gray-700 mb-2">Ruta del Viaje</h4>
                                    <a href="${mapsUrl}" target="_blank" class="text-blue-600 hover:underline">
                                        Ver ruta completa en Google Maps
                                    </a>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600">Atracciones incluidas:</p>
                                        <ul class="list-disc list-inside">
                                            ${waypoints.map((w, i) => 
                                                `<li>${window.ultimoViaje.actividades[i].nombre}</li>`
                                            ).join('')}
                                        </ul>
                                    </div>
                                `;
                            } else {
                                rutaContainer.innerHTML = '<p class="text-red-500">No se pudo calcular la ruta</p>';
                            }
                        });
                    });
                } else {
                    rutaContainer.innerHTML = '<p class="text-gray-500">No hay actividades para mostrar</p>';
                }
            });
        }

        function cargarClima() {
            const ciudad = encodeURIComponent(window.ultimoViaje.destino);
            const pais = encodeURIComponent(window.ultimoViaje.pais);
            const fechaInicio = new Date(window.ultimoViaje.fecha_inicio);
            const fechaFin = new Date(window.ultimoViaje.fecha_fin);

            const apiKey = window.OPEN_WEATHER_API_KEY
            
            axios.get(`https://api.openweathermap.org/data/2.5/forecast?q=${ciudad},${pais}&appid=${apiKey}&units=metric&lang=es`)
                .then(response => {
                    const climaContainer = document.getElementById('clima-container');
                    climaContainer.innerHTML = '';
                    
                    const pronosticos = response.data.list.filter(item => {
                        const fechaItem = new Date(item.dt * 1000);
                        return fechaItem >= fechaInicio && fechaItem <= fechaFin;
                    });
                    
                    if (pronosticos.length === 0) {
                        climaContainer.innerHTML = '<p class="text-gray-500">No hay datos de clima disponibles</p>';
                        return;
                    }
                    
                    pronosticos.forEach(pronostico => {
                        const fecha = new Date(pronostico.dt * 1000);
                        const dia = fecha.toLocaleDateString('es-ES', { weekday: 'short' });
                        const temp = Math.round(pronostico.main.temp);
                        const icono = pronostico.weather[0].icon;
                        
                        const elemento = document.createElement('div');
                        elemento.className = 'flex flex-col items-center px-3 py-2 bg-white rounded shadow';
                        elemento.innerHTML = `
                            <span class="font-medium">${dia}</span>
                            <img src="https://openweathermap.org/img/wn/${icono}.png" alt="${pronostico.weather[0].description}">
                            <span>${temp}°C</span>
                        `;
                        climaContainer.appendChild(elemento);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar clima:', error);
                    document.getElementById('clima-container').innerHTML = 
                        '<p class="text-gray-500">No se pudo cargar el pronóstico</p>';
                });
        }
        
        initializeMap();
        cargarClima();
    }
});