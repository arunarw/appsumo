# Mock AppSumo Portal

A local Laravel app that simulates AppSumo's marketplace, webhook sender, and OAuth server for end-to-end testing of a client's AppSumo integration.

## Setup

```bash
cd appsumo
composer install
cp .env.example .env   # then configure (see below)
php artisan key:generate
php artisan migrate
```

### Database

Uses MySQL. Create the database first:

```sql
CREATE DATABASE appsumo;
```

### Serving

Point `appsumo.test` at the project via Valet/Herd, or:

```bash
php artisan serve --port=8001
```

## .env Configuration

```env
# Where to send webhooks (client webhook endpoint)
CLIENT_WEBHOOK_URL=http://client.test/appsumo/webhook
CLIENT_API_KEY=shared-secret-with-client

# Where to redirect after OAuth authorize (client callback)
CLIENT_OAUTH_REDIRECT_URL=http://client.test/appsumo/callback

# OAuth credentials (must match client .env)
APPSUMO_CLIENT_ID=mock-client-id
APPSUMO_CLIENT_SECRET=mock-client-secret
```

### Client .env (point at mock)

Add these to your client `.env` so it talks to the mock instead of appsumo.com:

```env
APPSUMO_CLIENT_ID=mock-client-id
APPSUMO_CLIENT_SECRET=mock-client-secret
APPSUMO_API_KEY=shared-secret-with-client
APPSUMO_OAUTH_REDIRECT_URL=http://client.test/appsumo/callback
APPSUMO_OAUTH_TOKEN_URL=http://appsumo.test/openid/token/
APPSUMO_OAUTH_LICENSE_KEY_URL=http://appsumo.test/openid/license_key/
APPSUMO_API_BASE_URL=http://appsumo.test
```
