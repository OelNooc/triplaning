<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT nombre, email FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $viajesPorPagina = 5;
    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($paginaActual - 1) * $viajesPorPagina;
    
    $stmt = $conn->prepare("
        SELECT i.id, d.nombre AS destino, i.fecha_inicio, i.fecha_termino
        FROM itinerarios i
        JOIN destinos d ON i.id_destino = d.id
        WHERE i.id_usuario = :user_id
        ORDER BY i.fecha_inicio DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindValue(':limit', $viajesPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM itinerarios WHERE id_usuario = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $totalViajes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPaginas = ceil($totalViajes / $viajesPorPagina);

} catch(PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Triplaning</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8 px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Mi Perfil</h1>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Información Personal</h2>
                <div class="mt-2">
                    <p class="text-gray-600"><span class="font-medium">Nombre:</span> <?= htmlspecialchars($user['nombre']) ?></p>
                    <p class="text-gray-600"><span class="font-medium">Email:</span> <?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
            
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Mis Viajes</h2>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($viajes)): ?>
                                <?php foreach ($viajes as $viaje): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($viaje['destino']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?= date('d/m/Y', strtotime($viaje['fecha_inicio'])) ?> - 
                                                <?= date('d/m/Y', strtotime($viaje['fecha_termino'])) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($viaje['destino']) ?>" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver en Maps
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No tienes viajes registrados aún</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPaginas > 1): ?>
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-gray-700">
                        Mostrando <?= count($viajes) ?> de <?= $totalViajes ?> viajes
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($paginaActual > 1): ?>
                            <a href="?pagina=<?= $paginaActual - 1 ?>" class="px-3 py-1 border rounded text-sm font-medium hover:bg-gray-50">
                                Anterior
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <a href="?pagina=<?= $i ?>" class="px-3 py-1 border rounded text-sm font-medium <?= $i == $paginaActual ? 'bg-blue-50 text-blue-600 border-blue-300' : 'hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <a href="?pagina=<?= $paginaActual + 1 ?>" class="px-3 py-1 border rounded text-sm font-medium hover:bg-gray-50">
                                Siguiente
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/perfil.js"></script>
</body>
</html>