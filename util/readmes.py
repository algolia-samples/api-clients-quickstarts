"""
Create README files for every language from a common template.

To add a new language:

    - add an entry to the `languages.json` file, for example:

    ```json
    {
        "language": // the name of the language,
        "code": // the common file extension,
        "localEnvironment": // any requirements for running the quickstarts locally, for example a programming language, or a package manager,
        "dependencies": // the command to install the dependencies,
        "command": // the command to run the `simple` example
    }
    ```
"""

import json
import logging
from argparse import ArgumentParser, Namespace
from string import Template
from typing import Dict

log = logging.getLogger(__name__)


def cli() -> Namespace:
    """Define command-line interface.

    Running the script generates **ALL** README files.
    """
    p = ArgumentParser(
        description="Generate READMEs for Algolia's API client quickstarts."
    )

    # the language definition file (for possible override)
    p.add_argument(
        "-l",
        "--languages",
        default="languages.json",
        type=open,
        help="A file with the language definitions.",
    )

    # which template to use
    p.add_argument(
        "-t",
        "--template",
        default="quickstart.template",
        type=open,
        help="A template file for the READMEs.",
    )

    return p.parse_args()


def render(template: Template, data: Dict[str, str]) -> str:
    """Render a tamples"""
    return template.substitute(**data)


if __name__ == "__main__":

    # Get command-line options
    args = cli()

    # Read the template file
    template = Template(args.template.read())

    # Read the data file
    languages_data = json.loads(args.languages.read())

    for lang in languages_data["languages"]:

        # Prepare the data to render into the template
        data = {
            "language": lang["name"],
            "code": lang["code"],
            "languageLowerCase": lang["name"].lower(),
            "localEnvironment": lang["localEnvironment"],
            "dependencies": lang["dependencies"],
            "command": lang["command"],
        }

        # Get the rendered README as string
        readme = render(template, data)

        # write files to disk
        readme_dir = lang.get("readmeDirectory") or lang["name"].lower()

        try:
            with open(f"../{readme_dir}/README.md", "w") as out_f:
                out_f.write(readme)
        except FileNotFoundError:
            print(
                f"No such directory: `{readme_dir}`. Ignoring. "
                f"Check, if your file `{args.languages.name}` is up-to-date."
            )
            continue
