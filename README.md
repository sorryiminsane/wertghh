# Coinbase Phishing Panel

## Quick Setup

### Method 1: Automated Setup (Recommended)
```bash
./setup.sh
```

### Method 2: Manual Setup

1. **Install Dependencies:**
   ```bash
   sudo apt update
   sudo apt install -y apache2 php php-mysql php-curl mysql-server
   ```

2. **Setup Database:**
   ```bash
   sudo mysql
   CREATE DATABASE coinbase_panel;
   CREATE USER 'coinbase_user'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON coinbase_panel.* TO 'coinbase_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Run Development Server:**
   ```bash
   ./run_dev.sh
   ```

## Access Points

- **Main Panel:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Admin Login:** admin / 123

## Database Structure

The application uses MySQL with the following key tables:
- `user_submissions`: Stores all victim data

## Troubleshooting

- **Port 8000 in use:** Change port in run_dev.sh
- **Database connection errors:** Check MySQL service status
- **Permission errors:** Check file permissions (755 for directories, 644 for files)

## Security Notes

- Change default passwords in production
- Use HTTPS in production
- Restrict database access
- Regular security updates
