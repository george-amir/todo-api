# Basic TODO App RESTful API based on Symfony v3.4
==================================================

## About

A Basic RESTful API todo application build with symfony v3.4 which follow these rules:
- All Api routes return in json format 
- All Api requests must be in json format with application/json header
- All API routes require authentication with Authorization header (Ex: Bearer ODFlMGIwNzM2Yzk2Y2Y4NG.....)

The following bundles is used:
- FOSOAuthServerBundle
- FOSRestBundle.
- NelmioApiDocBundle.
- FOSUserBundle.
- JMSSerializerBundle..  

## Rrequirements

The application require php v7.0.0 or greater

## Installation

1- Clone the repo
```
 git clone https://github.com/george-amir/todo-api.git
```

2- Change directory to todo-api and run composer
```
cd todo-api
composer install
```

3- Make sure that the database configuration in app/config/parameters.yml is correct

4- Run the following command to create the application tables
```
php bin/console doctrine:schema:update --force
``` 

5- Run the following command to generate client_id, client_secret and app_secret
```
php bin/console todo-app:install
``` 

6- Run the server
```
php bin/console server:run
```

you can now go to api/register to register a user then you can use the application

## Testing

All routes has been tested using phpunit with the following test cases:
- [x] Register new user with right parameters
- [x] Register new user with existed username
- [x] Register new user with existed email
- [x] Register new user with missing parameters
- [x] User login with right credentials
- [x] User login with wrong credentials
- [x] User login with missing parameters
- [x] Create new list with right parameters
- [x] Create new list with missing parameters
- [x] Get all user lists
- [x] Access spesific list owned by the user
- [x] Access spesific list doesn't owned by the user
- [x] Edit existing list with right parameters
- [x] Edit existing list with missing parameters
- [x] Create new item in an existing list with right parameters
- [x] Create new item in an existing list with missing parameters
- [x] Create an item in a list doesn't exist
- [x] Edit item in an existing list with right parameters
- [x] Edit item doesn't owned by the user
- [x] Edit item in an existing list with missing parameters
- [x] Delete item in an existing list with right parameters
- [x] Delete item doesn't owned by the user
- [x] Delete existing list owned by the user
- [x] Delete existing list doesn't owned by the user
