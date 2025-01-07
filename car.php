<?php
include 'components/connect.php';

// Fetch marques
$marques = [];
try {
    $stmt = $conn->query("SELECT id, marque FROM marques ORDER BY marque ASC");
    $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Fetch modeles
$modeles = [];
try {
    $stmt = $conn->query("SELECT id, modele FROM modeles ORDER BY modele ASC");
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}


// Fetch carburants
$carburants = [];
try {
    $stmt = $conn->query("SELECT id, type FROM carburant ORDER BY type ASC");
    $carburants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>


<?php
include 'components/connect.php';

// معالجة إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_voiture'])) {
        $matricule = $_POST['matricule'];
        $annee = $_POST['annee']; // Fixed from 'annee_modele' to 'annee'
        $couleur = $_POST['couleur'];
        $puissance = $_POST['puissance'];
        $cout = $_POST['cout'];
        $modele = $_POST['modele'];
        $marque = $_POST['marque'];
        $carburant = $_POST['carburant'];
        
        try {
            $stmt = $conn->prepare("INSERT INTO car (matricule, annee_modele, couleur, puissance, cout_par_jour, modele, marque, carburant)
                                    VALUES (:matricule, :annee, :couleur, :puissance, :cout, :modele, :marque, :carburant)");
            $stmt->execute([
                ':matricule' => $matricule,
                ':annee' => $annee,
                ':couleur' => $couleur,
                ':puissance' => $puissance,
                ':cout' => $cout,
                ':modele' => $modele,
                ':marque' => $marque,
                ':carburant' => $carburant,
            ]);
            $message = "Voiture ajoutée avec succès!";
        } catch (PDOException $e) {
            $message = "Erreur: " . $e->getMessage();
        }
        

        // Redirect to avoid duplicate submission
        //header("Location: car.php");
        //exit; // Stop further execution
    }
}


// استرداد السيارات لعرضها في الجدول
try {
    $stmt = $conn->query("SELECT * FROM car");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $voitures = [];
}
?>


<?php
// include 'components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // حذف السيارات المحددة
        if (isset($_POST['supprimer_client']) && !empty($_POST['selected_cars'])) {
            $selectedCars = $_POST['selected_cars'];

            // إعداد الاستعلام لحذف السيارات المحددة
            $placeholders = implode(',', array_fill(0, count($selectedCars), '?'));
            $sql = "DELETE FROM car WHERE matricule IN ($placeholders)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt->execute($selectedCars)) {
                $message = "Les voitures sélectionnées ont été supprimées avec succès!";
            } else {
                $message = "Erreur lors de la suppression des voitures sélectionnées.";
            }
        }

        // حذف جميع السيارات
        if (isset($_POST['supprimer_tous_clients'])) {
            $sql = "DELETE FROM car";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute()) {
                $message = "Toutes les voitures ont été supprimées avec succès!";
            } else {
                $message = "Erreur lors de la suppression de toutes les voitures.";
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}

// استرداد البيانات لعرض الجدول
try {
    $stmt = $conn->query("SELECT * FROM car");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $voitures = [];
    $message = "Erreur: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navbar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
        .card {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<?php include 'components/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- Gestion Voitures Section -->
        <div class="col-md-5">
            <div class="card p-4">
                <h5 class="mb-4">Gestion voitures</h5>
                <form method="POST" action="">
    <div class="mb-3">
        <label for="matricule" class="form-label">Matricule :</label>
        <input type="text" id="matricule" name="matricule" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="annee" class="form-label">Année de modèle :</label>
        <input type="text" id="annee" name="annee" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="couleur" class="form-label">Couleur :</label>
        <input type="text" id="couleur" name="couleur" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="puissance" class="form-label">Puissance :</label>
        <input type="text" id="puissance" name="puissance" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="cout" class="form-label">Coût par jour :</label>
        <input type="text" id="cout" name="cout" class="form-control" required>
    </div>


<div class="mb-3">
    <label for="modele" class="form-label">Modèle :</label>
    <select id="modele" name="modele" class="form-select" required>
        <option value="">Sélectionnez un modèle</option>
        <?php foreach ($modeles as $modele): ?>
            <option value="<?= htmlspecialchars($modele['modele']) ?>">
                <?= htmlspecialchars($modele['modele']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>




<div class="mb-3">
    <label for="marque" class="form-label">Marque :</label>
    <select id="marque" name="marque" class="form-select" required>
        <option value="">Sélectionnez une marque</option>
        <?php foreach ($marques as $marque): ?>
            <option value="<?= htmlspecialchars($marque['marque']) ?>">
                <?= htmlspecialchars($marque['marque']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


<div class="mb-3">
    <label for="carburant" class="form-label">Carburant :</label>
    <select id="carburant" name="carburant" class="form-select" required>
        <option value="">Sélectionnez un type de carburant</option>
        <?php foreach ($carburants as $carburant): ?>
            <option value="<?= htmlspecialchars($carburant['type']) ?>">
                <?= htmlspecialchars($carburant['type']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

    <div class="d-flex justify-content-between">
        <button type="submit" name="ajouter_voiture" class="btn btn-primary">Enregistrer</button>
 

    </div>
</form>

            </div>
        </div>

        <!-- Liste Voitures Section -->

<div class="col-md-12">
    <div class="card p-4">
        <h5 class="mb-4">Liste des voitures</h5>
        <form method="POST" action="">
    <div class="table-container">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Selectionner</th>
                    <th>Matricule</th>
                    <th>Année de modèle</th>
                    <th>Couleur</th>
                    <th>Puissance</th>
                    <th>Coût par jour</th>
                    <th>Modèle</th>
                    <th>Marque</th>
                    <th>Carburant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voitures as $voiture): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected_cars[]" value="<?= htmlspecialchars($voiture['matricule']) ?>">
                    </td>
                    <td><?= htmlspecialchars($voiture['matricule']) ?></td>
                    <td><?= htmlspecialchars($voiture['annee_modele']) ?></td>
                    <td><?= htmlspecialchars($voiture['couleur']) ?></td>
                    <td><?= htmlspecialchars($voiture['puissance']) ?></td>
                    <td><?= htmlspecialchars($voiture['cout_par_jour']) ?></td>
                    <td><?= htmlspecialchars($voiture['modele']) ?></td>
                    <td><?= htmlspecialchars($voiture['marque']) ?></td>
                    <td><?= htmlspecialchars($voiture['carburant']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-3">
        <!-- Button to delete selected cars -->
        <button type="submit" name="supprimer_client" class="btn btn-danger">Supprimer les voitures sélectionnées</button>
        
        <!-- Button to delete all cars -->
        <button type="submit" name="supprimer_tous_clients" class="btn btn-warning">Supprimer toutes les voitures</button>
    </div>
</form>

    </div>
</div>


    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

<script src="js/script.js"></script>

<script>
let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   searchForm.classList.remove('active');
   profile.classList.remove('active');
}

</script>







</body>
</html>
