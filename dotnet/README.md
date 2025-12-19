# API Clients Quickstarts: .NET

This quickstart demonstrates various usages of the the [Algolia .NET API Client](https://www.algolia.com/doc/api-client/getting-started/install/csharp/?client=csharp).

## Setting up the quickstart

### Prerequisites

- An Algolia account. If you don't have one already, [create an account for free](https://www.algolia.com/users/sign_up).
- A [compatible .NET local environment](https://github.com/algolia/algoliasearch-client-csharp#-features), or [Docker](https://www.docker.com/get-started).

<details>
  <summary>Using VSCode</summary>

  By using VScode and having the [Visual Studio Code Remote - Containers](https://code.visualstudio.com/docs/remote/containers) extension installed, you can run any of the quickstarts by using the command [Remote-Containers: Open Folder in Container](https://code.visualstudio.com/docs/remote/containers#_quick-start-open-an-existing-folder-in-a-container) command.
  
  Each of the quickstart contains a [.devcontainer.json](./.devcontainer/devcontainer.json), along with a [Dockerfile](./.devcontainer/Dockerfile).
</details>

1. Create an Algolia Application and an [Algolia Index](https://www.algolia.com/doc/guides/getting-started/quick-start/tutorials/getting-started-with-the-dashboard/#indices)
2. Copy the file [.env.example](.env.example) and rename it to `.env` 
3. Set the environment variables `ALGOLIA_APP_ID`, `ALGOLIA_API_KEY` and `ALGOLIA_INDEX_NAME` in the `.env` file. You can obtain those from the [Algolia Dashboard](https://www.algolia.com/api-keys/). The `ALGOLIA_API_KEY` should be the "Admin API Key" (necessary for indexing).

## How to use

Once setup, you can run each of the sample in this folder using the .NET command line.
Example: to execute the `simple` sample:

```bash
dotnet run simple
```

## Available quickstarts

| File | Description |
| ------------- | ------------- |
| [simple](./Program.cs#54)  | Index a single object and run a search query |
| [indexing](./Program.cs#81)  | Showcase of the main indexing methods |
| [change-index-settings](./Program.cs#259)  | Change index settings |
| [rules](./Program.cs#291)  | 	Export rules and add a new rule to an index |
| [backup](./Program.cs#340)  | Backup an index |
| [restore](./Program.cs#439)  | Restore an index |
| [rest-api-return-top-hits](./Program.cs#522)  | Get top 1000 searches with Analytics REST API |
| [generate-key](./Program.cs#553)  | Generate a rate-limited search only API key |
