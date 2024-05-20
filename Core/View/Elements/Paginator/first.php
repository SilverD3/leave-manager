<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 */

$isFirst = $pageResult->getPageNumber() == 1;

?>

<li class="page-item <?= $isFirst ? "disabled" : '' ?>">
  <a class="page-link" href="<?= addQueryToUrl("page_action", "first") ?>">Début</a>
</li>