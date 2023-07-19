# API Clients Quickstarts: Ruby

This quickstart demonstrates various usages of the the [Algolia Ruby API Client](https://www.algolia.com/doc/api-client/getting-started/install/python/?client=ruby).

## Setting up the quickstart

### Prerequisites

- An Algolia account. If you don't have one already, [create an account for free](https://www.algolia.com/users/sign_up).
- A Ruby ^2.2 local environment, or [Docker](https://www.docker.com/get-started).

<details>
  <summary>Using VSCode</summary>

  By using VScode and having the [Visual Studio Code Remote - Containers](https://code.visualstudio.com/docs/remote/containers) extension installed, you can run any of the quickstarts by using the command [Remote-Containers: Open Folder in Container](https://code.visualstudio.com/docs/remote/containers#_quick-start-open-an-existing-folder-in-a-container) command.
  
  Each of the quickstart contains a [.devcontainer.json](./.devcontainer/devcontainer.json), along with a [Dockerfile](./.devcontainer/Dockerfile).
</details>

1. Create an Algolia Application and an [Algolia Index](https://www.algolia.com/doc/guides/getting-started/quick-start/tutorials/getting-started-with-the-dashboard/#indices)
2. Copy the file [.env.example](.env.example) and rename it to `.env` 
3. Set the environment variables `ALGOLIA_APP_ID`, `ALGOLIA_API_KEY` and `ALGOLIA_INDEX_NAME` in the `.env` file. You can obtain those from the [Algolia Dashboard](https://www.algolia.com/api-keys/). The `ALGOLIA_API_KEY` should be the "Admin API Key" (necessary for indexing).

## How to use

Once setup, you can run each of the script in this folder using the PHP command line.
Example: to execute the `simple.rb` script:

```bash
ruby simple.rb
```

## Available quickstarts

| File | Description |
| ------------- | ------------- |
| [simple.rb](./simple.rb)  | Index a single object and run a search query |
| [indexing.rb](./indexing.rb)  | Showcase of the main indexing methods |
| [generate_key.rb](./generate_key.rb)  | Generate a search only rate limited API key |
| [index_settings.rb](./index_settings.rb)  | Change index settings |

