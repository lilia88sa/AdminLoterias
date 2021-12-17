** Coding Standard ** 

    • For variables, class namings and namespaces we'll use camelcase. 
        Ex: UserAuthorizationChecker, $userChecker
    • Routes should be on kebab-case format and ALWAYS lowercase.  
        Ex: create-user, profile-update
    • Use controllers onle if they will involve many actions in it, otherwise 
      we must use a custom ActionClass. 
        Ex: "UserController" if it will involve UpdateProfileAction, ChangePasswordAction, etc., (En lugar de usar 
        Ex: "UpdateProfileAction" for single or isolated actions. 
    
**Setting up local development environment:**

 1) Configuring LexikJWTAuthenticationBundle (docs on: https://github.com/lexik/LexikJWTAuthenticationBundle):
 
    On root project's run the following commands to generate your local private/public key pair:
    
    `$ mkdir -p config/jwt`
    
    `$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096`
    
    `$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`
    
    `$ chmod 644 public.pem private.pem`

 2) Create database by running the following command: 
 
    `php bin/console doctrine:database:create`
    
 3) Update your database schema by running:
 
    `php bin/console doctrine:migrations:migrate`
    
 4) Run fixtures:
 
    `php bin/console doctrine:fixtures:load`
    
    This wil create a default admin account:
        user: admin
        password: admin
        role: ROLE_ADMIN
        
    And will also fill the database with fake generated data.
    
 5) Run Symfony Local Web Server, with any of these commands (docs: https://symfony.com/doc/current/setup/symfony_server.html):
 
    `symfony server:start`
    
    `php bin/console server:start`
    
 ** Para consultar las rutas disponibles en la aplicación:
 
    `php bin/console debug:router` 
    
