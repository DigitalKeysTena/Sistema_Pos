<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/conection.php';

/* -------------------------------------------------------------
   AJAX: OBTENER DESCRIPCIONES FILTRADAS POR CATEGORÍA
------------------------------------------------------------- */
if ($_GET['ajax'] ?? '' === "descripciones") {

    header('Content-Type: application/json; charset=utf-8');

    $cat = $_GET['cat'] ?? null;

    if (!$cat) {
        echo json_encode([]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT Id_Descripcion_Categoria, Descrip_Categoria
            FROM descripcion_categoria
            WHERE Id_Categoria = ?
            ORDER BY Descrip_Categoria ASC
        ");
        $stmt->execute([$cat]);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]);
    }

    exit;
}

/* -------------------------------------------------------------
   CARGAR CATEGORÍAS
------------------------------------------------------------- */
$categorias = $pdo->query("
    SELECT Id_Categoria, Tipo_Categoria
    FROM categoria
    ORDER BY Tipo_Categoria ASC
")->fetchAll(PDO::FETCH_ASSOC);
