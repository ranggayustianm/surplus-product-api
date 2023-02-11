# Surplus Product API (PHP Laravel 8.0)

This is a README file for the Surplus Product API project. In this file, you will find information on how to set up your local development environment, run migrations and seeds, and start the project.

## Setting Up Your Development Environment

1. Install MySQL and create a database with the name surplus_product_api.
2. Clone the repository and navigate to the project directory.
3. Install dependencies by running composer install.
4. Create a copy of the .env.example file and name it .env. In the .env file, update the database connection details to match the database you just created.
5. Run the following command to run all migrations and seeds 
```
php artisan migrate --seed
```


## Running the Project

1. To start the development server, run `php artisan serve` in your terminal.
2. The API will now be accessible at http://localhost:8000.
Examples of the API usage is accessible by importing the JSON file named `Surplus Product API.postman_collection.json` in Postman. The JSON file is located at the root of this project.

## API Endpoints
### Category

| Endpoint | Description | Parameters
| ------ | ------ | ------ |
| GET /api/categories | Get a list of categories with pagination | page: integer<br>The current category ID<br> size: integer<br>The amount of categories per page to be displayed
| GET /api/categories/{id} | Get one category | id: integer
| GET /api/categories/{id}/products | Get list of products of a category | id: integer


## Conclusion

You are now set up and ready to start developing with the Surplus Product API project! If you have any questions or issues, please do not hesitate to reach out.