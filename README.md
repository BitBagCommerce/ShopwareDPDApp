## Introduction

This repo contains a skeleton for developing new cloud-based apps for Shopware. For more information about Shopware apps visit [this page](https://developer.shopware.com/docs/guides/plugins/apps/app-base-guide).

## Local development with docker-compose

In your local Shopware installation directory add the following code in your docker-compose.yml:

```yaml
networks:
    shopware:
      
    # Add these lines
    development:
    appSystem:

services:
  app_server:
    image: shopware/development:7.4-composer-2
    networks:
      shopware:
        aliases:
          - docker.vm
            
      # Add these lines
      development:
        aliases:
            - shopware
    extra_hosts:
      - "docker.vm:127.0.0.1"
    volumes:
      - ~/.composer:/.composer
    tmpfs:
      - /tmp:mode=1777

```

You also need to add containers that your app will use. In the same file add these lines:

```yaml
  example_app:
        image: shopware/development:local
        volumes:
            
            # Remember about changing your app's directory location
            - "../AppTemplate:/app"
            - "~/.composer:/.composer"
        environment:
            CONTAINER_UID: 1000
            APPLICATION_UID: 1000
            APPLICATION_GID: 1000
        ports:
            - "127.0.0.1:7777:8000"
        networks:
            appSystem:
            development:
                aliases:
                    - example-app

  example_app_db:
    build: dev-ops/docker/containers/mysql
    environment:
        MYSQL_ROOT_PASSWORD: root
        MYSQL_USER: app
        MYSQL_PASSWORD: app
    ports:
        - "5506:3306"
    volumes:
        - ./dev-ops/docker/_volumes/mysql-example:/mysql-data
    networks:
        appSystem:
            aliases:
                - example-app-mysql

```

You can adjust the ports as you see fit.

## What it does

This is a standard Symfony application that contains hundreds of lines of code that you'd need anyway to create a means of authenticating & authorizing requests coming from a Shopware store.
It stores the API keys that will later be used in your services to make requests to a Shopware instance.

It also contains argument resolvers for your controllers so provided you want to create a controller that listens for some events, actions can have the following signature:

```php
class ProductController extends AbstractController
{
    public function __invoke(EventInterface $event)
    {
        // Act upon the event based on shop id:
        if ($event->getShopId === '...') {
            // ...
        }
    }
}
```

It also contains a client resolver so it's easy to send requests to Shopware directly from your controller or delegating them to other services:

```php
final class CustomerController extends AbstractController
{
    private const CUSTOMER_WRITTEN_EVENT = 'customer.written';

    private AdminEmailServiceInterface $emailService;
    
    // ...

    public function __invoke(EventInterface $event, ClientInterface $client)
    {
        $eventData = $event->getEventData();
        $eventType = $eventData['event'] ?? null;
        
        if ($eventType === self::CUSTOMER_WRITTEN_EVENT) {
        
            // The client knows what store it'll be making requests to
            $this->emailService->notifyAdmin($event, $client);
        }
    }
}
```

## How it works

It wraps the raw communication between Shopware and your app. Mainly:
- It can register and confirm [the registration of your app](https://developer.shopware.com/docs/guides/plugins/apps/app-base-guide#setup)
- It stores API keys to be used later by auto-injectable client instances
- It has a scaffolding for reacting to [app lifecycle events](https://developer.shopware.com/docs/guides/plugins/apps/app-base-guide#app-lifecycle-events)
- It handles all authentication and authorization in the background:
  - secret keys,
  - hmac signatures,
  - basically it's a swiss knife.
