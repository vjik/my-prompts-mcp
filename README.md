# My Prompts MCP

![GitHub Release](https://img.shields.io/github/v/release/vjik/my-prompts-mcp)

Lightweight MCP server that serves user prompts defined in Markdown files, with support for arguments.

Keep your prompt `.md` files in a repository — a dedicated git repo or a folder in an existing one — 
and connect that directory to your AI client via this MCP server. This way your prompts are versioned,
shareable, and easy to update across machines and team members.

_Example of My Prompts MCP usage with [Cherry Studio](https://cherry-ai.com/):_

https://github.com/user-attachments/assets/32dfa36d-657f-4b9b-8086-c7a00ffe3185

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
    "my-prompts-mcp": {
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

> [!IMPORTANT]
> Prompts without arguments or with one argument are supported by almost all MCP clients. Prompts with
> two or more arguments are not universally supported. Known clients that do support multiple arguments:
> - [Cherry Studio](https://cherry-ai.com/),
> - [Claude Desktop](https://claude.com/download).

### Placeholders

Use `{{arg_name}}` in the prompt body. Placeholders are substituted with the argument values provided at request time.

### Example prompt file

```markdown
---
name: name-generator
title: Name Generator
description: Generate a name for a product, project, or company
arguments:
  - name: description
    description: What needs to be named (product, project, company, etc.)
    required: true
  - name: style
    description: Naming style (e.g. minimalist, creative, technical, playful)
    required: false
---
Generate 10 name ideas for: {{description}}.

{{style}}

Requirements for the names:
- Easy to remember and pronounce
- Suitable for use as a domain name
- Unique and distinctive

For each name provide a one-line explanation of why it works.
```

## License

The "My Prompts MCP" is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE) for more information.
