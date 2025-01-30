<div class="page-error container d-flex flex-column align-items-center min-vh-100 text-center py-5">
  <h2>Oops ! Quelque chose ne s'est pas passé comme prévu !</h2>
  <p><?php echo isset($errorMessage) ? htmlspecialchars((string) $errorMessage) : '' ?></p>
    <?php if (isset($isDebug) && $isDebug) : ?>
      <pre><?php echo isset($trace) ? htmlspecialchars((string) $trace) : '' ?></pre>
    <?php endif; ?>
  <p><a class="btn btn-custom-secondary" href="/">Retour à l'accueil</a></p>
</div>