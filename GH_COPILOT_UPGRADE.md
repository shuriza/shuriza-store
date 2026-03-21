# GitHub Copilot CLI Extension - Upgrade Guide

## Prerequisites

Before upgrading gh-copilot, ensure you have:

1. **GitHub CLI (gh)** installed
   ```bash
   # Check if installed
   gh --version
   ```

2. **gh-copilot extension** already installed
   ```bash
   # Check if extension is installed
   gh extension list
   ```
   
   If not installed, first install it:
   ```bash
   gh extension install github/gh-copilot
   ```

## Upgrade Steps

### Step 1: Verify Current Version
```bash
gh extension list
```

Look for `github/gh-copilot` in the output to see the current installed version.

### Step 2: Run Upgrade Command
```bash
gh extension upgrade gh-copilot
```

### Step 3: Verify Upgrade Success
```bash
gh extension list
```

Check that gh-copilot shows the updated version.

## Troubleshooting

### "gh: extension not installed" error
This means gh-copilot is not installed yet. Install it first:
```bash
gh extension install github/gh-copilot
```

### "gh: command not found"
GitHub CLI (gh) is not installed. Install it from: https://cli.github.com/

### Permission errors
You may need to run the command with appropriate permissions or check file system permissions.

## Version Information

After upgrade, you can verify the extension is working properly by running:
```bash
gh copilot help
```

This should display the help information for the Copilot CLI.
