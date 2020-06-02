# Github Actions

This repository has three Actions workflows:

| Name | Triggers | Branch Restrictions | Description |
|-|-|-|-|
| **Test** | [`push`, `pull_request`] | | This workflow installs and compiles dependencies and assets before running the test and coverage suite against core. |
| **Release** | [`repository_dispatch`: types[`release-trigger`]] | `master` | This workflow runs semantic release against the master branch to tag and publish a new release. |
| **Deploy** | [`release`] | `master` | This workflow initiates a GitHub deployment, installs and compiles dependenices, generates configuration files from secrets and uploads the application to VATSIM UK's production systems. The workflow is responsible for all server-side file management including version housekeeping. |

## Important Notes

Worklows **Release** and **Deploy** are configured to be triggered by upstream workflows. The built-in `GITHUB_TOKEN` token available to actions is not permitted to trigger downstream workflows for security and loop prevention purposes. Therefore, both of these jobs need to be triggered using a Personal Access Token with `repo` permissions generated by a user with the ability to write to the repository. This PAT needs to be stored as a secret with the id `PAT`.

## Triggering a Deployment Manually

This may be necessary from time to time should a given deployment fail or if you need to update the value of a secret and deploy just configuration changes.

There is **no way** to trigger a workflow manually within the Github User Interface. However, the workflow will trigger on a repository dispatch event of the type `manual-trigger`. Therefore, we can use an authenticated API request to trigger a deployment.

Below is an example of the curl request you need to make to trigger a deployment.

```bash
curl -X POST \
  https://api.github.com/repos/vatsim-uk/core/dispatches \
  -H 'Authorization: token REPLACE_ME_WITH_YOUR_PERSONAL_ACCESS_TOKEN' \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
  "event_type": "manual-trigger",
  "client_payload": {
    "ref": "REPLACE_ME_WITH_THE_TAG_TO_DEPLOY"
  }
}'
```

## .env Configuration

The `deploy.yml` workflow has been designed to copy the .env.example file to .env (**.env.example MUST CONTINUE TO REFLECT THE PARAMETERS USED IN PRODUCTION**)

From there, the workflow replaces variables found in the .env file if there is a matching key in the runner's system environment that is populated from the `<step>.<env>` section of the workflow. This env can be populated using both plaintext and secrets as required.

Below is an example of the step that generates the env:

```yaml
- name: Populate .env
  env:
    APP_KEY: ${{ secrets.APP_KEY }}
    DB_MYSQL_USER: db_user
  run: |
  ...
```

### To add a new value to your .env file

1. If the value should be kept secret, **create a secret** for it using the same key as found in the .env file
2. Update the workflow step to include that new secret within the env for the step as shown above. Make sure that the key matches the `.env` file.

## Secrets

Workflows running on master require a number of secrets to run successfuly. **If a secret is not present, it will be presented to the job as `null` or `0` and will lead to unexpected results**

### Job Secrets

Below is a table of all secrets used within the workflows directly. See [.env Configuration](#.env-Configuration) for secrets used when generating the .env configuration file.

| Name | Used In | Description |
|-|-|-|
| `NOVA_USERNAME` | [`test.yml`, `deploy.yml`] | Nova is a licensed download; username for composer auth |
| `NOVA_PASSWORD` | [`test.yml`, `deploy.yml`] | Nova is a licensed download; password for composer auth |
| `PAT` | [`test.yml`, `release.yml`] | Personal Access Token with `repo` permissions. More Info: see above under [Actions -> Important Notes](#important-notes) |
| `APPLICATION_ROOT` | `deploy.yml` | Specifies the target deployment application root for use when uploading and for creating / managing additional app directories / links. |
| `SSH_HOST` | `deploy.yml` | SSH target host for deployment |
| `SSH_PORT` | `deploy.yml` | SSH port for deployment |
| `SSH_KEY` | `deploy.yml` | SSH Private Key for Authentication against remote server |
| `SSH_USER` | `deploy.yml` | SSH username for deployment |