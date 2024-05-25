<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order']) && is_array($_POST['order'])) {
        $order = $_POST['order'];
        //$conn = db_connect(); // Assurez-vous que db_connect() est défini et que $conn est une connexion valide

        foreach ($order as $weight => $id) {
            $stmt = $conn->prepare("UPDATE notes SET weight = ? WHERE id = ?");
            $stmt->bind_param("ii", $weight, $id);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

