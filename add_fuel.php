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

<h2>Ajouter un ravitaillement</h2>
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

    <label>Date :</label>
    <input type="date" name="date" required>

    <button type="submit">Ajouter</button>
</form>
