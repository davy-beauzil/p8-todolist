# How work authentication ?

## Where users are stocked ?
Users are stocked on database table `user`

## How a user can be authenticated ?
A user can be authenticated by the route `/login` when he give his username and password.

## What is important files for authentication ?
- `config/packages/security.yaml`
This file ensure security of the application. It's here that we can configure the authentication system.
```yaml
security:
    #...
    providers:
        #...
        app_user_provider:
            entity:
                class: App\Entity\User
                property: username
    firewalls:
        #...
        main:
            provider: app_user_provider
            lazy: true
            pattern: ^/
            form_login:
                login_path: login # The route to the login form
                check_path: login # The same route to the check login (then, Symfony's internal workings take over)
                always_use_default_target_path: true # If true, always redirect to default_target_path
                default_target_path: / # The route to redirect to after a successful login
                provider: app_user_provider # Say to Symfony to use the provider to check the user
            logout:
                path: logout # The route to logout

    role_hierarchy:
        ROLE_ADMIN: ROLE_USER

    access_control:
        - { path: ^/login, roles: PUBLIC_ACCESS } # All people can access to login page
        #...
```

- `src/Controller/SecurityController.php`
This file is the controller of the authentication system. There are mainly Symfony's internal workings, so you probably shouldn't change something here.
    - `login` method is the route to the login form
    - `logout` method is the route to logout


- `templates/security/login.html.twig`
This is template file of the login form. You can change the form here.