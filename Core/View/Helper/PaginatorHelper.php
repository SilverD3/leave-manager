<?php

declare(strict_types=1);

namespace Core\View\Helper;

use Core\Utils\Session;

class PaginatorHelper
{
  const PAGINATOR_TEMPLATE_PATH = CORE_PATH . 'View' . DS . 'Elements' . DS . 'Paginator' . DS;

  public static function show()
  {
    if (!Session::check(PAGINATOR_KEY)) {
      return;
    }

    /**
     * @var \Core\Database\Paginator\PagedResult $paginator
     */
    $pageResult = unserialize(Session::read(PAGINATOR_KEY));
    $paginatorTemplateBaseDir = self::PAGINATOR_TEMPLATE_PATH;
    $GLOBALS['pageResult'] = $pageResult;
    $GLOBALS['paginatorTemplateBaseDir'] = $paginatorTemplateBaseDir;

    require self::PAGINATOR_TEMPLATE_PATH . 'show.php';
  }
}
