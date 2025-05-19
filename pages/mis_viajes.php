<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $show_login_prompt = true;
} else {
    $show_login_prompt = false;
    
    try {
        $stmt = $conn->prepare("
            SELECT i.id, d.nombre AS destino, i.fecha_inicio, i.fecha_termino, i.presupuesto 
            FROM itinerarios i
            JOIN destinos d ON i.id_destino = d.id
            WHERE i.id_usuario = :user_id
            ORDER BY i.id DESC
            LIMIT 1
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $ultimo_viaje = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $actividades = [];
        if ($ultimo_viaje) {
            $stmt = $conn->prepare("
                SELECT a.id, a.nombre, a.costo, id.fecha, id.hora
                FROM itinerario_detalle id
                JOIN atracciones a ON id.id_atraccion = a.id
                WHERE id.id_itinerario = :itinerario_id
                ORDER BY id.fecha, id.hora
            ");
            $stmt->bindParam(':itinerario_id', $ultimo_viaje['id']);
            $stmt->execute();
            $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $stmt = $conn->query("SELECT id, nombre, pais FROM destinos ORDER BY pais, nombre");
        $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paso1'])) {
        $_SESSION['nuevo_viaje'] = [
            'id_destino' => $_POST['destino'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_termino' => $_POST['fecha_termino'],
            'presupuesto' => $_POST['presupuesto']
        ];
        header("Location: itinerario_detalle.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Viajes - Triplaning</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="../assets/css/common_styles.css">
    <link rel="stylesheet" href="../assets/css/mis_viajes.css">
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
    <script>
        window.OPEN_WEATHER_API_KEY = '<?php echo $_ENV['OPEN_WEATHER_API_KEY'] ?? ''; ?>';
    </script>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8 px-4">
        <?php if ($show_login_prompt): ?>
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 text-center">
                <h2 class="text-xl font-bold text-gray-800 mb-4">No has iniciado sesión</h2>
                <p class="text-gray-600 mb-6">Debes iniciar sesión para acceder a tus viajes</p>
                <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Iniciar Sesión
                </a>
            </div>
        <?php else: ?>
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Mis Viajes</h1>
                <button 
                    id="btn-nuevo-viaje"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
                >
                    + Nuevo Viaje
                </button>
            </div>
            
        <div id="nuevo-viaje-form" class="hidden mb-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Planificar Nuevo Viaje</h2>
            <form method="POST" action="">
                <input type="hidden" name="paso1" value="1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2">País</label>
                        <select id="pais-select" class="w-full p-2 border rounded">
                            <option value="">Seleccione un país</option>
                            <?php
                            $paises = [];
                            foreach ($destinos as $destino) {
                                if (!in_array($destino['pais'], $paises)) {
                                    $paises[] = $destino['pais'];
                                    echo "<option value='{$destino['pais']}'>{$destino['pais']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Destino</label>
                        <select id="destino-select" name="destino" class="w-full p-2 border rounded" required>
                            <option value="">Primero seleccione un país</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" class="w-full p-2 border rounded" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Fecha de Termino</label>
                        <input type="date" name="fecha_termino" class="w-full p-2 border rounded" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Presupuesto</label>
                        <select name="presupuesto" class="w-full p-2 border rounded" required>
                            <option value="">Seleccione</option>
                            <option value="Económico">Económico</option>
                            <option value="Medio">Medio</option>
                            <option value="Alto">Alto</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Continuar
                </button>
            </form>
        </div>
            
            <?php if ($ultimo_viaje): ?>
            <h2 class="text-xl font-bold mb-4">Mi Último Viaje</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="md:w-1/3">
                            <?php 
                            $imagen_url = "../assets/img/" . strtolower(str_replace(' ', '_', $ultimo_viaje['destino'])) . ".png";
                            if (file_exists($imagen_url)): ?>
                                <img src="<?= $imagen_url ?>" alt="<?= $ultimo_viaje['destino'] ?>" class="w-full h-48 object-cover rounded-lg">
                            <?php else: ?>
                                <div class="bg-gray-200 w-full h-48 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-500">Imagen no disponible</span>
                                </div>
                            <?php endif; ?>
                            <h3 class="text-lg font-semibold mt-2"><?= htmlspecialchars($ultimo_viaje['destino']) ?></h3>
                            <p class="text-gray-600"><?= htmlspecialchars($ultimo_viaje['pais']) ?></p>
                        </div>
                        
                        <div class="md:w-2/3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <h4 class="font-medium text-gray-700">Fecha Inicio</h4>
                                    <p><?= htmlspecialchars($ultimo_viaje['fecha_inicio']) ?></p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700">Fecha Fin</h4>
                                    <p><?= htmlspecialchars($ultimo_viaje['fecha_termino']) ?></p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700">Presupuesto</h4>
                                    <p><?= htmlspecialchars($ultimo_viaje['presupuesto']) ?></p>
                                </div>
                            </div>
                            
                            <div id="mapa" class="h-48 bg-gray-200 mb-4 rounded-lg flex items-center justify-center">
                                <p class="text-gray-500">Mapa se cargará aquí</p>
                            </div>
                            
                            <div id="clima" class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Pronóstico del Clima</h4>
                                <div class="flex space-x-4 overflow-x-auto py-2" id="clima-container">
                                    <p class="text-gray-500">Cargando pronóstico...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="font-medium text-gray-700 mt-6 mb-2">Actividades Planificadas</h4>
                    <?php if (!empty($actividades)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Fecha</th>
                                        <th class="px-4 py-2 text-left">Hora</th>
                                        <th class="px-4 py-2 text-left">Actividad</th>
                                        <th class="px-4 py-2 text-left">Costo Aprox.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actividades as $actividad): ?>
                                        <tr class="border-t">
                                            <td class="px-4 py-2"><?= htmlspecialchars($actividad['fecha']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($actividad['hora']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($actividad['nombre']) ?></td>
                                            <td class="px-4 py-2">$<?= number_format($actividad['costo_aproximado'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">No hay actividades planificadas para este viaje.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No tienes viajes registrados aún.</p>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        window.destinosData = <?= json_encode($destinos) ?>;
        window.ultimoViaje = <?= json_encode(array_merge(
            $ultimo_viaje ?? [],
            ['actividades' => $actividades ?? []]
        )) ?>;
    </script>
    <script src="../assets/js/mis_viajes.js"></script>
    <?php if (isset($ultimo_viaje) && $ultimo_viaje): ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD68XHXg0mD5TjKaau8sfo0qaXZYW7gTsw&libraries=places,geometry" async defer></script>
    <?php endif; ?>
    
</body>
</html>
<?php endif; ?>