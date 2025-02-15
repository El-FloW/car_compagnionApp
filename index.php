<!DOCTYPE html>
<?php
    error_reporting(0);
    session_start();
    $user_id = $_SESSION["user_id"];
    require "config.php"; // Connexion à la base de données
?>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Compagnion App</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/main.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/cadre.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/floatingButton.css'>
    <script src='main.js'></script>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php
    if($_SESSION["user_id"]) :
    ?>
    <section>
        <h1>Bienvenue</h1>
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

    // Récupérer les voitures de l'utilisateur
    $stmt = $pdo->prepare("SELECT id, car_name FROM ca_car WHERE fk_user = ?");
    $stmt->execute([$user_id]);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<section>";
    echo "<h2>Mes véhicules</h2>";

    if (empty($cars)) :
        echo "<a href='./addCar.php'>Ajouter un véhicule</a>";
    else :
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fk_car_id'])) {
        $_SESSION['selected_car_id'] = $_POST['fk_car_id'];
    }
    ?>
    <form method="post" action="">
        <select name="fk_car_id" required onchange="this.form.submit()">
            <option value="">Sélectionnez un véhicule</option>
            <?php foreach ($cars as $car): ?>
                <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['car_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($_SESSION['selected_car_id'])): ?>
            <?php
            $selected_car_id = $_SESSION['selected_car_id'];
            $stmt = $pdo->prepare("SELECT car_name FROM ca_car WHERE id = ?");
            $stmt->execute([$selected_car_id]);
            $selected_car = $stmt->fetch(PDO::FETCH_ASSOC);

            //GET LAST ODOMETER
            $stmt = $pdo->prepare("SELECT `odometre` FROM `ca_ravitaillement` WHERE `fk_car_id` = ? ORDER BY `date` DESC LIMIT 1");
            $stmt->execute([$_SESSION['selected_car_id']]);
            $lastOdo = $stmt->fetch(PDO::FETCH_ASSOC);

            ?>
            <p><?= htmlspecialchars($selected_car['car_name']) ?> (<?= htmlspecialchars($lastOdo['odometre']) ?> km)</p>
            <a href='./addCar.php'>Ajouter un véhicule</a>
        <?php endif; ?>
    </form>
    <?php
    endif;
    echo "</section>";
    ?>

    <section class="halfSection">
<!--Récupérer les 5 derniers ravitaillements de l'utilisateur -->
    <?php
        
        $stmt = $pdo->prepare("
            SELECT f.id, f.litre, f.prix, f.isFull, f.carburant, f.odometre, f.date, c.car_name
            FROM ca_ravitaillement f
            JOIN ca_car c ON f.fk_car_id = c.id
            WHERE c.fk_user = ? AND c.id = ?
            ORDER BY f.date DESC
            LIMIT 2
        ");
        $stmt->execute([$user_id, $_SESSION["selected_car_id"]]);
        $fuels = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<section onclick=\"window.location.href='./listRefuel.php';\" style=\"cursor: pointer;\">";
        echo "<h2>Dernier ravitaillement</h2>";
        echo "<p><strong>" . htmlspecialchars($fuels[0]["car_name"]) . "</strong></p>";
        $date = new DateTime($fuels[0]["date"]);
        echo "<p>" . $date->format('d/m H:i') . "</p>";
        echo "<div class=\"resume\">";

        if (empty($fuels)) {
            echo "<p>Vous n'avez enregistré aucun ravitaillement pour le moment.</p>";
        } else {
            $fuel = $fuels[0];
            echo "<p>" . htmlspecialchars($fuel["carburant"]) . "</p>";
            echo "<p>" . htmlspecialchars($fuel["litre"]) . " L</p>";
            echo "<p>" . htmlspecialchars($fuel["prix"]) . " €</p>";
            echo "<p>" . ($fuel["isFull"] ? "✅ Plein effectué" : "❌ Pas un plein complet") . "</p>";
            }
            // Calcul de la consommation si ce n'est pas le dernier élément
            if ($fuels[1] && $fuels[1]["isFull"] && $fuel["isFull"]) {
                $nextFuel = $fuels[1];
                $distance = $fuel["odometre"] - $nextFuel["odometre"];
                $litres = $fuel["litre"];
                $consumption = $distance > 0 ? ($litres / $distance) * 100 : 0;
                $pricePerKm = $nextFuel["prix"] / $distance;
                echo "<p><strong>Distance :</strong> " . $distance . " km</p>";
                echo "<p>" . number_format($consumption, 2) . " L/100km</p>";
                echo "<p>" . number_format($pricePerKm, 2) . " €/km</p>";
            }
        echo "</div>";
        echo "</section>";
        
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