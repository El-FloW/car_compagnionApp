<?php
session_start();
require "config.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    die("❌ Vous devez être connecté pour ajouter un véhicule.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_name = trim($_POST["car_name"]);
    $kilometrage = intval($_POST["kilometrage"]);
    $user_id = $_SESSION["user_id"];

    if (!empty($car_name) && $kilometrage >= 0) {
        // Insérer le véhicule dans la base de données
        $stmt = $pdo->prepare("INSERT INTO ca_car (fk_user, car_name, kilometrage) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $car_name, $kilometrage]);

        echo "✅ Véhicule ajouté avec succès !";
    } else {
        echo "❌ Merci de remplir tous les champs correctement.";
    }
}
?>

<h2>Ajouter un véhicule</h2>
<form method="post">
    <label>Nom du véhicule :</label>
    <input type="text" name="car_name" required>

    <label>Kilométrage :</label>
    <input type="number" name="kilometrage" min="0" required>

    <button type="submit">Ajouter</button>
</form>
