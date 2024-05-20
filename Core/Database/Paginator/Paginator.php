<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace Core\Database\Paginator;

use Core\Database\ConnectionManager;
use Core\Utils\Session;

final class Paginator
{
  private static \PDO $connection;

  public static function initialize()
  {
    if (empty(self::$connection)) {
      self::$connection = (new ConnectionManager())->getConnection();
    }
  }

  public static function paginate(Query $query, int $pageSize, int $page, ?\CLosure $mapper): PagedResult
  {
    self::initialize();

    $pagedResult = new PagedResult();
    $pagedResult->setPageSize($pageSize);
    $pagedResult->setPageNumber($page);
    $pagedResult->setQuery($query);
    $pagedResult->setMapper($mapper);
    $pagedResult->setItems([]);

    try {
      $statement = self::buildCountQuery($query, self::$connection);
      $statement->execute();

      $result = $statement->fetch(\PDO::FETCH_ASSOC);
      $totalElements = intval($result['totalElements']);

      $pagedResult->setTotalOfElements($totalElements);

      if ($totalElements == 0) {
        $pagedResult->setTotalOfPages(0);
        $pagedResult->setPageNumber(0);

        return $pagedResult;
      } else {
        if ($totalElements < $pageSize) {
          $pagedResult->setTotalOfPages(1);
        } else {
          $modulo = $totalElements - ($totalElements % $pageSize);
          $nbPages = ($totalElements % $pageSize) > 0 ? ($modulo / $pageSize) + 1 : $modulo / $pageSize;

          $pagedResult->setTotalOfPages($nbPages);
        }
      }
    } catch (\Throwable $e) {
      throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
    }

    try {
      $statement = self::buildSelectQuery($query, $pageSize, $page, self::$connection);
      $statement->execute();

      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      $pagedResult->setItems(isset($mapper) ? call_user_func_array($mapper, [$result]) : $result);
    } catch (\Throwable $e) {
      throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
    }

    Session::write(PAGINATOR_KEY, serialize($pagedResult));

    return $pagedResult;
  }

  public static function getPage(int $page): PagedResult
  {
    /**
     * @var PagedResult $pageOptions
     */
    $pageOptions = unserialize(Session::consume(PAGINATOR_KEY));

    if (empty($pageOptions)) {
      throw new \Exception("There is no pagination context", 1);
    }

    self::initialize();
    return self::changePage($pageOptions, $page);
  }

  public static function first(): PagedResult
  {
    /**
     * @var PagedResult $pageOptions
     */
    $pageOptions = unserialize(Session::consume(PAGINATOR_KEY));

    if (empty($pageOptions)) {
      throw new \Exception("There is no pagination context", 1);
    }

    self::initialize();
    return self::changePage($pageOptions, 1);
  }

  public static function next(): PagedResult
  {
    /**
     * @var PagedResult $pageOptions
     */
    $pageOptions = unserialize(Session::consume(PAGINATOR_KEY));

    if (empty($pageOptions)) {
      throw new \Exception("There is no pagination context", 1);
    }

    self::initialize();

    if ($pageOptions->getPageNumber() >= $pageOptions->getTotalOfPages()) {
      return $pageOptions;
    }

    return self::changePage($pageOptions, $pageOptions->getPageNumber() + 1);
  }

  public static function previous(): PagedResult
  {
    /**
     * @var PagedResult $pageOptions
     */
    $pageOptions = unserialize(Session::consume(PAGINATOR_KEY));

    if (empty($pageOptions)) {
      throw new \Exception("There is no pagination context", 1);
    }

    self::initialize();

    if ($pageOptions->getPageNumber() <= 1) {
      return $pageOptions;
    }

    return self::changePage($pageOptions, $pageOptions->getPageNumber() - 1);
  }

  public static function last(): PagedResult
  {
    /**
     * @var PagedResult $pageOptions
     */
    $pageOptions = unserialize(Session::consume(PAGINATOR_KEY));

    if (empty($pageOptions)) {
      throw new \Exception("There is no pagination context", 1);
    }

    self::initialize();

    return self::changePage($pageOptions, $pageOptions->getTotalOfPages());
  }

  private static function changePage(PagedResult $pageOptions, int $page): PagedResult
  {
    $pageOptions->setPageNumber($page);

    try {
      $statement = self::buildSelectQuery($pageOptions->getQuery(), $pageOptions->getPageSize(), $pageOptions->getPageNumber(), self::$connection);
      $statement->execute();

      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

      $pageOptions->setItems(!empty($pageOptions->getMapper()) ? call_user_func_array($pageOptions->getMapper(), [$result]) : $result);
    } catch (\Throwable $e) {
      throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
    }

    Session::write(PAGINATOR_KEY, serialize($pageOptions));

    return $pageOptions;
  }

  private static function buildCountQuery(Query $query, \PDO $connection): \PDOStatement
  {
    $sql = "SELECT COUNT(*) as totalElements FROM " . $query->getTable();
    if (!empty($query->getJoin())) {
      foreach ($query->getJoin() as $join) {
        $sql .= " JOIN " . $join;
      }
    }

    if (!empty($query->getConditions())) {
      $sql .= " WHERE " . $query->getConditions();
    }

    $statement = $connection->prepare($sql);

    if (!empty($query->getParams())) {
      foreach ($query->getParams() as $param) {
        $statement->bindParam($param[0], $param[1], $param[2]);
      }
    }

    return $statement;
  }

  private static function buildSelectQuery(Query $query, int $pageSize, int $page, \PDO $connection): \PDOStatement
  {
    $sql = "SELECT " . $query->getSelect() . " FROM " . $query->getTable();
    if (!empty($query->getJoin())) {
      foreach ($query->getJoin() as $join) {
        $sql .= " JOIN " . $join;
      }
    }

    if (!empty($query->getConditions())) {
      $sql .= " WHERE " . $query->getConditions();
    }

    if (!empty($query->getOrder())) {
      $sql .= " ORDER BY " . $query->getOrder();
    }

    $offset = ($page - 1) * $pageSize;

    $sql .= " LIMIT :limit OFFSET :offset";

    $statement = $connection->prepare($sql);
    $statement->bindParam(":limit", $pageSize, \PDO::PARAM_INT);
    $statement->bindParam(":offset", $offset, \PDO::PARAM_INT);

    if (!empty($query->getParams())) {
      foreach ($query->getParams() as $param) {
        $statement->bindValue($param[0], $param[1], $param[2]);
      }
    }

    return $statement;
  }
}
