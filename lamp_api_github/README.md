# Contact Manager — PHP API

## Project Structure

```
lamp_api/
├── schema.sql                  ← Run this first in MySQL
├── .htaccess                   ← Apache config
├── config/
│   ├── db.php                  ← DB credentials & connection
│   └── helpers.php             ← Shared utilities
└── api/
    ├── auth/
    │   ├── register.php        ← POST /api/auth/register
    │   ├── login.php           ← POST /api/auth/login
    │   └── logout.php          ← POST /api/auth/logout
    └── contacts/
        └── index.php           ← GET/POST/PUT/DELETE /api/contacts
```

---

## Setup on Digital Ocean

1. SSH into your droplet
2. Place files in `/var/www/html/` (or your Apache root)
3. Run the schema:
   ```bash
   mysql -u root -p < schema.sql
   ```
4. Update `config/db.php` with your actual DB credentials
5. Enable Apache `mod_rewrite`:
   ```bash
   sudo a2enmod rewrite headers
   sudo systemctl restart apache2
   ```
6. Make sure your Apache virtual host has `AllowOverride All`

---

## API Endpoints

### Auth

| Method | Endpoint              | Body / Params                                      |
|--------|-----------------------|----------------------------------------------------|
| POST   | /api/auth/register    | `{ "username", "email", "password" }`              |
| POST   | /api/auth/login       | `{ "email", "password" }`                          |
| POST   | /api/auth/logout      | —                                                  |

### Contacts (all require login)

| Method | Endpoint                  | Description                        |
|--------|---------------------------|------------------------------------|
| GET    | /api/contacts             | List all contacts                  |
| GET    | /api/contacts?q=john      | Search contacts (partial match)    |
| GET    | /api/contacts?id=1        | Get single contact                 |
| POST   | /api/contacts             | Create contact                     |
| PUT    | /api/contacts?id=1        | Update contact                     |
| DELETE | /api/contacts?id=1        | Delete contact                     |

### Example: Register
```json
POST /api/auth/register
{
  "username": "david",
  "email": "david@example.com",
  "password": "securepass123"
}
```
Response `201`:
```json
{ "message": "Account created successfully", "user_id": 1 }
```

### Example: Search
```
GET /api/contacts?q=john
```
Response `200`:
```json
[
  { "contact_id": 3, "first_name": "John", "last_name": "Doe", "email": "john@example.com", ... }
]
```

---

## Security Features
- Passwords hashed with `password_hash()` using **bcrypt** (cost 12) — auto-salted
- All DB queries use **PDO prepared statements** — no SQL injection possible
- Session fixation prevented with `session_regenerate_id(true)` on login
- HTTPS enforced via `.htaccess` redirect
- Directory listing disabled
- PHP version header hidden
