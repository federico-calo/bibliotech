<h2 class="m-3 text-center">Supprimer le livre</h2>
<form method="POST" class="container mt-3 p-4 border rounded bg-light">
  <div class="mb-3">
    <label for="book_id" class="form-label">
      <strong>Etes-vous sûr(e) de vouloir supprimer ce livre ?</strong>
    </label>
    <input value="<?php echo $book['id'] ?? '' ?>" type="hidden" name="book_id">
    <input value="book_delete" type="hidden" name="op">
  </div>
  <button type="submit" class="btn btn-primary mt-3">Supprimer</button>
  <a href="/book/<?php echo $book['id'] ?? '' ?>"
     class="btn btn-secondary mt-3">Retour</a>
</form>