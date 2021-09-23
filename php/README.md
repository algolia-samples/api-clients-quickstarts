# API Clients Quickstarts: PHP

This quickstart demonstrates various usages of the the [Algolia PHP API Client](https://www.algolia.com/doc/api-client/getting-started/install/python/?client=php).

## Setting up the quickstart

### Prerequisites

- An Algolia account. If you don't have one already, [create an account for free](https://www.algolia.com/users/sign-up).
- A PHP >= 7.2 local environment, or [Docker](https://www.docker.com/get-started)

1. Create an Algolia Application and an [Algolia Index](https://www.algolia.com/doc/guides/getting-started/quick-start/tutorials/getting-started-with-the-dashboard/#indices)
2. Copy the file [.env.example](.env.example) and rename it to `.env` 
3. Set the environment variables `ALGOLIA_APP_ID`, `ALGOLIA_API_KEY` and `ALGOLIA_INDEX_NAME` in the `.env` file.

## How to use

Once setup, you can run each of the script in this folder using the PHP command line.
Example: to execute the `simple.php` script:

```bash
php simple.php
```

## Available quickstarts

| File | Description |
| ------------- | ------------- |
| [simple.php](./simple.php)  | Index a single object and run a search query |
| [indexing.php](./indexing.php)  | Showcase of the main indexing methods |
