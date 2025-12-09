
# Statamic Feedbucket

Integrates [Feedbucket](https://www.feedbucket.app/) into Statamic in two ways:

- Frontend (via partial)

- CP

## How to Install
You can install this addon via Composer:

```  bash
composer  require  steadfast-collective/statamic-feedbucket
```


Optionally, publish the config file `php artisan vendor:publish --tag="statamic-feedbucket-config"` (this is only relevant if you're wanting to use Feedbucket within the CP).

## Enabling
### Front end
Add the following to your base layout's `head`, to enable feedbucket for your frontend:

```
{{ feedbucket }}
```

### Statamic CP

Within the `config/statamic-feedbucket.php` config file, there is an `cms_routes` array of route names that Feedbucket will be enabled on. We've added some sensible defaults, but you can change them to suit your needs.

  

> Please do not use wildcard routes in this array. It is not supported.
