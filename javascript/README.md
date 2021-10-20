# API Clients Quickstarts: JavaScript

This quickstart demonstrates various usages of the [Algolia JavaScript API Client](https://www.algolia.com/doc/api-client/getting-started/install/javascript/?client=javascript).

## Setting up the quickstart

### Prerequisites

- An Algolia account. If you don't have one already, [create an account for free](https://www.algolia.com/users/sign_up).
- A Node local environment, or [Docker](https://www.docker.com/get-started).

<details>
  <summary>Using VSCode</summary>

By using VScode and having the [Visual Studio Code Remote - Containers](https://code.visualstudio.com/docs/remote/containers) extension installed, you can run any of the quickstarts by using the command [Remote-Containers: Open Folder in Container](https://code.visualstudio.com/docs/remote/containers#_quick-start-open-an-existing-folder-in-a-container) command.

Each of the quickstart contains a [.devcontainer.json](./.devcontainer/devcontainer.json), along with a [Dockerfile](./.devcontainer/Dockerfile).

</details>

1. Create an Algolia Application and an [Algolia Index](https://www.algolia.com/doc/guides/getting-started/quick-start/tutorials/getting-started-with-the-dashboard/#indices)
2. Copy the file [.env.example](.env.example) and rename it to `.env`
3. Set the environment variables `ALGOLIA_APP_ID`, `ALGOLIA_API_KEY` and `ALGOLIA_INDEX_NAME` in the `.env` file. You can obtain those from the [Algolia Dashboard](https://www.algolia.com/api-keys/). The `ALGOLIA_API_KEY` should be the "Admin API Key" (necessary for indexing).

## How to use

Once setup, you can run each of the script in this folder using the Node command line.
Example: to execute the `simple.js` script:

```bash
node simple.js
```

## Available quickstarts


| File                         | Description                                  |

| ---------------------------- | -------------------------------------------- |

| [simple.js](./simple.js)     | Index a single object and run a search query |

| [indexing.js](./indexing.js) | Showcase of the main indexing methods        |
