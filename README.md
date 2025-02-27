# Update env files
Copy paste .env.dist to .env files in docker dir

# Build and start containers
docker compose up -d

Within logly_backend:

# Run migrations
bin/console doctrine:migrate

# Load fixtures
bin/console doctrine:fixtures:load

# Run server
symfony server:start --listen-ip=0.0.0.0
