<h2 class="m-3 text-center">Paramètres du compte</h2>
<?php require_once __DIR__ . '/user-tabs.php'; ?>
<form method="POST" class="container mt-3 p-4 rounded">
  <div class="form-group mb-3">
    <label for="login" class="form-label"><strong>Nom
        d'utilisateur</strong></label>
    <input type="text" id="login" name="login" class="form-control" required
           maxlength="255" placeholder="Nom d'utilisateur"
           value="<?php echo $user['login'] ?? '' ?>">
  </div>
  <div class="form-group mb-3">
    <label for="current_pwd" class="form-label"><strong>Mot de passe
        actuel</strong></label>
    <input type="password" id="current_pwd" name="current_pwd"
           class="form-control" required maxlength="255"
           placeholder="Mot de passe actuel">
  </div>
  <div class="form-group mb-3">
    <label for="pwd" class="form-label"><strong>Nouveau mot de
        passe</strong></label>
    <input type="password" id="pwd" name="pwd" class="form-control"
           maxlength="255" placeholder="Nouveau mot de passe">
  </div>
  <div class="form-group mb-3">
    <label for="confirm_pwd" class="form-label"><strong>Confirmer le nouveau mot
        de passe</strong></label>
    <input type="password" id="confirm_pwd" name="confirm_pwd"
           class="form-control" maxlength="255"
           placeholder="Confirmer le nouveau mot de passe">
  </div>
  <div class="form-group mb-3">
    <label for="mail" class="form-label"><strong>Email</strong></label>
    <input type="email" id="mail" name="mail" class="form-control" required
           maxlength="255" placeholder="Email"
           value="<?php echo $user['mail'] ?? '' ?>">
  </div>
  <div class="form-group mb-3">
    <label for="lastname" class="form-label"><strong>Nom de
        famille</strong></label>
    <input type="text" id="lastname" name="lastname" class="form-control"
           maxlength="255" placeholder="Nom de famille"
           value="<?php echo $user['lastname'] ?? '' ?>">
  </div>
  <div class="form-group mb-3">
    <label for="firstname" class="form-label"><strong>Nom de
        famille</strong></label>
    <input type="text" id="firstname" name="firstname" class="form-control"
           maxlength="255" placeholder="Prénom"
           value="<?php echo $user['firstname'] ?? '' ?>">
  </div>
  <div class="form-group mb-3">
    <label for="role" class="form-label"><strong>Rôle</strong></label>
    <select id="role" name="role" class="form-select">
      <option value="user" <?php echo isset($user['role']) && $user['role'] === 'user' ? 'selected' : '' ?>>
        Utilisateur
      </option>
      <option value="admin" <?php echo isset($user['role']) && $user['role'] === 'admin' ? 'selected' : '' ?>>
        Administrateur
      </option>
    </select>
  </div>
  <input value="user_update" type="hidden" name="op">
  <input value="<?php echo $csrfToken ?? '' ?>" type="hidden" name="csrf_token">
  <input value="<?php echo $user['id'] ?? '' ?>" type="hidden"
         name="user_id">
  <button type="submit" class="btn-custom-secondary mt-3">Enregistrer</button>

</form>
