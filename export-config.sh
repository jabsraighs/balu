#!/bin/bash

# Ensure the file is empty.
> config_db/.env

# Map the database information from the PLATFORM_RELATIONSHIPS variable into the YAML file.
# Use this process to use whatever variable names your app needs.

printf "DATABASE_URL= %s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".DATABASE_URL") >> .env
printf "MAILER_DSN= %s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".MAILER_DSN") >> .env