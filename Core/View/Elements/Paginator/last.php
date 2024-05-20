<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 */

$isLast = $pageResult->getPageNumber() == $pageResult->getTotalOfPages();

?>

<li class="page-item <?= $isLast ? "disabled" : '' ?>">
  <a class="page-link" href="<?= addQueryToUrl("page_action", "last") ?>">Fin</a>
</li>