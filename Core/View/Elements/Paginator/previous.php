<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 */

$hasPrevious = $pageResult->getPageNumber() > 1;

?>

<li class="page-item <?= !$hasPrevious ? "disabled" : '' ?>">
  <a class="page-link" href="<?= addQueryToUrl("page_action", "previous") ?>">Précédent</a>
</li>