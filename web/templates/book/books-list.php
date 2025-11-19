<div class="p-3 row-first row bg-custom-primary">
  <div class="col col-md-5 col-12 mb-3"></div>
    <?php foreach (['Auteur', 'Catégorie'] as $column_title) : ?>
      <div class="col col-md-3 col-12 mb-3">
          <?php echo $column_title ?>
      </div>
    <?php endforeach; ?>
  <div class="col col-md-1 col-12 mb-3 position-relative">
    <div class="position-absolute end-0 p-2 my-4">
      <div class="dropdown">
        <span role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </span>
        <ul class="dropdown-menu dropdown-menu-end">
            <?php if (!empty($isLoggedIn)) : ?>
              <li><a class="dropdown-item" href="/book/add">Ajouter un livre</a>
              </li>
              <li><a class="dropdown-item text-danger"
                     href="/user/logout">Se déconnecter</a></li>
              <li><a class="dropdown-item text-danger"
                     href="<?php echo $userEditUrl ?? '' ?>">Paramètres du compte</a></li>
                <?php if (!empty($isAdmin)) : ?>
                <li><a class="dropdown-item text-danger"
                       href="/admin/dashboard">Dashboard</a></li>
                <?php endif; ?>
            <?php else : ?>
              <li><a class="dropdown-item text-danger" href="/user/login">Se
                  connecter</a></li>
            <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php if (isset($books)) : ?>
    <?php foreach ($books as $book) : ?>
    <div class="row ps-4 py-3">
      <div class="col col-md-5 col-12 mb-3 book-title">
        <i class="fa-solid fa-book"></i>
        <h5 class="ms-4">
          <a href="/book/<?php echo $book['id'] ?>"><?php echo $book['title'] ?></a>
        </h5>
      </div>
      <div class="col col-md-3 col-12 mb-3 ps-0">
        <p><?php echo $book['author'] ?></p>
      </div>
      <div class="col col-md-3 col-12 mb-3 ps-0">
        <div class="tag-list">
            <?php foreach ($book['tags'] as $tag) : ?>
              <a href="/?tag=<?php echo $tag ?>">
                <span class="tag-item">
                  <i class="fas fa-tag"></i>
                  <?php echo $tag ?>
                </span>
              </a>
            <?php endforeach; ?>
        </div>
      </div>
      <div class="col col-md-1 col-12 mb-3 book-more">
        <a href="/book/<?php echo $book['id'] ?>">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  <nav aria-label="Pagination">
    <ul class="pagination justify-content-center">
        <?php if (isset($pagination['prev'])) : ?>
          <li class="page-item page-item-prev">
            <a class="page-link" href="<?php echo $pagination['prev']; ?>"
               tabindex="-1">
              <i class="fas fa-chevron-left"></i>&nbsp;Précédent
            </a>
          </li>
        <?php endif; ?>
        <?php if (isset($pagination['next'])) : ?>
          <li class="page-item  page-item-next">
            <a class="page-link" href="<?php echo $pagination['next']; ?>">
              Suivant&nbsp;<i class="fas fa-chevron-right"></i>
            </a>
          </li>
        <?php endif; ?>
    </ul>
  </nav>
    <?php if (!empty($isAdmin)) : ?>
    <div class="container p-3 text-end admin-actions">
      <form action="/export" method="GET" class="container mt-2">
        <input value="<?php echo $queryTag ?? '' ?>" type="hidden" name="tag">
        <input value="<?php echo $querySearch ?? '' ?>" type="hidden" name="search">
        <button type="submit" class="btn btn-custom-primary mt-3">Export CSV</button>
      </form>
    </div>
    <?php endif; ?>
<?php endif; ?>