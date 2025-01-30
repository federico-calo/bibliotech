<div class="container p-3">
  <h2>Gérer la bibliothèque</h2>

  <div class="container mt-4">
    <div class="row">

      <div class="col-md-6 col-12 mb-3 mt-3">
        <h3>Importer des livres</h3>
        <form method="POST" class="container mt-2" enctype="multipart/form-data">
          <p>
            <label for="csv_file">Fichier CSV :</label>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
          </p>
          <div>
            <button type="submit" class="btn btn-custom-primary">Importer</button>
          </div>
        </form>
      </div>

      <div class="col-md-6 col-12 mb-3 mt-3">
        <h3>Exporter la bibliothèque</h3>
        <form action="/export<?php echo $queryString ?? '' ?>" method="GET" class="container mt-2">
          <button type="submit" class="btn btn-custom-primary">Export CSV</button>
        </form>
      </div>

    </div>

    <div class="row">
      <div class="col-md-6 col-12 mb-3">
        <div class="mt-3">
          <h3>Statistiques générales</h3>
          <p><span>Total utilisateurs : </span><?php echo $nbUsers ?? 0 ?></p>
          <p><span>Total livres : </span><?php echo $nbBooks ?? 0 ?></p>
        </div>

      </div>

      <div class="col-md-6 col-12 mb-3">
          <?php if(isset($monthlyBooks)) : ?>
            <div class="mt-3">
              <h3>Statistiques mensuelles</h3>
              <table>
                <thead>
                <th>Mois</th>
                <th>Publications</th>
                </thead>
                  <?php foreach(($monthlyBooks) as $monthlyBook): ?>
                    <tr>
                      <td><p><?php echo $monthlyBook['publicationMonth'] ?></p></td>
                      <td class="text-center"><p><?php echo $monthlyBook['bookCount'] ?? 0 ?></p></td>
                    </tr>
                  <?php endforeach; ?>
              </table>
            </div>
          <?php endif; ?>
    </div>
  </div>

    <div class="row">

      <div class="col-md-6 col-12 mb-3 mt-3">
        <h3>Rafraîchir les données</h3>
        <form method="POST" class="container mt-2">
          <input value="1" type="hidden" name="clearCache">
          <button type="submit" class="btn btn-custom-primary">Vider les caches</button>
        </form>
      </div>

      <div class="col-md-6 col-12 mb-3 mt-3"></div>

    </div>


</div>