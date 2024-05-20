<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 */

$hasNext = $pageResult->getPageNumber() < $pageResult->getTotalOfPages();

?>

<li class="page-item <?= !$hasNext ? "disabled" : '' ?>">
  <a class="page-link" href="<?= addQueryToUrl("page_action", "next") ?>">Suivant</a>
</li>