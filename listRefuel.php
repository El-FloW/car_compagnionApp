<?php
    error_reporting(0);
    session_start();
    $user_id = $_SESSION["user_id"];
    require "config.php"; // Connexion à la base de données
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Refuel</title>
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" type='text/css' media='screen' href='./css/cadre.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/floatingButton.css'>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    if($_SESSION["user_id"]) :
    ?>
    <section>
        <h1>Ravitallements</h1>
        <a href="index.php">Retour</a>
    </section>
    <?php
    else :
        header("Location: ./index.php");
        exit();
    endif;
    ?>

    <?php
        
        $stmt = $pdo->prepare("
            SELECT f.id, f.litre, f.prix, f.isFull, f.carburant, f.odometre, f.date, c.car_name
            FROM ca_ravitaillement f
            JOIN ca_car c ON f.fk_car_id = c.id
            WHERE c.fk_user = ? AND c.id = ?
            ORDER BY f.date DESC
            LIMIT 1000000
        ");
        $stmt->execute([$user_id, $_SESSION["selected_car_id"]]);
        $fuels = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (empty($fuels)) {
            echo "<p>Vous n'avez enregistré aucun ravitaillement pour le moment.</p>";
        } else {
            $fuelsCount = count($fuels);

            for ($i = 0; $i < $fuelsCount; $i++) {
                $fuel = $fuels[$i];
                echo "<section>";
                echo "<p><strong>Voiture :</strong> " . htmlspecialchars($fuel["car_name"]) . "<br>
                    <strong>Carburant :</strong> " . htmlspecialchars($fuel["carburant"]) . "<br>
                    <strong>Litres :</strong> " . htmlspecialchars($fuel["litre"]) . " L<br>
                    <strong>Prix :</strong> " . htmlspecialchars($fuel["prix"]) . " €<br>
                    <strong>Date :</strong> " . htmlspecialchars($fuel["date"]) . "<br>
                    <strong>Kilométrage :</strong> " . htmlspecialchars($fuel["odometre"]) . " km<br>
                    " . ($fuel["isFull"] ? "✅ Plein effectué" : "❌ Pas un plein complet") . "<br>";

                // Calcul de la consommation si ce n'est pas le dernier élément
                if ($i < $fuelsCount - 1 && $fuels[$i + 1]["isFull"] && $fuel["isFull"]) {
                    $nextFuel = $fuels[$i + 1];
                    $distance = $fuel["odometre"] - $nextFuel["odometre"];
                    $litres = $fuel["litre"];
                    $consumption = $distance > 0 ? ($litres / $distance) * 100 : 0;
                    echo "<strong>Distance :</strong> " . $distance . " km<br>";
                    echo "<strong>Consommation :</strong> " . number_format($consumption, 2) . " L/100km<br>";
                }

                echo "<br></p>";
                echo "</section>";
            }
        }
    ?>
</body>
</html>
