# Mock AppSumo Portal

A local Laravel app that simulates AppSumo's marketplace, webhook sender, and OAuth server for end-to-end testing of a client's AppSumo integration.

## Setup

```bash
cd ~/Projects/appsumo
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
```

## Pages

| Route | Description |
|---|---|
| `GET /` | Dashboard — lists all licenses with actions |
| `GET /buy` | Buy page — 5 tier cards, enter buyer name, click to purchase |
| `POST /buy` | Creates license + sends `purchase` webhook to client |
| `POST /licenses/{id}/activate` | Sends `activate` webhook, updates status |
| `POST /licenses/{id}/deactivate` | Sends `deactivate` webhook, updates status |

## OAuth Endpoints

These mimic appsumo.com's OAuth flow. The client's AppSumo OAuth provider calls these:

| Route | Description |
|---|---|
| `GET /oauth/authorize?license_id=x` | Generates auth code, redirects to client callback with `?code=xxx` |
| `POST /openid/token/` | Exchanges auth code for access token (validates client_id/secret) |
| `GET /openid/license_key/?access_token=xxx` | Returns license key and status |
| `GET /v2/licenses/{licenseKey}` | Returns redemption and change-plan URLs (validates `X-AppSumo-Licensing-Key` header against `CLIENT_API_KEY`) |

## Webhook Format

All webhooks are HMAC SHA256 signed:

- **Header** `X-Appsumo-Signature`: `hash_hmac('sha256', timestamp . body, api_key)`
- **Header** `X-Appsumo-Timestamp`: unix timestamp

Payload:

```json
{
    "license_key": "uuid",
    "event": "purchase|activate|deactivate",
    "license_status": "inactive|active|deactivated",
    "event_timestamp": 1234567890,
    "tier": 1,
    "test": false
}
```

## End-to-End Test Flow

1. Visit `/buy` -> enter name -> click a tier
2. Mock creates license + sends `purchase` webhook -> client creates a license record (inactive)
3. Click "Activate" on dashboard -> mock sends `activate` webhook -> client marks license active
4. Click "Start OAuth" -> mock redirects to `client.test/appsumo/callback?code=xxx`
5. Client exchanges code for token -> calls mock's `/openid/token/`
6. Client gets license key -> calls mock's `/openid/license_key/`
7. Client stores license in session -> redirects to its registration flow
8. User completes registration -> account is linked to the AppSumo license
