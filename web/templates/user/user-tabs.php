<?php if (isset($activeTab)) : ?>
  <ul class="nav nav-tabs mb-3 custom-tabs">
    <li class="nav-item">
      <a class="nav-link <?php echo $activeTab === 'edit' ? 'active' : '' ?>"
         href="<?php echo $userEditUrl ?? '' ?>">
        Paramètres du compte
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo $activeTab === 'api' ? 'active' : '' ?>"
         href="<?php echo $userApiUrl ?? '' ?>">
        API
      </a>
    </li>
  </ul>
<?php endif; ?>