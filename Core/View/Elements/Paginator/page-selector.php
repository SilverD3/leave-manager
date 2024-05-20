<?php

/**
 * @var \Core\Database\Paginator\PagedResult $pageResult
 */
?>

<li class="page-item">
  <select class="page-link active" id="pageSelector">
    <?php for ($i = 1; $i <= $pageResult->getTotalOfPages(); $i++) : ?>
      <option value="<?= $i ?>" <?= $pageResult->getPageNumber() == $i ? "selected" : '' ?>><?= $i ?></option>
    <?php endfor; ?>
  </select>
</li>

<script type="text/javascript">
  const pageSelector = document.getElementById("pageSelector");
  pageSelector.addEventListener('change', (event) => {
    const page = event.target.value;
    window.location = "<?= prepareUrlForQuery(['page', 'page_action']) ?>" + 'page=' + page
  })
</script>