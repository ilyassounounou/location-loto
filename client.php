
<?php
include 'components/connect.php';
?>

<?php

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_all'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM client");
            $stmt->execute();
            $message = "Tous les clients ont été supprimés avec succès !";
            $message_type = "alert-success";
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
            $message_type = "alert-danger";
        }
    }
}


?>


<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's for deleting all clients
    if (isset($_POST['delete_all'])) {
        try {
            // Delete all clients
            $stmt = $conn->prepare("DELETE FROM client");
            $stmt->execute();
            echo "<div class='alert alert-success'>Tous les clients ont été supprimés avec succès !</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } 
    // Check if it's for deleting a specific client
    elseif (isset($_POST['delete_cin']) && !empty($_POST['delete_cin'])) {
        $delete_cin = $_POST['delete_cin'];

        try {
            // Delete client by CIN
            $stmt = $conn->prepare("DELETE FROM client WHERE cin = :cin");
            $stmt->execute([':cin' => $delete_cin]);
            echo "<div class='alert alert-success'>Client supprimé avec succès !</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } 
    // Check if it's for adding a client
    elseif (
        isset($_POST['cin'], $_POST['permis'], $_POST['prenom'], $_POST['nom'], $_POST['sexe']) &&
        !empty($_POST['cin']) && !empty($_POST['permis']) && !empty($_POST['prenom']) && !empty($_POST['nom']) && !empty($_POST['sexe'])
    ) {
        $cin = $_POST['cin'];
        $permis = $_POST['permis'];
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $sexe = $_POST['sexe'];
        $adresse = $_POST['adresse'] ?? null; // Optional field
        $telephone = $_POST['telephone'] ?? null; // Optional field
        $email = $_POST['email'] ?? null; // Optional field

        try {
            // Add client to database
            $stmt = $conn->prepare("INSERT INTO client (cin, permis, prenom, nom, sexe, adresse, telephone, email)
                                    VALUES (:cin, :permis, :prenom, :nom, :sexe, :adresse, :telephone, :email)");
            $stmt->execute([
                ':cin' => $cin,
                ':permis' => $permis,
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':sexe' => $sexe,
                ':adresse' => $adresse,
                ':telephone' => $telephone,
                ':email' => $email,
            ]);
            echo "<div class='alert alert-success'>Client ajouté avec succès !</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Veuillez remplir tous les champs requis.</div>";
    }
}


?>

<!-- HTML content remains the same -->



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
    <div class="row">
        <!-- Gestion Clients Form -->
        <div class="col-md-5">
            <h5 class="mb-4">Gestion clients</h5>
            <form method="POST" action="">
                <!-- Existing Fields for Adding Clients -->
                <div class="mb-3">
                    <label for="cin" class="form-label">CIN</label>
                    <input type="text" class="form-control" id="cin" name="cin" required>
                </div>
                <div class="mb-3">
                    <label for="permis" class="form-label">Permis</label>
                    <input type="text" class="form-control" id="permis" name="permis" required>
                </div>
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select class="form-select" id="sexe" name="sexe" required>
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse">
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>

            <!-- Form for Deleting All Clients -->
<!-- Form for Deleting All Clients -->
<h5 class="mt-5">Supprimer tous les clients</h5>
<form method="POST" action="" id="deleteAllForm">
    <input type="hidden" name="delete_all" value="1">
    <button type="button" class="btn btn-danger" id="deleteAllButton">Supprimer tous les clients</button>
</form>



            <!-- Form for Deleting Clients -->
            <h5 class="mt-5">Supprimer un client</h5>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="delete_cin" class="form-label">CIN du client à supprimer</label>
                    <input type="text" class="form-control" id="delete_cin" name="delete_cin" required>
                </div>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>

        <!-- Liste Clients Table -->
        <div class="col-md-7">
            <h5 class="mb-4">Liste clients</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>CIN</th>
                        <th>Permis</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Sexe</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>

                <?php
$stmt = $conn->query("SELECT * FROM client");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['cin']) ?></td>
                        <td><?= htmlspecialchars($client['permis']) ?></td>
                        <td><?= htmlspecialchars($client['prenom']) ?></td>
                        <td><?= htmlspecialchars($client['nom']) ?></td>
                        <td><?= htmlspecialchars($client['sexe']) ?></td>
                        <td><?= htmlspecialchars($client['telephone']) ?></td>
                        <td><?= htmlspecialchars($client['email']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            

            <div class="text-end mb-3">
    <a href="" class="btn btn-secondary">Actualiser</a>
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

<script>
document.querySelector('form[action=""]').addEventListener('submit', function(e) {
    const deleteCinInput = document.querySelector('#delete_cin');
    if (deleteCinInput && deleteCinInput.value.trim() !== '') {
        if (!confirm(`Êtes-vous sûr de vouloir supprimer le client avec CIN : ${deleteCinInput.value.trim()} ?`)) {
            e.preventDefault();
        }
    }
});


</script>


<script>
    document.querySelector('#deleteAllButton').addEventListener('click', function () {
    const confirmationCode = prompt("Veuillez entrer le code de confirmation pour supprimer tous les clients :");
    if (confirmationCode === '0000') {
        // إذا كان الرمز صحيحًا، قم بإرسال النموذج
        document.querySelector('#deleteAllForm').submit();
    } else {
        // إذا كان الرمز غير صحيح، أظهر رسالة تنبيه
        alert("Code incorrect. Suppression annulée.");
    }
});

</script>
</body>
</html>





