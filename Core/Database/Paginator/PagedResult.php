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

use Opis\Closure\SerializableClosure;

class PagedResult
{
  private int $totalOfPages;
  private int $totalOfElements;
  private int $pageSize;
  private int $pageNumber;
  private array $items;
  private Query $query;
  private SerializableClosure $mapper;


  /**
   * Get the value of totalOfPages
   */
  public function getTotalOfPages(): int
  {
    return $this->totalOfPages;
  }

  /**
   * Set the value of totalOfPages
   */
  public function setTotalOfPages(int $totalOfPages): self
  {
    $this->totalOfPages = $totalOfPages;

    return $this;
  }

  /**
   * Get the value of totalOfElements
   */
  public function getTotalOfElements(): int
  {
    return $this->totalOfElements;
  }

  /**
   * Set the value of totalOfElements
   */
  public function setTotalOfElements(int $totalOfElements): self
  {
    $this->totalOfElements = $totalOfElements;

    return $this;
  }

  /**
   * Get the value of pageSize
   */
  public function getPageSize(): int
  {
    return $this->pageSize;
  }

  /**
   * Set the value of pageSize
   */
  public function setPageSize(int $pageSize): self
  {
    $this->pageSize = $pageSize;

    return $this;
  }

  /**
   * Get the value of pageNumber
   */
  public function getPageNumber(): int
  {
    return $this->pageNumber;
  }

  /**
   * Set the value of pageNumber
   */
  public function setPageNumber(int $pageNumber): self
  {
    $this->pageNumber = $pageNumber;

    return $this;
  }

  /**
   * Get the value of items
   */
  public function getItems(): array
  {
    return $this->items;
  }

  /**
   * Set the value of items
   */
  public function setItems(array $items): self
  {
    $this->items = $items;

    return $this;
  }

  /**
   * Get the value of query
   */
  public function getQuery(): Query
  {
    return $this->query;
  }

  /**
   * Set the value of query
   */
  public function setQuery(Query $query): self
  {
    $this->query = $query;

    return $this;
  }

  /**
   * Get the value of mapper
   */
  public function getMapper(): callable
  {
    return $this->mapper;
  }

  /**
   * Set the value of mapper
   */
  public function setMapper(\Closure $mapper): self
  {
    $this->mapper = new SerializableClosure($mapper);

    return $this;
  }
}
