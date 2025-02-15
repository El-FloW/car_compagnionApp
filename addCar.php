<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Compagnion App</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/main.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/cadre.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/floatingButton.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/form.css'>
    <script src='main.js'></script>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
</head>

<?php
session_start();
require "config.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    die("❌ Vous devez être connecté pour ajouter un véhicule.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_name = trim($_POST["car_name"]);
    $user_id = $_SESSION["user_id"];

    if (!empty($car_name) && $kilometrage >= 0) {
        // Insérer le véhicule dans la base de données
        $stmt = $pdo->prepare("INSERT INTO ca_car (fk_user, car_name) VALUES (?, ?)");
        $stmt->execute([$user_id, $car_name]);

        echo "✅ Véhicule ajouté avec succès !";
    } else {
        echo "❌ Merci de remplir tous les champs correctement.";
    }
}
?>
<section>
    <h1>Ajouter un véhicule</h1>
</section>
<section class="form">
    <form method="post">
        <label>Nom du véhicule :</label>
        <input type="text" name="car_name" required>
</section>
<section class="submitSection">
    <button type="submit" class="submit">Ajouter</button>
</section>
</form>
