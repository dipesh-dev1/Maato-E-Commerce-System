# Installing Maato on another computer

These steps take the project from the zip file to a working shop on a new Windows machine with XAMPP. Laragon notes are at the end. It takes about 10 minutes.

## What you need

- **XAMPP** with PHP 8.0 or newer (any current XAMPP release works): https://www.apachefriends.org
- The project zip: `suman-ecom.zip`

## Step 1: Install and start XAMPP

1. Install XAMPP (the default folder is `C:\xampp`).
2. Open the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**. Both should turn green.

## Step 2: Put the project in place

1. Extract `suman-ecom.zip` into `C:\xampp\htdocs\`.
2. Check the result: the file `C:\xampp\htdocs\suman-ecom\README.md` must exist.
   - Watch for a doubled folder such as `htdocs\suman-ecom\suman-ecom\`. If you see one, move the inner folder up a level.

> The folder name matters. If you use a different name (for example `shop`), change `APP_URL` in Step 3 to match (`http://localhost/shop`).

## Step 3: Create the settings file (.env)

1. In `C:\xampp\htdocs\suman-ecom`, copy `.env.example` and name the copy `.env`.
   - Command Prompt: `cd C:\xampp\htdocs\suman-ecom` then `copy .env.example .env`
2. Open `.env` in Notepad and check these lines:
   ```
   APP_URL=http://localhost/suman-ecom
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=suman-ecom
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   - If Apache runs on another port (for example 8080), use `APP_URL=http://localhost:8080/suman-ecom`.
   - If your MySQL `root` user has a password, put it after `DB_PASSWORD=`.
   - Leave no trailing slash on `APP_URL`.

## Step 4: Create the database

Choose **one** of these options.

### Option A: Command Prompt (fastest)

```
cd C:\xampp\htdocs\suman-ecom
C:\xampp\mysql\bin\mysql -u root < database\schema.sql
C:\xampp\mysql\bin\mysql -u root --default-character-set=utf8mb4 < database\seed.sql
```
If root has a password, add `-p` after `-u root` and type the password when asked.

### Option B: phpMyAdmin

1. Open http://localhost/phpmyadmin
2. Click the **Import** tab (with no database selected), choose `database/schema.sql`, and click **Import**. This creates the database `suman-ecom`.
3. Click the new `suman-ecom` database on the left, open **Import** again, choose `database/seed.sql`, and click **Import**.

Always import `schema.sql` first, then `seed.sql`. Importing both again at any time resets the shop to the demo data.

## Step 5: Turn on image resizing (recommended)

1. Open `C:\xampp\php\php.ini` in Notepad.
2. Find the line `;extension=gd` and remove the semicolon so it reads `extension=gd`.
3. Save the file, then **Stop** and **Start** Apache in the XAMPP Control Panel.

The shop works without this step, but uploaded images won't get resized thumbnails, and **Store settings** shows a warning until you turn it on.

## Step 6: Open the shop

- Storefront: http://localhost/suman-ecom
- Back office: http://localhost/suman-ecom/admin

| Role | Email | Password |
|---|---|---|
| Admin | admin@maato.test | Maato@Admin2026 |
| Staff | staff@maato.test | Maato@Staff2026 |
| Customer | aarati@example.com | Customer@2026 |
| Customer | bikash@example.com | Customer@2026 |
| Customer | sneha@example.com | Customer@2026 |

Password-reset links aren't emailed; they are written to `storage\logs\mail.log`.

## Moving your current data instead of the demo data

The zip contains the demo seed. If you have added real products or orders and want to bring them along:

1. On the **old** computer, export the database:
   ```
   C:\xampp\mysql\bin\mysqldump -u root --default-character-set=utf8mb4 --databases "suman-ecom" > suman-ecom-backup.sql
   ```
2. Copy `suman-ecom-backup.sql` **and** the whole `storage\uploads` folder to the new computer (uploaded photos live there, not in the database).
3. On the **new** computer, skip Step 4 and run:
   ```
   C:\xampp\mysql\bin\mysql -u root --default-character-set=utf8mb4 < suman-ecom-backup.sql
   ```
4. Replace `C:\xampp\htdocs\suman-ecom\storage\uploads` with the copied folder.

## Laragon instead of XAMPP

1. Extract the zip into `C:\laragon\www\` so you have `C:\laragon\www\suman-ecom`.
2. Click **Start All**. Laragon creates `http://suman-ecom.test` automatically.
3. In `.env`, set `APP_URL=http://suman-ecom.test`.
4. Import the database as in Step 4, using Laragon's terminal (`mysql -u root < database\schema.sql`, then `seed.sql`) or HeidiSQL.

## Troubleshooting

| Problem | Fix |
|---|---|
| Every page except the home page shows Apache's own "Not Found" | `mod_rewrite` or `.htaccess` isn't active. In `C:\xampp\apache\conf\httpd.conf`, make sure `LoadModule rewrite_module modules/mod_rewrite.so` has no `#` in front of it, and that the `<Directory "C:/xampp/htdocs">` block says `AllowOverride All`. Restart Apache. |
| Pages load without any styling, or links go to the wrong place | `APP_URL` in `.env` doesn't match the address in your browser (folder name, port, or a trailing slash). |
| "Something went wrong on our side" on every page | The database isn't reachable. Check MySQL is running, the database was imported, and the `DB_*` values in `.env`. Details are in `storage\logs\app.log`. |
| "Your session timed out" when submitting forms | Cookies are blocked for localhost, or `APP_URL` doesn't match the address you are using. Reload the page and try again. |
| Port 80 or 3306 is already in use | Change the port in XAMPP (Config → Apache/MySQL), then update `APP_URL` or `DB_PORT` in `.env` to match. |
| Uploaded images aren't resized | Turn on GD (Step 5). |
| Characters such as “Rs.”, dashes or Nepali text look garbled after import | Re-import with `--default-character-set=utf8mb4`, as shown above. |

## Before putting it online

- Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
- Use HTTPS (the session cookie becomes secure automatically).
- Use a dedicated MySQL user with a strong password instead of `root`.
- Change the demo passwords, or create your own admin and delete the demo accounts.
