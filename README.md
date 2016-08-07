# Pagination

Universal pagination class

## Install
```
composer require zver/pagination
```

## Usage

```php
<?php

use Zver\Pagination;

Pagination::create()
          
          /**
          * Array or PaginationInterface implemented class
          */
          ->setItems([1,2,3,4,5,6])
          
          /**
          * Items per page
          */
          ->setItemsPerPage(20)
          
          /**
          * Here you must define URL generation callback.
          */
          ->setPageUrlCallback(
              function ($number)
              {
                  /**
                  * For example
                  */
                  return "/items?page=" . $number;
              }
          )
          
          /**
          * Here you must define callback which returns current page number.
          */
          ->setCurrentPageCallback(
              function ()
              {
                  /**
                  * For example
                  */
                  
                  if(isset($_GET['page']) && is_numeric($_GET['page']))
                  {
                    return $_GET['page'];
                  }
                  
                  return 1;
              }
          )
          
          /**
          * Here you can render items html as you want.
          */
          ->showItems(
              function ($items, Pagination $pagination)
              {
                  
              }
          )
          
          /**
          * Here you can render pages html as you want.
          * Callback will executed only if pages count > 1
          */
          ->showPages(
              function ($pages, Pagination $pagination)
              {
                 
              }
);
```
