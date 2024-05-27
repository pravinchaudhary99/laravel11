#!/bin/bash

# Replace these values with your actual MySQL service details
MYSQL_HOST=192.168.1.102
MYSQL_PORT=3329
MYSQL_ROOT_PASSWORD=wot-epsilon-test-2024
MYSQL_DATABASE=epsilon

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
for i in {1..30}; do
  mysqladmin ping -h $MYSQL_HOST -P $MYSQL_PORT --silent && echo "MySQL is up and running" && break
  echo "Waiting..."
  sleep 2
done

# Test MySQL connection
echo "Testing MySQL connection..."
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u root -p$MYSQL_ROOT_PASSWORD -e "SHOW DATABASES;"

# Run Laravel database migrations
DB_CONNECTION=mysql
DB_HOST=$MYSQL_HOST
DB_PORT=$MYSQL_PORT
DB_DATABASE=$MYSQL_DATABASE
DB_USERNAME=root
DB_PASSWORD=$MYSQL_ROOT_PASSWORD
php artisan migrate --force

# Run Pest tests
DB_CONNECTION=mysql
DB_HOST=$MYSQL_HOST
DB_PORT=$MYSQL_PORT
DB_DATABASE=$MYSQL_DATABASE
DB_USERNAME=root
DB_PASSWORD=$MYSQL_ROOT_PASSWORD
./vendor/bin/pest
