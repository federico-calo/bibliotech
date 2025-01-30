<h2 class="m-3 text-center">Créer un utilisateur</h2>
<form method="POST" class="container mt-3 p-4 rounded">
  <div class="form-group mb-3">
    <label for="login" class="form-label"><strong>Nom
        d'utilisateur</strong></label>
    <input type="text" id="login" name="login" class="form-control" required
           maxlength="255" placeholder="login">
  </div>
  <div class="form-group mb-3">
    <label for="pwd" class="form-label"><strong>Mot de passe</strong></label>
    <input type="password" id="pwd" name="pwd" class="form-control" required
           maxlength="255" placeholder="Mot de passe">
  </div>
  <input value="user_insert" type="hidden" name="op">
  <input value="<?php echo $csrfToken ?? '' ?>" type="hidden" name="csrf_token">
  <button type="submit" class="btn-custom-secondary mt-3">S'inscrire</button>
</form>