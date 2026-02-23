# Logs Directory

This directory stores access logs for ARALINKS WiFi authentication system.

## Log Files

- **access.log** - Main application log file with all security and access events

## Log Format

Each log entry follows this format:

```
[YYYY-MM-DD HH:MM:SS] [LEVEL] [IP_ADDRESS] ACTION - DETAILS
```

### Log Levels

- **INFO** - General information
- **WARNING** - Warning conditions that should be reviewed
- **ERROR** - Error conditions
- **SECURITY** - Security-related events (attack attempts, violations)

### Example Entries

```
[2025-02-23 14:35:22] [INFO] [192.168.1.100] PAGE_VISIT - User Agent: Mozilla/5.0...
[2025-02-23 14:38:23] [SECURITY] [192.168.1.101] RATE_LIMIT_EXCEEDED - Voucher validation attempts exceeded
[2025-02-23 14:40:15] [ERROR] [192.168.1.102] DATABASE_ERROR - Connection failed
```

## Maintenance

- Logs are automatically created by the application
- Review logs regularly for suspicious activity
- Archive old logs monthly
- Delete logs older than 90 days to save space

## Troubleshooting

If logs are not being created:

1. Verify this directory exists
2. Check directory permissions (needs write access)
3. Verify `ENABLE_LOGGING = true` in `config.php`
4. Check PHP error log for permission issues
