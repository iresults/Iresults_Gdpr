Iresults_Gdpr
=============

Magento 1 module providing some user privacy tools


Installation
------------

Install with [modman](https://github.com/colinmollenhour/modman.git):

```bash
modman clone https://github.com/iresults/Iresults_Gdpr.git
```


Commands
--------

To get an overview of all available commands run

```bash
php shell/gdpr.php
```

### customer:show

Display information about a Customer

```bash
php shell/gdpr.php customer:show --email user@domain.tld --website-id 0
```


### sales:order:show

Display information about an Order

```bash
php shell/gdpr.php sales:order:show --id 100000114
```

Add `-v` to also display the Order's Quote.


### sales:order:list

List Orders matching the filter(s)

```bash
php shell/gdpr.php sales:order:list --no-guests --age 112
```

#### Available filters

- `customer [value]` Show Orders of the given Customer
- `age [days]` Show only Orders older than the given number of days
- `email [customer@domain.tld]` Show only Orders associated to the given email
- `guests` Show only guest Orders
- `no-guests` Hide all guest Orders


### sales:order:delete

Delete the given Order

```bash
php shell/gdpr.php sales:order:delete --id 100000118
```

Add `--force` to really delete the Order, otherwise it will only be simulated


### sales:order:delete-all

Delete all Orders matching the filter(s)

```bash
php shell/gdpr.php sales:order:delete-all --guests --age 112
```

See `sales:order:list` for the available filter arguments.

Add `--force` to really delete the Order, otherwise it will only be simulated

