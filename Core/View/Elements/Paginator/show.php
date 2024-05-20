<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 * @var string $paginatorTemplateBaseDir
 */

?>

<?php if (!empty($pageResult) && $pageResult->getTotalOfPages() > 0) : ?>
  <nav aria-label="Select a page" class="mt-4 mb-2">
    <ul class="pagination justify-content-center">
      <?php require $paginatorTemplateBaseDir . 'first.php' ?>
      <?php require $paginatorTemplateBaseDir . 'previous.php' ?>
      <?php require $paginatorTemplateBaseDir . 'page-selector.php' ?>
      <?php require $paginatorTemplateBaseDir . 'next.php' ?>
      <?php require $paginatorTemplateBaseDir . 'last.php' ?>
    </ul>
  </nav>
<?php endif; ?>