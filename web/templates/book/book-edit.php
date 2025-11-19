<h2 class="m-3 text-center"><?php echo $title ?? '' ?></h2>
<form method="POST" class="container mt-3 p-4 rounded">

  <div class="form-group mb-3">
    <label for="title" class="form-label"><strong>Titre</strong></label>
    <input value="<?php echo isset($book['title']) ? (string) $book['title'] : '' ?>"
           type="text" id="title" name="title" class="form-control" required
           maxlength="255"
           placeholder="Titre du livre">
  </div>

  <div class="form-group mb-3">
    <label for="author" class="form-label"><strong>Auteur(s)</strong> </label>
    <input value="<?php echo isset($book['author']) ? (string) $book['author'] : '' ?>"
           type="text" id="author" name="author" class="form-control" required
           maxlength="255"
           placeholder="Nom(s) de l'auteur">
  </div>

  <div class="form-group mb-3">
    <label for="isbn" class="form-label"><strong>ISBN</strong></label>
    <input value="<?php echo isset($book['isbn']) ? (string) $book['isbn'] : '' ?>"
           type="text" id="isbn" name="isbn" class="form-control" required
           pattern="\d{13}"
           maxlength="13" placeholder="ISBN à 13 chiffres">
  </div>

  <div class="form-group mb-3">
    <label for="summary" class="form-label"><strong>Résumé</strong></label>
    <textarea id="summary" name="summary" class="form-control" rows="4"
              maxlength="500"
              placeholder="Résumé du livre"
              required><?php echo isset($book['summary']) ? (string) $book['summary'] : '' ?></textarea>
  </div>

  <div class="form-group mb-3">
    <label for="tags" class="form-label"><strong>Tags</strong> <small>(séparés
        par des virgules)</small></label>
    <input value="<?php echo isset($book['tags']) && is_array($book['tags']) ? implode(', ', $book['tags']) : '' ?>"
           type="text" id="tags" name="tags" class="form-control"
           placeholder="PHP, MySQL, Web Development">
  </div>

    <?php if (isset($book['id'])) : ?>
      <input value="<?php echo $book['id'] ?>" type="hidden" name="book_id">
      <input value="book_update" type="hidden" name="op">
    <?php else : ?>
      <input value="book_insert" type="hidden" name="op">
    <?php endif; ?>

  <input value="<?php echo $csrfToken ?? '' ?>" type="hidden" name="csrf_token">
  <button type="submit" class="btn-custom-secondary mt-3">Enregistrer</button>
</form>