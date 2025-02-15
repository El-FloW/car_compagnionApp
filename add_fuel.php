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
    die("Vous devez être connecté pour ajouter un ravitaillement.");
}

$user_id = $_SESSION["user_id"];

// Récupérer les véhicules de l'utilisateur
$stmt = $pdo->prepare("SELECT id, car_name FROM ca_car WHERE fk_user = ?");
$stmt->execute([$user_id]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fk_car_id = intval($_POST["fk_car_id"]);
    $litre = intval($_POST["litre"]);
    $prix = intval($_POST["prix"]);
    $isFull = isset($_POST["isFull"]) ? 1 : 0;
    $carburant = $_POST["carburant"];
    $odometre = intval($_POST["odometre"]);
    $date = $_POST["date"];

    // Vérifier que tous les champs sont remplis
    if ($fk_car_id > 0 && $litre > 0 && $prix > 0 && $odometre >= 0 && !empty($carburant) && !empty($date)) {
        // Insérer le ravitaillement dans la base de données
        $stmt = $pdo->prepare("INSERT INTO ca_ravitaillement (fk_car_id, litre, prix, isFull, carburant, odometre, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fk_car_id, $litre, $prix, $isFull, $carburant, $odometre, $date]);

        echo "✅ Ravitaillement ajouté avec succès !";
    } else {
        echo "❌ Merci de remplir tous les champs correctement.";
    }
}
?>
<section>
    <h1>Ajouter un ravitaillement</h1>
    <a href="index.php">Retour</a>
</section>
<section class="form">
    <form method="post">
        <label>Véhicule :</label>
        <select name="fk_car_id" required>
            <option value="">Sélectionnez un véhicule</option>
            <?php foreach ($cars as $car): ?>
                <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['car_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Litres :</label>
        <input type="number" name="litre" min="1" required>

        <label>Prix total (€) :</label>
        <input type="number" name="prix" min="1" required>

        <label>Plein fait ?</label>
        <input type="checkbox" name="isFull">

        <label>Type de carburant :</label>
        <select name="carburant" required>
            <option value="SP95">SP95</option>
            <option value="SP98">SP98</option>
            <option value="E85">E85</option>
            <option value="Diesel">Diesel</option>
        </select>

        <label>Compteur kilométrique :</label>
        <input type="number" name="odometre" min="0" required>

        <label>Date et heure :</label>
        <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i') ?>" required>
</section>
<section class="submitSection">
    <button type="submit" class="submit">Ajouter</button>
</section>
</form>
