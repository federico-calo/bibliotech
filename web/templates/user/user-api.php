<h2 class="m-3 text-center">API</h2>
<?php if (isset($activeTab)) : ?>
    <?php include_once __DIR__ . '/user-tabs.php'; ?>
<?php endif; ?>
<div class="container">
  <div class="container  mt-3 p-2">
    <h3>Accès à l'API</h3>
    <form method="POST" class="container mt-3 p-2">
      <div class="form-group mb-3">
        <label for="lastname" class="form-label"><strong>Token
            d'authentification</strong></label>
        <input type="text" id="token" name="token" class="form-control"
               value="<?php echo htmlspecialchars($user['token'] ?? '') ?>"
               disabled>
      </div>
      <input value="<?php echo $csrfToken ?? '' ?>" type="hidden"
             name="csrf_token">
      <button type="submit" class="btn-custom-secondary  mt-3 p-2">Générer un
        nouveau
        token
      </button>
    </form>
  </div>
  <div class="container  mt-3 p-2">
    <h3>Documentation de l'API</h3>
    <div class="container  mt-3 p-2">
      <h4>1. Obtenir la liste des livres</h4>
      <div class="endpoint">
        <p><span class="param">Méthode</span>: GET</p>
        <p><span class="param">URL</span>: /api/books</p>
        <p><span class="param">Description</span>: Cette requête récupère la
          liste
          paginée des livres.</p>
        <p><span class="param">Paramètres :</span></p>
        <ul>
          <li><span class="param">page</span> (optionnel): Numéro de la page à
            afficher (par défaut 1).
          </li>
          <li><span class="param">limit</span> (optionnel): Nombre de livres par
            page (par défaut 10).
          </li>
          <li><span class="param">tag</span> (optionnel): Filtrer les livres
            par tag.
          </li>
          <li><span class="param">search</span> (optionnel): Recherche par titre
            ou auteur.
          </li>
        </ul>
        <p><span class="param">Exemple de requête</span>:</p>
        <code>
          <pre>GET /api/books?page=2&limit=10&tag=PHP   </pre>
        </code>
        <p><span class="param">Réponse</span>:</p>
        <code>
      <pre>
{
  "current_page": 2,
  "total_pages": 5,
  "total_books": 50,
  "books": [
    {
      "id": 1,
      "title": "Introduction à PHP",
      "author": "John Doe",
      "tags": ["PHP", "Développement"]
    },
    ...
  ]
}
          </pre>
        </code>
      </div>
    </div>
  </div>
  <div class="container mt-2 p-2">
    <h4>2. Rechercher un livre</h4>
    <div class="endpoint">
      <p><span class="param">Méthode</span>: GET</p>
      <p><span class="param">URL</span>: /api/books</p>
      <p><span class="param">Description</span>: Cette requête permet de
        rechercher des livres par titre ou auteur.</p>
      <p><span class="param">Paramètres :</span></p>
      <ul>
        <li><span class="param">search</span> (optionnel): Recherche par titre
          ou
          auteur.
        </li>
      </ul>
      <p><span class="param">Exemple de requête</span>:</p>
      <pre>GET /api/books?search=PHP</pre>
      <p><span class="param">Réponse</span>:</p>
      <code>
      <pre>
{
  "current_page": 1,
  "total_pages": 3,
  "total_books": 30,
  "books": [
    {
      "id": 5,
      "title": "PHP pour débutants",
      "author": "Jane Smith",
      "tags": ["PHP", "Web"]
    },
    ...
  ]
}
        </pre>
      </code>
    </div>
  </div>
  <div class="container mt-2 p-2">
    <h4>3. Ajouter un livre</h4>
    <div class="endpoint">
      <p><span class="param">Méthode</span>: POST</p>
      <p><span class="param">URL</span>: /api/books</p>
      <p><span class="param">Description</span>: Cette requête permet d'ajouter
        un
        nouveau livre.</p>
      <p><span class="param">Corps de la requête :</span></p>
      <code>
      <pre>
{
  "title": "Nouveau livre",
  "author": "Auteur inconnu",
  "tags": ["PHP", "Programmation"]
}
       </pre>
      </code>
      <p><span class="param">Réponse</span>:</p>
      <code>
      <pre>
{
  "id": 101,
  "title": "Nouveau livre",
  "author": "Auteur inconnu",
  "tags": ["PHP", "Programmation"]
}
        </pre>
      </code>
    </div>
  </div>
</div>