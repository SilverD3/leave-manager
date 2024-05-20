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

class Query
{
  /**
   * comma-separated list of columns to select
   */
  private string $select;

  /**
   * Table name
   */
  private string $table;

  /**
   * Array containing joined tables
   * @example example1: ["roles r ON r.id = users.role_id"]
   */
  private array $join;

  /**
   * String representing conditions
   * @example example1: "role_id = ? AND u.username LIKE %?%"
   */
  private string $conditions;

  /**
   * Specify result ordering with the format: $columnName direction
   * @var string $order
   * @example example1: created ASC
   */
  private string $order;

  /**
   * Query params in formatted like: array<[identifier, value, type]>
   * @var array $params
   * @example example1: [[1, "like", \PDO::PARAM_STR]]
   */
  private array $params;

  /**
   * Get the value of select
   */
  public function getSelect(): string
  {
    return $this->select;
  }

  /**
   * Set the value of select
   */
  public function setSelect(string $select): self
  {
    $this->select = $select;

    return $this;
  }

  /**
   * Get the value of table
   */
  public function getTable(): string
  {
    return $this->table;
  }

  /**
   * Set the value of table
   */
  public function setTable(string $table): self
  {
    $this->table = $table;

    return $this;
  }

  /**
   * Get the value of join
   */
  public function getJoin(): array
  {
    return $this->join;
  }

  /**
   * Set the value of join
   */
  public function setJoin(array $join): self
  {
    $this->join = $join;

    return $this;
  }

  /**
   * Get the value of conditions
   */
  public function getConditions(): string
  {
    return $this->conditions;
  }

  /**
   * Set the value of conditions
   */
  public function setConditions(string $conditions): self
  {
    $this->conditions = $conditions;

    return $this;
  }

  /**
   * Get the value of order
   */
  public function getOrder(): string
  {
    return $this->order;
  }

  /**
   * Set the value of order
   */
  public function setOrder(string $order): self
  {
    $this->order = $order;

    return $this;
  }

  /**
   * Get the value of params
   */
  public function getParams(): array
  {
    return $this->params;
  }

  /**
   * Set the value of params
   */
  public function setParams(array $params): self
  {
    $this->params = $params;

    return $this;
  }
}
