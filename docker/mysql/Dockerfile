# docker/mysql/Dockerfile
# Fallback: builds MySQL image locally if internet pull fails.
# Uses mysql:8.0 as base — only needed if docker-compose can pull it once.
FROM mysql:8.0

# Copy the init script so it auto-runs on first container startup
COPY init.sql /docker-entrypoint-initdb.d/init.sql

# MySQL listens on port 3306 inside the container
EXPOSE 3306
