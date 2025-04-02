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

$q can be spliced by the client

Basis query：
```php
$q = 'id__eq=100';
new Filter(new User(), $q)->filteredQuery();
//mean
User::where('id',1);
```

Multiple query conditions：
```php
$q = 'id__gt=100,name__like=%baby%';
new Filter(new User(), $q)->filteredQuery();
//User::where('id', ‘>’, ‘100’)->where('name','like','%baby%');

```

Model query conditions
```php
$q = 'user.city__eq=Fuzhou';
new Filter(new Article(), $q)->filteredQuery();
//Article::whereHas('user', function ($query){
//    $query->where('city', '=', 'Fuzhou');
//});
```
