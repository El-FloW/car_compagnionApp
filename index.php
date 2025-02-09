<!DOCTYPE html>
<?php
    error_reporting(0);
    session_start();
    require "config.php"; // Connexion à la base de données
?>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Compagnion App</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/main.css'>
    <script src='main.js'></script>
</head>
<body>
    <?php
    if($_SESSION["user_id"]) :
    ?>
    <section>
        <h2>Bienvenue</h2>
        <p>Bienvenue <?php echo $_SESSION['user_name']?></p>
        <a href="disconnect.php">déconnexion</a>
    </section>
    <?php
    else :
    ?>
    <section>
        <h2>Connecte toi !</h2>
        <a href="./login.php">Connexion</a>
    </section>
    <?php
    endif;
    ?>
    
    <?php
    $user_id = $_SESSION["user_id"];

    // Récupérer les voitures de l'utilisateur
    $stmt = $pdo->prepare("SELECT id, car_name, kilometrage FROM ca_car WHERE fk_user = ?");
    $stmt->execute([$user_id]);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<section>";
    echo "<h2>Mes véhicules</h2>";

    if (empty($cars)) {
        echo "<a href='./addCar.php'>Ajouter un véhicule</a>";
    } else {
        foreach ($cars as $car) {
            echo "<strong>Voiture :</strong> " . htmlspecialchars($car["car_name"]) . 
                " | <strong>Kilométrage :</strong> " . htmlspecialchars($car["kilometrage"]) . " km";
        }
    }
    echo "</section>";
    ?>

    <section class="halfSection">
<!--Récupérer les 5 derniers ravitaillements de l'utilisateur -->
    <?php
        
        $stmt = $pdo->prepare("
            SELECT f.id, f.litre, f.prix, f.isFull, f.carburant, f.odometre, f.date, c.car_name
            FROM ca_ravitaillement f
            JOIN ca_car c ON f.fk_car_id = c.id
            WHERE c.fk_user = ?
            ORDER BY f.date DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $fuels = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<section>";
        echo "<h2>⛽ Mes 5 derniers ravitaillements</h2>";

        if (empty($fuels)) {
            echo "<p>Vous n'avez enregistré aucun ravitaillement pour le moment.</p>";
        } else {
            foreach ($fuels as $fuel) {
                echo "<strong>Voiture :</strong> " . htmlspecialchars($fuel["car_name"]) . "<br>
                    ⛽ <strong>Carburant :</strong> " . htmlspecialchars($fuel["carburant"]) . "<br>
                    🛢️ <strong>Litres :</strong> " . htmlspecialchars($fuel["litre"]) . " L<br>
                    💰 <strong>Prix :</strong> " . htmlspecialchars($fuel["prix"]) . " €<br>
                    📅 <strong>Date :</strong> " . htmlspecialchars($fuel["date"]) . "<br>
                    🔄 <strong>Kilométrage :</strong> " . htmlspecialchars($fuel["odometre"]) . " km<br>
                    " . ($fuel["isFull"] ? "✅ Plein effectué" : "❌ Pas un plein complet") . "<br><br>
                    ";
            }
            echo "</section>";
        }
    ?>
    <section>
        <h2>TEST</h2>
        <p>test</p>
    </section>
    </section>

    <!-- BOUTON AJOUT -->
     <div class="floating_button">
        <a href="./add_fuel.php">+</a>
     </div>

</body>
</html>