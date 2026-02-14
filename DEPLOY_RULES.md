# Wiwa Tour Deployment Rules & Standards

This document certifies the deployment strategy and rules for the `wiwa-tour-ig-feed` plugin.

## 1. Environment Strategy

| Environment     | Branch | Trigger        | Server Path                                                                       | User           |
| --------------- | ------ | -------------- | --------------------------------------------------------------------------------- | -------------- |
| **Development** | `dev`  | Push to `dev`  | `/home/wiwatour-dev/htdocs/dev.wiwatour.com/wp-content/plugins/wiwa-tour-ig-feed` | `wiwatour-dev` |
| **Production**  | `main` | Push to `main` | `/home/wiwatour-ssh/htdocs/wiwatour.com/wp-content/plugins/wiwa-tour-ig-feed`     | `wiwatour-ssh` |

## 2. Deployment Workflows

### Automatic Deployment to Development

- **File**: `.github/workflows/deploy-dev.yml`
- **Condition**: Anytime code is pushed to the `dev` branch.
- **Action**: Uses `rsync` to synchronize files to the development server.
- **Exclusions**: `.git`, `.github`, `tests`, `node_modules` (if any dev deps).

### Production Deployment

- **File**: `.github/workflows/deploy-prod.yml`
- **Condition**: Only when code is merged/pushed to the `main` branch.
- **Pre-requisite**: Code must be tested in Development first.
- **Verification**: Ensure `CHANGELOG.md` is updated and version is bumped before merging to `main`.

## 3. GitHub Secrets Configuration

Required secrets in GitHub Repo Settings:

- `SSH_PRIVATE_KEY`: Private key for `wiwatour-ssh` (used for prod) and `wiwatour-dev` (used for dev).
- `SERVER_IP`: Target server IP address.
- `SERVER_PORT`: SSH port (Default: 22).
- `KNOWN_HOSTS`: Server host key verification.

## 4. Rollback Strategy

In case of critical failure:

1. Revert the commit in Git: `git revert <commit-hash>`.
2. Push the reversion to the respective branch (`dev` or `main`).
3. The automated workflow will re-deploy the previous working state.
