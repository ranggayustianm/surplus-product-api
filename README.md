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

| Endpoint | Description | Request Body Type | Parameters
| ------ | ------ | ------ | ------ |
| `GET /api/categories` | Get a list of categories with pagination | Url Query | `page: integer`<br>The current category ID<br><br> `size: integer`<br>The amount of categories per page to be displayed
| `GET /api/categories/{id}` | Get one category | Url Query | `id: integer`<br>The current category ID
| `GET /api/categories/{id}/products` | Get list of products of a category | Url Query | `id: integer`<br>The current category ID
| `POST /api/categories` | Create new category | JSON Body | `name: string`<br>The name of the new category<br><br>`enable: boolean`<br>Is this enabled?
| `PUT /api/categories/{id}` | Update an existing category | Url Query (id)<br>JSON Body (others) | `id: integer`<br>The current category ID<br><br>`name: string`<br>The name of the new category<br><br>`enable: boolean`<br>Is this enabled?
| `DELETE /api/categories/{id}` | Delete a category | Url Query | `id: integer`<br>The current category ID
| `PATCH /api/categories/{id}/enable` | Enable a category | Url Query | `id: integer`<br>The current category ID
| `PATCH /api/categories/{id}/disable` | Disable a category | Url Query | `id: integer`<br>The current category ID

### Image

| Endpoint | Description | Request Body Type | Parameters
| ------ | ------ | ------ | ------ |
| `GET /api/images` | Get a list of images with pagination | Url Query | `page: integer`<br>The current image ID<br><br> `size: integer`<br>The amount of images per page to be displayed
| `GET /api/images/{id}` | Get one image | Url Query | `id: integer`<br>The current image ID
| `GET /api/images/{id}/products` | Get list of products of an image | Url Query | `id: integer`<br>The current image ID
| `POST /api/images` | Create new image | Form Data | `name: string`<br>The name of the new image<br><br>`enable: boolean`<br>Is this enabled?<br><br>`file[]: image - mime:jpeg,png,jpg,gif,svg`<br>The file to be uploaded (can be multiple files) with max size 2048 KB. Required
| `PUT /api/images/{id}`<br>Because of PHP limitations, this is accessed by `POST /api/images/{id}?_method=PUT` | Update an existing image | Url Query (id)<br>Form Data (others) | `id: integer`<br>The current image ID<br><br>`name: string`<br>The name of the new image<br><br>`enable: boolean`<br>Is this enabled?<br><br>`file: image - mime:jpeg,png,jpg,gif,svg`<br>The file to be uploaded (only one file) with max size 2048 KB. Can be ignored.
| `DELETE /api/images/{id}` | Delete an image. The image file itself will be deleted as well | Url Query | `id: integer`<br>The current image ID
| `PATCH /api/images/{id}/enable` | Enable an image | Url Query | `id: integer`<br>The current image ID
| `PATCH /api/images/{id}/disable` | Disable an image | Url Query | `id: integer`<br>The current image ID

### Product

| Endpoint | Description | Request Body Type | Parameters
| ------ | ------ | ------ | ------ |
| `GET /api/products` | Get a list of products with pagination | Url Query | `page: integer`<br>The current product ID<br><br> `size: integer`<br>The amount of products per page to be displayed
| `GET /api/products/{id}` | Get one product | Url Query | `id: integer`<br>The current product ID
| `GET /api/products/{id}/images` | Get list of images of a product | Url Query | `id: integer`<br>The current product ID
| `GET /api/products/{id}/categories` | Get list of categories of a product | Url Query | `id: integer`<br>The current product ID
| `POST /api/products` | Create new product | JSON Body | `name: string`<br>The name of the new product<br><br>`description: string`<br>The description of a product<br><br>`enable: boolean`<br>Is this enabled?<br><br>`image_ids: integer[]`<br>Array of valid image IDs (optional)<br><br>`category_ids: integer[]`<br>Array of valid category IDs (optional)
| `PUT /api/products/{id}` | Update an existing product | Url Query (id)<br>JSON Body (others) | `id: integer`<br>The current product ID<br><br>`name: string`<br>The name of the new product<br><br>`enable: boolean`<br>Is this enabled?
| `PUT /api/products/{id}/images` | Update an images of a product | Url Query (id)<br>JSON Body (others) | `id: integer`<br>The current product ID<br><br>`image_ids: integer[]`<br>Array of valid image IDs
| `PUT /api/products/{id}/categories` | Update a categories of a product | Url Query (id)<br>JSON Body (others) | `id: integer`<br>The current product ID<br><br>`category_ids: integer[]`<br>Array of valid category IDs
| `DELETE /api/products/{id}` | Delete a product | Url Query | `id: integer`<br>The current product ID
| `PATCH /api/products/{id}/enable` | Enable a product | Url Query | `id: integer`<br>The current product ID
| `PATCH /api/products/{id}/disable` | Disable a product | Url Query | `id: integer`<br>The current product ID


## Conclusion

You are now set up and ready to start developing with the Surplus Product API project! If you have any questions or issues, please do not hesitate to reach out.