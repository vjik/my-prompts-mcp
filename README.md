# My Prompts MCP

Lightweight MCP server that serves user prompts defined in Markdown files, with support for arguments.

Keep your prompt `.md` files in a repository — a dedicated git repo or a folder in an existing one — 
and connect that directory to your AI client via this MCP server. This way your prompts are versioned,
shareable, and easy to update across machines and team members.

> [!IMPORTANT]
> This project is developed and maintained by [Sergei Predvoditelev](https://github.com/vjik).
> Community support helps keep the project actively developed and well maintained.
> You can support the project using the following services:
>
> - [Boosty](https://boosty.to/vjik)
> - [CloudTips](https://pay.cloudtips.ru/p/192ce69b)
>
> Thank you for your support ❤️

## General Usage

### Installation

Download the binary for your platform from [GitHub Releases](https://github.com/vjik/my-prompts-mcp/releases).

### Create a prompt

Create a `.md` file in your prompts directory, for example `greet.md`:

```markdown
---
description: Greet a person by name
arguments:
  - name: name
    description: The person's name
    required: true
---
Please greet {{name}} in a friendly and professional way.
```

### Connecting to an MCP client

Add the following to your MCP client configuration (stdio transport):

```json
{
  "mcpServers": {
    "my-prompts": {
      "command": "/path/to/my-prompts-mcp",
      "args": ["--path=/path/to/prompts"]
    }
  }
}
```

<details>
<summary>Claude Desktop</summary>

Edit the `claude_desktop_config.json` file:

- **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
- **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`
- **Linux**: `~/.config/Claude/claude_desktop_config.json`

Add the snippet above to the file, then restart Claude Desktop.

</details>

<details>
<summary>Cherry Studio</summary>

Open **Settings → MCP Servers**, click **Add**, and fill in the server command and arguments from the snippet above.

</details>

## Documentation

### Prompt file format

Each `.md` file in the configured directory is exposed as a prompt. The file name without the `.md` extension is used as
the prompt name by default.

### Front matter fields

Optional YAML front matter at the top of the file controls how the prompt is presented to the client.

| Field         | Type   | Required | Description                                              |
|---------------|--------|----------|----------------------------------------------------------|
| `name`        | string | No       | Prompt name. Defaults to the filename without extension. |
| `title`       | string | No       | Human-readable title.                                    |
| `description` | string | No       | Short description shown to the AI client.                |
| `arguments`   | list   | No       | List of arguments (see below).                           |

### Arguments

Arguments can be defined in two forms:

**Simple** (name only):
```yaml
arguments:
  - arg_name
```

**Full object**:
```yaml
arguments:
  - name: arg_name
    description: What this argument means
    required: true
```

Each argument field:

| Field         | Type    | Required | Description                                            |
|---------------|---------|----------|--------------------------------------------------------|
| `name`        | string  | Yes      | Argument name.                                         |
| `description` | string  | No       | Description of the argument.                           |
| `required`    | boolean | No       | Whether the argument is required. Defaults to `false`. |

### Placeholders

Use `{{arg_name}}` in the prompt body. Placeholders are substituted with the argument values provided at request time.

### Client compatibility

Not all MCP clients support prompts with multiple arguments. Known clients that do: **Cherry Studio**, 
**Claude Desktop**.

### Example prompt file

```markdown
---
name: code-review
title: Code Review
description: Review code for bugs, style issues, and improvements
arguments:
  - name: language
    description: Programming language
    required: true
  - name: focus
    description: Area to focus on (e.g. security, performance, readability)
    required: false
---
Review the following {{language}} code. Identify bugs, suggest improvements,
and check for best practices. Focus area: {{focus}}.

Provide feedback in this format:
- **Bugs**: list any bugs or errors found
- **Improvements**: concrete suggestions with examples
- **Summary**: overall assessment
```

## License

The "My Prompts MCP" is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE) for more information.
