# Remove emails from IMAP
Remove emails from email boxes older then the provided time.

Example configuration:
```json
{
  "accounts": [
    {
      "host": "domain.com",
      "username": "user@domain.com",
      "password": "Welcom123",
      "before": "2 weeks",
      "port": 993,
      "ssl": true,
      "ignore-certificate": false,
      "boxes": [
        "INBOX",
        "Trash"
      ]
    }
  ]
}
```

## Install
```shell
git clone git@github.com:feyst/purge-email.git
cd purge-email
composer install
```

## Usage
```shell
php src/application.php encrypt ~/source.json ./env.json.enc mysecret
php src/application.php encrypt --opslimit=1 --memlimit=64000000 ~/source.json ./env.json.enc mysecret
php src/application.php remove-old-mail mysecret
```

## Todo
- Implement DI