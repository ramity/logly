# Getting Started

#### Update env files
Copy paste .env.dist to .env files in docker dir

#### Build and start containers
`docker compose up -d`

#### Exec into backend container
`docker exec -it logly_backend bash`

#### Run migrations
`bin/console doctrine:migrate`

#### Load fixtures
`bin/console doctrine:fixtures:load`

#### PAT for Clyde - Enable the docker container to act as Clyde

(interactive)
```
gh auth login
github.com
SSH
id_ed25519.pub
GitHub CLI
```

Goto https://github.com/settings/tokens and provide 'repo', 'read:org', 'admin:public_key' permissions

Paste the result

#### Enable cylde to make suggestions

- Add feed.js source to your repo
- Add Clyde as a contributor

#### How it works

Adding the feed.js script allows for it to intercept all errors that are generated on the client.
These logs are forwarded to a central server which also has a copy of the repo's source code on file.
Using the error and filename, the source code of the offending file is located and obtained.
Now with the required pieces:
- LLM prompt is created
- LLM response is generated
- Code is extracted and updated
- Git operations are performed
- GitHub operations are performed
