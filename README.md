# filter-eloquent
Quickly filter data by concatenating query strings
### Install

```
composer install imzeali/filter-eloquent
```
### Supported operators
```
  ' '           // equal to
  'eq'          // equal to
  'ne'          // not equal to
  'gt'          // Is greater than
  'ge'          // great than and equal to
  'lt'          // less than
  'le'          // less than and equal to 
  'like'        // LIKE
  'in'          // IN
  'not_in'      // NOT IN
```
### Usage
$q syntax：{field name}__{operator}={query condition}

Basis query：
```php
$q = 'id__eq=100';
$query = new Filter(new User(), $q)->filteredQuery()->get();
//Equivalent to
User::where('id',1)->get();
```

Multiple query conditions：
```php
$q = 'id__gt=100,name__like=%baby%';
$query = new Filter(new User(), $q)->filteredQuery()->get();
//Equivalent to
User::where('id', ‘>’, ‘100’)->where('name','like','%baby%');
```

Model query conditions
```php
$q = 'user.city__gt=Fuzhou';
$query = new Filter(new User(), $q)->filteredQuery()->get();
//Equivalent to
Article::whereHas('user', function ($query){
    $query->where('city', '=', 'Fuzhou');
});
```
