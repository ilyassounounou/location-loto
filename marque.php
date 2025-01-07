<?php
include 'components/connect.php';

// إضافة ماركة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['marque'])) {
        $id = $_POST['id'];
        $marque = $_POST['marque'];

        try {
            $stmt = $conn->prepare("INSERT INTO marques (id, marque) VALUES (:id, :marque)");
            $stmt->execute([
                ':id' => $id,
                ':marque' => $marque,
            ]);
        } catch (PDOException $e) {
            // Handle error
        }
    } elseif (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
        // حذف ماركة بناءً على ID
        $delete_id = $_POST['delete_id'];

        try {
            $stmt = $conn->prepare("DELETE FROM marques WHERE id = :id");
            $stmt->execute([':id' => $delete_id]);
        } catch (PDOException $e) {
            // Handle error
        }
    } elseif (isset($_POST['delete_all'])) {
        // حذف جميع الماركات
        try {
            $stmt = $conn->prepare("DELETE FROM marques");
            $stmt->execute();
        } catch (PDOException $e) {
            // Handle error
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Marques</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<?php include 'components/header.php'; ?>

<div class="container mt-5">
    <!-- Section: Gestion des Marques -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Ajouter une Marque</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="id" class="form-label">Identifiant :</label>
                    <input type="text" id="id" name="id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="marque" class="form-label">Marque :</label>
                    <input type="text" id="marque" name="marque" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Enregistrer</button>
            </form>
        </div>
    </div>

    <!-- Section: Liste des Marques -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Marques</h5>
            <form method="POST" action="">
                <button type="submit" name="delete_all" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer Tout</button>
            </form>
        </div>
        <div class="card-body">
            <?php
            $stmt = $conn->query("SELECT * FROM marques");
            $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Identifiant</th>
                        <th>Marque</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($marques as $marque): ?>
                    <tr>
                        <td><?= htmlspecialchars($marque['id']) ?></td>
                        <td><?= htmlspecialchars($marque['marque']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Supprimer une Marque -->
    <div class="card">
        <div class="card-header bg-danger text-white">Supprimer une Marque</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="delete_id" class="form-label">Identifiant à supprimer :</label>
                    <input type="text" id="delete_id" name="delete_id" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Supprimer</button>
            </form>
        </div>
    </div>
</div>

   <!-- Bootstrap JS and dependencies -->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
<script src="js/sc"></script>

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
