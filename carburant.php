<?php
include 'components/connect.php';

$message = '';

// Processing form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_carburant'])) {
    $identifiant = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? '';

    if (!empty($identifiant) && !empty($type)) {
        try {
            // Insert data into the carbrant table
            $stmt = $conn->prepare("INSERT INTO carburant (id, type) VALUES (:id, :type)");
            $stmt->execute([
                ':id' => $identifiant,
                ':type' => $type,
            ]);
            $message = "Carburant ajouté avec succès!";
        } catch (PDOException $e) {
            $message = "Erreur: " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

// Retrieve data for display
try {
    $stmt = $conn->query("SELECT * FROM carburant ORDER BY id ASC");
    $carburants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $carburants = [];
    $message = "Erreur: " . $e->getMessage();
}
?>



<?php
include 'components/connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete selected carburants
        if (isset($_POST['supprimer_carb']) && !empty($_POST['selected_modele'])) {
            $selectedCarburants = $_POST['selected_modele'];

            // Prepare placeholders for the selected items
            $placeholders = implode(',', array_fill(0, count($selectedCarburants), '?'));
            $sql = "DELETE FROM carburant WHERE id IN ($placeholders)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt->execute($selectedCarburants)) {
                $message = "Les carburants sélectionnés ont été supprimés avec succès!";
            } else {
                $message = "Erreur lors de la suppression des carburants sélectionnés.";
            }
        }

        // Delete all carburants
        if (isset($_POST['supprimer_tous_carb'])) {
            $sql = "DELETE FROM carburant";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute()) {
                $message = "Tous les carburants ont été supprimés avec succès!";
            } else {
                $message = "Erreur lors de la suppression de tous les carburants.";
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}

// Retrieve data to display in the table
try {
    $stmt = $conn->query("SELECT * FROM carburant ORDER BY id ASC");
    $carburants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $carburants = [];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>


<?php include 'components/header.php'; ?>

<div class="container my-5">
    <!-- Display Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Gestion des Types Carburants Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title mb-4">Gestion des Types Carburants</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="id" class="form-label">Identifiant</label>
                    <input type="text" id="id" name="id" class="form-control" placeholder="Entrez l'identifiant" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" id="type" name="type" class="form-control" placeholder="Entrez le type de carburant" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="enregistrer_carburant" class="btn btn-success">Enregistrer</button>
           
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des Types Carburants Section -->
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Liste des Types Carburants</h3>
            <form method="POST" action="">
    <!-- Display Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Table for displaying carburants -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Sélectionner</th>
                <th>Identifiant</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($carburants as $carbrant): ?>
            <tr>
                <td>
                    <input type="checkbox" name="selected_modele[]" value="<?= htmlspecialchars($carbrant['id']) ?>">
                </td>
                <td><?= htmlspecialchars($carbrant['id']) ?></td>
                <td><?= htmlspecialchars($carbrant['type']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-3">
        <button type="submit" name="supprimer_carb" class="btn btn-danger">Supprimer les carburants sélectionnés</button>
        <button type="submit" name="supprimer_tous_carb" class="btn btn-warning">Supprimer tous les carburants</button>
    </div>
</form>

            
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
