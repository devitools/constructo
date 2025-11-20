You are responsible for publishing a new version of this PHP library to GitHub.

## Your Task

Automate the versioning and publishing process by:
1. Analyzing recent commits since the last tag
2. Suggesting the appropriate version bump (patch, minor, or major)
3. Creating an annotated git tag
4. Pushing the tag to GitHub

## Process

### 1. Get Current Version and Commits

Run these commands in parallel:
- `git describe --tags --abbrev=0` - Get the latest tag
- `git log $(git describe --tags --abbrev=0)..HEAD --oneline` - Get commits since last tag

### 2. Analyze Version Bump

Based on conventional commits since the last tag, determine the version bump:

**MAJOR (X.0.0)** - Breaking changes:
- Commits with `BREAKING CHANGE:` in body
- Commits with `!` after type (e.g., `feat!:`, `fix!:`)
- Major refactoring that changes public API

**MINOR (X.Y.0)** - New features:
- `feat:` commits
- New functionality added
- Backwards-compatible changes

**PATCH (X.Y.Z)** - Bug fixes and small improvements:
- `fix:` commits
- `refactor:` commits
- `perf:` commits
- `chore:` commits
- `docs:` commits
- `style:` commits
- `test:` commits

### 3. Present Suggestion to User

Show:
- Current version
- Commits since last tag (grouped by type)
- Suggested new version with reasoning
- Ask user to confirm or specify a different version

Example:
```
Current version: 1.6.0

Commits since last tag:
- fix(cast): correct boolify function to handle string conversions properly
- chore(config): add Claude Code lint command configuration

Suggested version: 1.6.1 (patch)
Reason: Bug fixes and configuration changes

Options:
1. Accept suggested version (1.6.1)
2. Specify custom version (e.g., 1.7.0 for minor, 2.0.0 for major)
```

### 4. Create Tag Summary

Ask the user for a concise summary description, or suggest one based on the commits.

The tag message format follows this pattern:
```
[X.Y.Z] Brief description
```

Examples from history:
- `[1.6.0] Upgrade type instruments`
- `[1.5.11] Improve interigify`
- `[1.5.10] Add new types`
- `[1.4.4] Supports array on builder`

### 5. Final Confirmation

Before creating the tag, show a clear summary and ask for explicit confirmation:

```
📦 Ready to publish version X.Y.Z

Tag: X.Y.Z
Message: [X.Y.Z] Description
Remote: origin

This will:
✓ Create annotated tag X.Y.Z locally
✓ Push tag to GitHub

Proceed? (yes/no)
```

**IMPORTANT**: Wait for explicit "yes" confirmation before proceeding.

### 6. Create and Push Tag

Only after receiving confirmation:
```bash
git tag -a "X.Y.Z" -m "[X.Y.Z] Description"
git push origin X.Y.Z
```

Then show:
- Confirmation that tag was created
- Tag name and message
- Confirmation that tag was pushed
- Link to GitHub releases: `https://github.com/devitools/constructo/releases/tag/X.Y.Z`

## Important Rules

- Always get user confirmation before creating and pushing tags
- NEVER push tags without explicit confirmation
- If no commits since last tag, inform user and stop
- Validate version format (must be semantic versioning: X.Y.Z)
- Check if tag already exists before creating
- If push fails, show error and suggest solutions

## Error Handling

If tag already exists:
- Show error message
- Ask if user wants to delete and recreate (not recommended)
- Suggest incrementing version instead

If push fails:
- Check internet connection
- Verify git remote is configured
- Show full error message
- Suggest running `git remote -v` to check remote configuration