# API Clients Quickstarts: Go

This quickstart demonstrates various usages of the the [Algolia Go API Client](https://www.algolia.com/doc/api-client/getting-started/install/python/?client=go).

## Setting up the quickstart

### Prerequisites

- An Algolia account. If you don't have one already, [create an account for free](https://www.algolia.com/users/sign-up).
- A Go >= 1.8 local environment, or [Docker](https://www.docker.com/get-started)

1. Create an Algolia Application and an [Algolia Index](https://www.algolia.com/doc/guides/getting-started/quick-start/tutorials/getting-started-with-the-dashboard/#indices)
2. Copy the file [.env.example](.env.example) and rename it to `.env` 
3. Set the environment variables `ALGOLIA_APP_ID`, `ALGOLIA_API_KEY` and `ALGOLIA_INDEX_NAME` in the `.env` file.

## How to use

Once setup, you can run each of the script in this folder using the Go command line.
Example: to execute the `simple.go` script:

```bash
go run simple.go
```

## Available quickstarts

| File | Description |
| ------------- | ------------- |
| [simple.go](./simple.go)  | Index a single object and run a search query |
| [indexing.go](./indexing.go)  | Showcase of the main indexing methods |
