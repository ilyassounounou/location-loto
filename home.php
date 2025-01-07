
<?php

include 'components/connect.php';

// Fetch the total number of cars
$totalCars = 0;
try {
    $stmtCars = $conn->query("SELECT COUNT(*) as total FROM car");
    $resultCars = $stmtCars->fetch(PDO::FETCH_ASSOC);
    $totalCars = $resultCars['total'];
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Fetch the total number of clients
$totalClient = 0;
try {
    $stmtClients = $conn->query("SELECT COUNT(*) as total FROM client"); // Corrected 'clinet' to 'client'
    $resultClients = $stmtClients->fetch(PDO::FETCH_ASSOC);
    $totalClient = $resultClients['total'];
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>


<?php
include 'components/connect.php';

$reservedCars = [];
try {
    // Fetch reserved cars, reservation details, and client details
    $stmt = $conn->query("
        SELECT 
            c.matricule,
            r.duration,
            r.start_date,
            r.end_date,
            CONCAT(cl.nom, ' ', cl.prenom) AS client_name
        FROM reservations r
        JOIN car c ON r.matricule = c.matricule
        JOIN client cl ON r.cin = cl.cin
        ORDER BY r.start_date ASC
    ");
    $reservedCars = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
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
        <!-- Cards Section -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-primary">CHIFFRE D'AFFAIRES</h5>
                        <h3>301,100 DH</h3>
                        <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title text-info">VOITURES</h5>
            <h3><?= htmlspecialchars($totalCars) ?></h3>
            <i class="bi bi-car-front" style="font-size: 2rem;"></i>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title text-info">CLIENTS</h5>
            <h3><?= htmlspecialchars($totalClient) ?></h3>
            <i class="bi bi-person" style="font-size: 2rem;"></i> <!-- Changed icon for clients -->
        </div>
    </div>
</div>

        </div>

        <!-- Tables Section -->
        <div class="row">
        <div class="col-md-6">
    <h5 class="text-center mb-3">VOITURES ACTUELLEMENT RESERVÉES</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Matricule</th>
                <th>Durée (jours)</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Client</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reservedCars)): ?>
                <?php foreach ($reservedCars as $car): ?>
                    <tr>
                        <td><?= htmlspecialchars($car['matricule']) ?></td>
                        <td><?= htmlspecialchars($car['duration']) ?></td>
                        <td><?= htmlspecialchars($car['start_date']) ?></td>
                        <td><?= htmlspecialchars($car['end_date']) ?></td>
                        <td><?= htmlspecialchars($car['client_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Aucune voiture actuellement réservée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

            <div class="col-md-6">
                <h5 class="text-center mb-3">5 MEILLEURS CLIENTS</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>CA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sara</td>
                            <td>Tourabi</td>
                            <td>35,100.00 DH</td>
                        </tr>
                        <tr>
                            <td>Amal</td>
                            <td>Fillali</td>
                            <td>29,300.00 DH</td>
                        </tr>
                        <tr>
                            <td>Khalid</td>
                            <td>Tabali</td>
                            <td>25,200.00 DH</td>
                        </tr>
                        <tr>
                            <td>Fatiha</td>
                            <td>Anouar</td>
                            <td>24,500.00 DH</td>
                        </tr>
                        <tr>
                            <td>Ali</td>
                            <td>Fahd</td>
                            <td>20,800.00 DH</td>
                        </tr>
                    </tbody>
                </table>
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
