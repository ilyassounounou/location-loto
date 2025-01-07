<?php
include 'components/connect.php';

// Fetch clients
$clients = [];
try {
    $stmt = $conn->query("SELECT id, prenom, nom, telephone, cin, permis, adresse, sexe, email FROM client ORDER BY prenom ASC");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Fetch vehicles with marque and modele
$vehicles = [];
try {
    $stmt = $conn->query("SELECT id, matricule, cout_par_jour, marque, modele FROM car ORDER BY matricule ASC");
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['ajouter_reservation'])) {
      try {
          $stmt = $conn->prepare(
              "INSERT INTO reservations (
                  client, telephone, cin, permis, adresse, sexe, email,
                  matricule, marque, modele, cout_par_jour, 
                  code, start_date, end_date, duration, amount
              ) VALUES (
                  :client, :telephone, :cin, :permis, :adresse, :sexe, :email, 
                  :matricule, :marque, :modele, :cout_par_jour, 
                  :code, :start_date, :end_date, :duration, :amount
              )"
          );

          $stmt->execute([
            ':client' => $_POST['client_name'], // Nom complet du client
            ':telephone' => $_POST['telephone'],
            ':cin' => $_POST['cin'],
            ':permis' => $_POST['permis'],
            ':adresse' => $_POST['adresse'],
            ':sexe' => $_POST['sexe'],
            ':email' => $_POST['email'],
            ':matricule' => $_POST['vehicle_matricule'], // Matricule de la voiture
            ':marque' => $_POST['marque'],
            ':modele' => $_POST['modele'],
            ':cout_par_jour' => $_POST['cout_par_jour'],
            ':code' => $_POST['code'],
            ':start_date' => $_POST['start_date'],
            ':end_date' => $_POST['end_date'],
            ':duration' => $_POST['duration'],
            ':amount' => $_POST['amount'],
        ]);
        

          echo "Reservation added successfully!";
          header("Location: reservation.php");
          exit;
      } catch (PDOException $e) {
          echo "SQL Error: " . $e->getMessage();
      }
  }





    if (isset($_POST['delete_selected_reservations']) && !empty($_POST['selected_reservations'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_reservations']));
        $stmt = $conn->prepare("DELETE FROM reservations WHERE id IN ($ids)");
        $stmt->execute();
    }

    if (isset($_POST['delete_all_reservations'])) {
        $stmt = $conn->prepare("DELETE FROM reservations");
        $stmt->execute();
    }
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
    .container {
      margin-top: 30px;
    }
    .form-section {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .form-section h3 {
      margin-bottom: 20px;
    }
    .table-section {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
    }
    .table-section h3 {
      margin-bottom: 20px;
    }
    .table {
      margin-top: 10px;
    }
    .btn-danger {
      background-color: #d9534f;
      border-color: #d9534f;
    }

    


  </style>
</head>
<body>


<?php include 'components/header.php'; ?>

<div class="container">
  <h2 class="mb-4 text-center">Car Rental Management</h2>

  <form method="POST" action="">
    <!-- Client Information -->
    <div class="form-section">
      <h3>Informations Client</h3>
      <div class="mb-3">
    <label for="client" class="form-label">Client</label>
    <select id="client" name="client" class="form-select" onchange="updateClientInfo()">
        <option value="">Sélectionnez un client</option>
        <?php foreach ($clients as $client): ?>
            <option value="<?= htmlspecialchars(json_encode($client)) ?>">
                <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<!-- Hidden Inputs for Client -->
<input type="hidden" id="client-name" name="client_name">
<input type="hidden" id="vehicle-matricule" name="vehicle_matricule">


      <div class="mb-3">
        <label for="phone" class="form-label">Téléphone</label>
        <input type="text" id="phone" name="telephone" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="cin" class="form-label">CIN</label>
        <input type="text" id="cin" name="cin" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="permis" class="form-label">Permis</label>
        <input type="text" id="permis" name="permis" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="adresse" class="form-label">Adresse</label>
        <input type="text" id="adresse" name="adresse" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="sexe" class="form-label">Sexe</label>
        <input type="text" id="sexe" name="sexe" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" readonly>
      </div>
    </div>

    <!-- Vehicle Information -->
    <div class="form-section">
      <h3>Informations Voiture</h3>
      <div class="mb-3">
    <label for="vehicle" class="form-label">Voiture</label>
    <select id="vehicle" name="vehicle" class="form-select" onchange="updateVehicleInfo()">
        <option value="">Sélectionnez une voiture</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= htmlspecialchars(json_encode($vehicle)) ?>">
                <?= htmlspecialchars($vehicle['matricule']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<!-- Hidden Inputs for Vehicle -->

      <div class="mb-3">
        <label for="marque" class="form-label">Marque</label>
        <input type="text" id="marque" name="marque" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="modele" class="form-label">Modèle</label>
        <input type="text" id="modele" name="modele" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="cout_par_jour" class="form-label">Coût par jour</label>
        <input type="text" id="cout_par_jour" name="cout_par_jour" class="form-control" readonly>
      </div>
    </div>

    <!-- Reservation Information -->
    <div class="form-section">
      <h3>Informations Réservation</h3>
      <div class="mb-3">
        <label for="code" class="form-label">Code</label>
        <input type="number" id="code" name="code" class="form-control" value="7">
      </div>
      <div class="mb-3">
        <label for="start-date" class="form-label">Date de Début</label>
        <input type="date" id="start-date" name="start_date" class="form-control">
      </div>
      <div class="mb-3">
        <label for="end-date" class="form-label">Date de Fin</label>
        <input type="date" id="end-date" name="end_date" class="form-control">
      </div>
      <div class="mb-3">
        <label for="duration" class="form-label">Durée</label>
        <input type="text" id="duration" name="duration" class="form-control" readonly>
      </div>
      <div class="mb-3">
        <label for="amount" class="form-label">Montant</label>
        <input type="text" id="amount" name="amount" class="form-control" readonly>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <button type="submit" name="ajouter_reservation" class="btn btn-success">Enregistrer</button>
    </div>
    
  </form>

</div>




 <!-- Combined Reservation List -->
 <div class="table-section">
    <h3>Liste des Réservations</h3>
    <button class="btn btn-primary" id="print-btn"><i class="fas fa-print"></i> Imprimer</button>

    <form method="POST" action="">
        <div class="table-responsive"> <!-- Added responsive class -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Selection</th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>CIN</th>
                        <th>Permis</th>
                        <th>Adresse</th>
                        <th>Sexe</th>
                        <th>Email</th>
                        <th>Matricule</th>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Coût par jour</th>
                        <th>Code</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Durée</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from database
                    $stmt = $conn->query("SELECT * FROM reservations");
                    while ($reservation = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_reservations[]" value="<?= htmlspecialchars($reservation['id']) ?>">
                            </td>
                            <td><?= htmlspecialchars($reservation['client']) ?></td>
                            <td><?= htmlspecialchars($reservation['telephone']) ?></td>
                            <td><?= htmlspecialchars($reservation['cin']) ?></td>
                            <td><?= htmlspecialchars($reservation['permis']) ?></td>
                            <td><?= htmlspecialchars($reservation['adresse']) ?></td>
                            <td><?= htmlspecialchars($reservation['sexe']) ?></td>
                            <td><?= htmlspecialchars($reservation['email']) ?></td>
                            <td><?= htmlspecialchars($reservation['matricule']) ?></td>
                            <td><?= htmlspecialchars($reservation['marque']) ?></td>
                            <td><?= htmlspecialchars($reservation['modele']) ?></td>
                            <td><?= htmlspecialchars($reservation['cout_par_jour']) ?></td>
                            <td><?= htmlspecialchars($reservation['code']) ?></td>
                            <td><?= htmlspecialchars($reservation['start_date']) ?></td>
                            <td><?= htmlspecialchars($reservation['end_date']) ?></td>
                            <td><?= htmlspecialchars($reservation['duration']) ?></td>
                            <td><?= htmlspecialchars($reservation['amount']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" name="delete_selected_reservations" class="btn btn-danger">Supprimer les réservations sélectionnées</button>
            <button type="submit" name="delete_all_reservations" class="btn btn-warning">Supprimer toutes les réservations</button>
        </div>
    </form>
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
function calculateReservationDetails() {
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    const coutParJour = parseFloat(document.getElementById('cout_par_jour').value || 0);

    if (startDate && endDate && startDate <= endDate && coutParJour > 0) {
        const timeDifference = endDate - startDate;
        const days = timeDifference / (1000 * 60 * 60 * 24);

        document.getElementById('duration').value = `${days} jours`;
        document.getElementById('amount').value = `${(days * coutParJour).toFixed(2)} DH`;
    } else {
        document.getElementById('duration').value = "";
        document.getElementById('amount').value = "";
    }
}

// Attach the calculation to the date fields
document.getElementById('start-date').addEventListener('change', calculateReservationDetails);
document.getElementById('end-date').addEventListener('change', calculateReservationDetails);

</script>

<script>
function updateClientInfo() {
    const selectedClient = JSON.parse(document.getElementById('client').value || '{}');

    document.getElementById('phone').value = selectedClient.telephone || '';
    document.getElementById('cin').value = selectedClient.cin || '';
    document.getElementById('permis').value = selectedClient.permis || '';
    document.getElementById('adresse').value = selectedClient.adresse || '';
    document.getElementById('sexe').value = selectedClient.sexe || '';
    document.getElementById('email').value = selectedClient.email || '';

    // Combine prenom et nom pour afficher dans la colonne client
    document.getElementById('client-name').value = selectedClient.prenom + ' ' + selectedClient.nom || '';
}

function updateVehicleInfo() {
    const selectedVehicle = JSON.parse(document.getElementById('vehicle').value || '{}');
    document.getElementById('marque').value = selectedVehicle.marque || '';
    document.getElementById('modele').value = selectedVehicle.modele || '';
    document.getElementById('cout_par_jour').value = selectedVehicle.cout_par_jour || '';

    // Matricule pour afficher correctement dans la colonne voiture
    document.getElementById('vehicle-matricule').value = selectedVehicle.matricule || '';
}

</script>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    const printButton = document.getElementById('print-btn');

    printButton.addEventListener('click', function () {
        // Get selected reservations
        const selectedCheckboxes = document.querySelectorAll('input[name="selected_reservations[]"]:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert("Veuillez sélectionner une réservation à imprimer.");
            return;
        }

        // Fetch reservation data
        const reservations = [];
        selectedCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest('tr');
            const cells = row.querySelectorAll('td');
            const reservation = {
                client: cells[1].textContent.trim(),
                telephone: cells[2].textContent.trim(),
                cin: cells[3].textContent.trim(),
                permis: cells[4].textContent.trim(),
                adresse: cells[5].textContent.trim(),
                sexe: cells[6].textContent.trim(),
                email: cells[7].textContent.trim(),
                matricule: cells[8].textContent.trim(),
                marque: cells[9].textContent.trim(),
                modele: cells[10].textContent.trim(),
                coutParJour: cells[11].textContent.trim(),
                dateDebut: cells[12].textContent.trim(),
                dateFin: cells[13].textContent.trim(),
                duree: cells[14].textContent.trim(),
                montant: cells[15].textContent.trim()
            };
            reservations.push(reservation);
        });

        // Generate printable content
        let printableContent = `
            <html>
            <head>
                <title>Imprimer Réservation</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h2 { text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2>Détails de la Réservation</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>CIN</th>
                            <th>Permis</th>
                            <th>Adresse</th>
                            <th>Sexe</th>
                            <th>Email</th>
                            <th>Matricule</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Coût par Jour</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Durée</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        reservations.forEach((res) => {
            printableContent += `
                <tr>
                    <td>${res.client}</td>
                    <td>${res.telephone}</td>
                    <td>${res.cin}</td>
                    <td>${res.permis}</td>
                    <td>${res.adresse}</td>
                    <td>${res.sexe}</td>
                    <td>${res.email}</td>
                    <td>${res.matricule}</td>
                    <td>${res.marque}</td>
                    <td>${res.modele}</td>
                    <td>${res.coutParJour}</td>
                    <td>${res.dateDebut}</td>
                    <td>${res.dateFin}</td>
                    <td>${res.duree}</td>
                    <td>${res.montant}</td>
                </tr>
            `;
        });

        printableContent += `
                    </tbody>
                </table>
            </body>
            </html>
        `;

        // Open print window
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printableContent);
        printWindow.document.close();
        printWindow.print();
    });
});

</script>
</body>
</html>
