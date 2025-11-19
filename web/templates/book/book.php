<?php if (isset($book)) : ?>
  <div class="book book-full position-relative py-1 px-3">
    <div class="book-img my-4">
      <i class="fa-solid fa-book"></i>
    </div>
    <div class="book-title my-4 mx-4">
      <h2><?php echo $book['title'] ?></h2>
    </div>
    <div>
      <p><strong>Résumé</strong> <?php echo $book['summary'] ?></p>
    </div>
    <div>
      <p><strong>Auteur(s) </strong> <?php echo $book['author'] ?></p>
    </div>
    <div class="my-4">
      <strong>Catégories</strong>
      <ul>
          <?php if (isset($book['tags'])) : ?>
                <?php foreach ($book['tags'] as $tag) : ?>
              <li>
                    <?php if ($tag == 'PHP') : ?>
                    <strong><?php echo $tag ?></strong>
                    <?php elseif ($tag == 'MySQL') : ?>
                    <em><?php echo $tag ?></em>
                    <?php else : ?>
                        <?php echo $tag ?>
                    <?php endif; ?>
              </li>
                <?php endforeach; ?>
          <?php endif; ?>
      </ul>
    </div>
    <div class="my-4">
      <p><strong>ISBN</strong> <?php echo $book['isbn'] ?></p>
    </div>
    <?php if (isset($book['data']['link_url'])) : ?>
      <div class="my-4">
        <p>
          <a class="btn btn-custom-secondary" target="_blank"
             href="<?php echo $book['data']['link_url'] ?>">
            <i class="fa-solid fa-eye"></i> Voir plus sur Open Library
          </a>
        </p>
      </div>
    <?php endif; ?>
    <?php if (!empty($isLoggedIn)) : ?>
      <div class="position-absolute top-0 end-0 me-3 my-4">
        <div class="dropdown">
        <span role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </span>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item"
                   href="/book/<?php echo $book['id'] ?>/edit">Éditer</a></li>
            <li><a class="dropdown-item text-danger"
                   href="/book/<?php echo $book['id'] ?>/delete">Supprimer</a>
            </li>
          </ul>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>