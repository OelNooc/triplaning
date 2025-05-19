<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['nuevo_viaje'])) {
    header("Location: mis_viajes.php");
    exit();
}

try {
    $stmt = $conn->prepare("SELECT nombre, pais FROM destinos WHERE id = :destino_id");
    $stmt->bindParam(':destino_id', $_SESSION['nuevo_viaje']['id_destino']);
    $stmt->execute();
    $destino = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $conn->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("
        SELECT a.id, a.nombre, a.costo, c.nombre AS categoria
        FROM atracciones a
        JOIN categorias c ON a.id_categoria = c.id
        WHERE a.id_destino = :destino_id
        ORDER BY c.nombre, a.nombre
    ");
    $stmt->bindParam(':destino_id', $_SESSION['nuevo_viaje']['id_destino']);
    $stmt->execute();
    $atracciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_viaje'])) {
        
        $stmt = $conn->prepare("
            INSERT INTO itinerarios 
            (id_usuario, id_destino, fecha_inicio, fecha_termino, presupuesto)
            VALUES (:user_id, :destino_id, :fecha_inicio, :fecha_termino, :presupuesto)
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':destino_id', $_SESSION['nuevo_viaje']['id_destino']);
        $stmt->bindParam(':fecha_inicio', $_SESSION['nuevo_viaje']['fecha_inicio']);
        $stmt->bindParam(':fecha_termino', $_SESSION['nuevo_viaje']['fecha_termino']);
        $stmt->bindParam(':presupuesto', $_SESSION['nuevo_viaje']['presupuesto']);
        $stmt->execute();
        
        $itinerario_id = $conn->lastInsertId();
        
        if (!empty($_POST['actividades'])) {
            foreach ($_POST['actividades'] as $actividad) {
                $stmt = $conn->prepare("
                    INSERT INTO itinerario_detalle
                    (id_itinerario, id_atraccion, fecha, hora)
                    VALUES (:itinerario_id, :atraccion_id, :fecha, :hora)
                ");
                $stmt->bindParam(':itinerario_id', $itinerario_id);
                $stmt->bindParam(':atraccion_id', $actividad['id_atraccion']);
                $stmt->bindParam(':fecha', $actividad['fecha']);
                $stmt->bindParam(':hora', $actividad['hora']);
                $stmt->execute();
            }
        }
        
        unset($_SESSION['nuevo_viaje']);
        header("Location: mis_viajes.php?success=1");
        exit();
    }
    
} catch(PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificar Actividades - Triplaning</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="../assets/css/common_styles.css">
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Planificar Actividades en <?= htmlspecialchars($destino['nombre']) ?></h2>
            <p class="text-gray-600 mb-4">
                Fechas: <?= htmlspecialchars($_SESSION['nuevo_viaje']['fecha_inicio']) ?> 
                al <?= htmlspecialchars($_SESSION['nuevo_viaje']['fecha_termino']) ?>
            </p>
            <p class="text-gray-600 mb-6">
                Presupuesto: <?= htmlspecialchars($_SESSION['nuevo_viaje']['presupuesto']) ?>
            </p>
            
            <form method="POST" action="" id="form-actividades">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Añadir Actividades</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Fecha</label>
                            <input type="date" id="actividad-fecha" class="w-full p-2 border rounded" 
                                   min="<?= $_SESSION['nuevo_viaje']['fecha_inicio'] ?>" 
                                   max="<?= $_SESSION['nuevo_viaje']['fecha_termino'] ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Hora</label>
                            <input type="time" id="actividad-hora" class="w-full p-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Categoría</label>
                            <select id="categoria-select" class="w-full p-2 border rounded">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Atracción</label>
                            <select id="atraccion-select" class="w-full p-2 border rounded" disabled>
                                <option value="">Seleccione categoría</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="button" id="btn-add-actividad" 
                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        Añadir Actividad
                    </button>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Actividades Planificadas</h3>
                    <div id="actividades-container" class="mb-4">
                        <p class="text-gray-500">No hay actividades añadidas aún.</p>
                    </div>
                    
                    <div id="resumen-presupuesto" class="bg-gray-50 p-3 rounded-lg">
                        <p class="font-medium">Total estimado: <span id="total-actividades">$0.00</span></p>
                        <p class="text-sm text-gray-600" id="presupuesto-restante"></p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="mis_viajes.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">
                        Cancelar
                    </a>
                    <button type="submit" name="confirmar_viaje" id="btn-confirmar"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition disabled:opacity-50"
                            disabled>
                        Confirmar Viaje
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        window.atraccionesData = <?= json_encode($atracciones) ?>;
        window.presupuestoNivel = "<?= $_SESSION['nuevo_viaje']['presupuesto'] ?>";
        
        const presupuestos = {
            'Económico': 500,
            'Medio': 1000,
            'Alto': 2000
        };
        window.presupuestoMaximo = presupuestos[window.presupuestoNivel] || 1000;
    </script>
    <script src="../assets/js/itinerario_detalle.js"></script>
</body>
</html>