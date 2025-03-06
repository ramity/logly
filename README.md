<p align="center">
  <img src="https://github.com/user-attachments/assets/32e99dc0-3609-43dc-a793-27b01a28ae6d">
</p>

> Logly is a development solution that forwards browser errors and automatically generates PRs to resolve them.

<h1>

```
<script src="https://this-doesn-t-exist-yet.com/logly.js">
  var repo = 'your-repo-name';
  var token = 'your-logly-token';
</script>
```

</h1>


# Getting Started

Login on our website, add your application, and use your application. Clyde the AI will handle the rest.

# Pricing

- Repo storage
- Automated PRs
- Issues

# Rolling out your own

#### Update env files
Copy paste .env.dist to .env files in docker dir

#### Build and start containers
`docker compose up -d`

#### Exec into backend container
`docker exec -it logly_backend bash`

#### Composer update repo
`composer update`

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

Goto https://github.com/settings/tokens (pick classic tokens) and provide 'repo', 'read:org', 'admin:public_key' permissions

Paste the result

#### Add logly to your application

```
import { Logly } from 'logly';

Logly.init({
  url: 'your-logly-backend-server-url/ingest'
  repo: 'your-repo-name',
  token: 'your-github-token',
});
```
(Mock example)

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

# Software

![javascript](https://github.com/user-attachments/assets/62724ff8-f477-4236-a2f6-9a9cacc038b9)
![php](https://github.com/user-attachments/assets/128e9324-e8d8-4c00-971a-c8c5df16a36b)
![docker-ollama](https://github.com/user-attachments/assets/554cee1c-31a7-465c-8b75-36b5f122720e)
![docker](https://github.com/user-attachments/assets/4941fdd5-7f62-4bee-9007-0872ddf37946)
![ollama](https://github.com/user-attachments/assets/63dc0ef6-c251-4a42-bc18-85efbc824392)
![qwen2 5](https://github.com/user-attachments/assets/de6c31bd-cdee-430f-b5f3-4740f17a2d21)
![symfony](https://github.com/user-attachments/assets/94b80444-7f50-40d7-b25b-5d63aa628299)
![github-cli](https://github.com/user-attachments/assets/7d4f50bf-b727-4af8-ad61-1477a0c585bc)
![composer](https://github.com/user-attachments/assets/3c62b790-a508-463a-a010-841ee64c83da)
![mariadb](https://github.com/user-attachments/assets/c5a7e43a-bb86-4c2b-ba02-61bbf414439f)

