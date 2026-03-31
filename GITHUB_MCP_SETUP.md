# GitHub MCP Server Setup

This guide explains how to set up GitHub MCP (Model Context Protocol) server to connect AI assistants to your GitHub repositories.

## Prerequisites

- Node.js 18+ installed
- GitHub Personal Access Token

## Setup Steps

### 1. Generate GitHub Personal Access Token

1. Go to GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click "Generate new token (classic)"
3. Give it a name (e.g., "MCP Integration")
4. Select scopes:
   - `repo` - Full control of private repositories
   - `read:user` - Read user profile data
5. Click "Generate token"
6. Copy the generated token

### 2. Set Environment Variable

Add to your `.env` file:

```env
GITHUB_TOKEN=your_github_personal_access_token_here
```

Or export in terminal:

```bash
export GITHUB_TOKEN=your_github_personal_access_token_here
```

### 3. Install Dependencies

```bash
npm install -g @modelcontextprotocol/server-github
```

### 4. Configure MCP Client

The configuration is saved in `mcp-servers.json`:

```json
{
  "mcpServers": {
    "github": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-github"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "${GITHUB_TOKEN}"
      }
    }
  }
}
```

### 5. Test the Connection

Run:

```bash
npx @modelcontextprotocol/server-github
```

You should see the server start successfully.

## Available Tools

Once connected, you can use these tools:

- `get_repo` - Get repository information
- `list_repos` - List user repositories
- `search_repositories` - Search repositories
- `get_file_contents` - Read file from repository
- `create_issue` - Create a new issue
- `list_issues` - List repository issues
- `create_pull_request` - Create a pull request
- `list_pull_requests` - List pull requests
- `get_pull_request` - Get pull request details

## Usage with Claude/OpenCode

When using AI assistants that support MCP, the AI can:
- Read your code from GitHub
- Create issues
- Manage pull requests
- Search through repositories
- And more!

## Troubleshooting

### Token not found
Make sure the `GITHUB_TOKEN` environment variable is set correctly.

### Permission denied
Check that your token has the required scopes (`repo`, `read:user`).

### Server won't start
Ensure Node.js 18+ is installed:
```bash
node --version
```

## Resources

- [MCP Documentation](https://modelcontextprotocol.io)
- [GitHub MCP Server](https://github.com/modelcontextprotocol/server-github)
- [GitHub API](https://docs.github.com/en/rest)
