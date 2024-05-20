# Core\Database\Paginator

Core class that can be used to paginate queries


## Usage

Create an object of type [Query](Query.php) to configure your query and page options and pass that object to [Paginator::paginate](Paginator.php). This method returns an object of type [PagedResult](PagedResult.php). Check every type evolved for more details.

Pagination methods inside [Paginator](Paginator.php) use [Session](../../Utils/Session.php) utilities to automatically manage actions like move to next or previous page.

To display paination controls, use `show` method of [Paginator Helper](../../View/Helper/PaginatorHelper.php). That helper relies on se [Session](../../Utils/Session.php) to load memoized page infos.

## Example

In a service:

```php
use Core\Database\Paginator\PagedResult;
use Core\Database\Paginator\Paginator;
use Core\Database\Paginator\Query;

class UserService
{
  public function getAll(): PagedResult
  {
    $query = new Query;
    $query->setTable("users e");
    $query->setSelect("*");
    $query->setJoin([]);
    $query->setConditions("deleted = :deleted");
    $query->setOrder('id DESC');
    $query->setParams([[":deleted", 0, \PDO::PARAM_BOOL]]);

    // getMapper() is a \Closure that transforms query raw results 
    // into object of type User
    return Paginator::paginate($query, 20, 1, $this->getMapper());
  }
}
```

In a controller:

```php
use Core\Database\Paginator\Paginator;
use Core\Utils\Session;

class UserController
{
  // code before ...

  public function getAll(){
    if (isset($_GET['page_action']) && Session::check(PAGINATOR_KEY)) {
      switch ($_GET['page_action']) {
        case 'first':
          $users = Paginator::first()->getItems();
          break;
        case 'next':
          $users = Paginator::next()->getItems();
          break;
        case 'previous':
          $users = Paginator::previous()->getItems();
          break;
        case 'last':
          $users = Paginator::last()->getItems();
          break;

        default:
          $users = $this->service->getPagedAll(true)->getItems();
          break;
      }
    } elseif (isset($_GET['page']) && is_numeric($_GET['page']) && Session::check(PAGINATOR_KEY)) {
      $users = Paginator::getPage(intval($_GET['page']))->getItems();
    } else {
      $users = $this->service->getPagedAll(true)->getItems();
    }
  }

  // code after ...
}
```

In a view:

```php
use Core\View\Helper\PaginatorHelper;

PaginatorHelper::show();
```

## Customization

Templates used to render controls are located in [Views](../../View/Elements/Paginator). Feel free to edit them.