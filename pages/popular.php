<?php
session_start();
require_once '../config/db.php';

try {
    $stmt = $conn->prepare("
        SELECT d.id, d.nombre, d.pais, COUNT(i.id_destino) as visitas
        FROM itinerarios i
        JOIN destinos d ON i.id_destino = d.id
        GROUP BY i.id_destino
        ORDER BY visitas DESC
        LIMIT 5
    ");
    $stmt->execute();
    $destinosPopulares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $destinosPopulares = [];
    error_log("Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinos Populares - Triplaning</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/common_styles.css">
    <link rel="stylesheet" href="../assets/css/popular_style.css">    
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.8/glider.min.css">
</head>
<body class="bg-gray-50">
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center mb-8">Estos son los destinos más populares del momento</h1>
        
        <?php if (!empty($destinosPopulares)): ?>
            <div class="glider-container relative max-w-4xl mx-auto">
                <div class="glider">
                    <?php foreach ($destinosPopulares as $destino): 
                        $imagenNombre = strtolower(str_replace(' ', '_', $destino['nombre']));
                        $imagenPath = "../assets/img/{$imagenNombre}.png";
                    ?>
                        <div class="glider-slide p-4">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <?php if (file_exists($imagenPath)): ?>
                                    <img src="<?= $imagenPath ?>" alt="<?= htmlspecialchars($destino['nombre']) ?>" class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Imagen no disponible</span>
                                    </div>
                                <?php endif; ?>
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($destino['nombre']) ?></h3>
                                    <p class="text-gray-600"><?= htmlspecialchars($destino['pais']) ?></p>
                                    <p class="text-sm text-blue-600 mt-2"><?= $destino['visitas'] ?> viajes planificados</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button aria-label="Previous" class="glider-prev absolute left-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button aria-label="Next" class="glider-next absolute right-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                
                <div class="glider-dots text-center mt-4"></div>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">No hay datos de destinos populares disponibles</p>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.8/glider.min.js"></script>
    <script src="../assets/js/popular.js"></script>
</body>
</html>