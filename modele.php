<?php
include 'components/connect.php';

$marques = [];
try {
    // Query the marques table
    $stmt = $conn->query("SELECT id, marque FROM marques ORDER BY marque ASC");
    $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>

<?php
include 'components/connect.php';

$message = '';

// معالجة إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_modele'])) {
    $identifiant = $_POST['id'];
    $modele = $_POST['modele'];
    $marque = $_POST['marque'];

    try {
        // إدخال البيانات إلى جدول modeles
        $stmt = $conn->prepare("INSERT INTO modeles (id, modele, marque) VALUES (:id, :modele, :marque)");
        $stmt->execute([
            ':id' => $identifiant,
            ':modele' => $modele,
            ':marque' => $marque,
        ]);
        $message = "Modèle ajouté avec succès!";
    } catch (PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}

// استرداد البيانات لعرضها في الجدول
try {
    $stmt = $conn->query("SELECT * FROM modeles ORDER BY id ASC");
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $modeles = [];
    $message = "Erreur: " . $e->getMessage();
}
?>




<?php


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // حذف النماذج المحددة
        if (isset($_POST['supprimer_modele']) && !empty($_POST['selected_modele'])) {
            $selectedModele = $_POST['selected_modele'];

            // إعداد الاستعلام لحذف النماذج المحددة
            $placeholders = implode(',', array_fill(0, count($selectedModele), '?'));
            $sql = "DELETE FROM modeles WHERE id IN ($placeholders)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt->execute($selectedModele)) {
                $message = "Les modèles sélectionnés ont été supprimés avec succès!";
            } else {
                $message = "Erreur lors de la suppression des modèles sélectionnés.";
            }
        }

        // حذف جميع النماذج
        if (isset($_POST['supprimer_tous_modele'])) {
            $sql = "DELETE FROM modeles";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute()) {
                $message = "Tous les modèles ont été supprimés avec succès!";
            } else {
                $message = "Erreur lors de la suppression de tous les modèles.";
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}

// استرداد البيانات لعرض الجدول
try {
    $stmt = $conn->query("SELECT * FROM modeles ORDER BY id ASC");
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $modeles = [];
    $message = "Erreur: " . $e->getMessage();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des modèles</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />



</head>

<style>
        .container {
            margin-top: 30px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pagination button {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
        }
        .pagination button:hover {
            background-color: #e2e6ea;
        }
    </style>
</head>
<body>

<?php include 'components/header.php'; ?>

<div class="container">
    <!-- Gestion des Modèles Section -->
    <div class="card shadow-sm p-4">
        <h3 class="mb-4">Gestion des modèles</h3>
        <form method="POST" action="">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="id" class="form-label">Identifiant :</label>
            <input type="text" name="id" id="id" class="form-control" placeholder="Entrez l'identifiant" required>
        </div>
        <div class="col-md-6">
            <label for="modele" class="form-label">Modèle :</label>
            <input type="text" name="modele" id="modele" class="form-control" placeholder="Entrez le modèle" required>
        </div>
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

    <div class="d-flex justify-content-between">
        <button type="submit" name="enregistrer_modele" class="btn btn-success w-100 me-2">Enregistrer</button>
    </div>
</form>

    </div>

    <!-- Liste des Modèles Section -->
    <div class="card shadow-sm p-4">
    <div class="table-header mb-3">
        <h3>Liste des modèles</h3>
        <button class="btn btn-primary"><i class="fas fa-print"></i> Imprimer</button>
    </div>
    <form method="POST" action="">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Selectionner</th>
                    <th>Identifiant</th>
                    <th>Modèle</th>
                    <th>Marque</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modeles as $modele): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected_modele[]" value="<?= htmlspecialchars($modele['id']) ?>">
                    </td>
                    <td><?= htmlspecialchars($modele['id']) ?></td>
                    <td><?= htmlspecialchars($modele['modele']) ?></td>
                    <td><?= htmlspecialchars($modele['marque']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" name="supprimer_modele" class="btn btn-danger">Supprimer les modèles sélectionnés</button>
            <button type="submit" name="supprimer_tous_modele" class="btn btn-warning">Supprimer tous les modèles</button>
        </div>
    </form>
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
