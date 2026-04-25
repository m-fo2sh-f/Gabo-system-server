# API Contract

## Client
- **Model:** `Client`
- **Endpoints:**
  - `GET /api/clients`
  - `GET /api/clients/{id}`
  - `POST /api/clients`
  - `PUT /api/clients/{id}`
  - `DELETE /api/clients/{id}`
- **Enums:**
  - `status`: `["active", "paused", "stopped"]`
  - `payment_cycle`: `["once", "monthly", "weakly"]`
  - `social_links.platform`: `["facebook", "snapchat", "instagram", "linkedin", "tiktok", "website"]`
- **Payload (`POST`, `PUT`):**
```json
{
  "name": "string (required)",
  "brand_name": "string (optional)",
  "address": "string (optional)",
  "phone": "string (required)",
  "contract_start_date": "date (optional)",
  "contract_value": "number (optional)",
  "payment_cycle": "enum (optional)",
  "status": "enum (optional)",
  "social_links": [
    {
      "platform": "enum",
      "url": "string"
    }
  ],
  "notes": "string (optional)"
}
```

## Employee
- **Model:** `Employee`
- **Endpoints:**
  - `POST /api/employees` (Inferred)
- **Enums:**
  - `employee_status`: `["active", "paused", "stopped"]`
- **Payload (`POST`):**
```json
{
  "is_freelance": "boolean",
  "name": "string (required)",
  "phone": "string (optional)",
  "job_title": "string (optional)",
  "commission_rate": "number (required if is_freelance=true)",
  "base_salary": "number (required if is_freelance=false)",
  "employee_status": "enum (optional)",
  "notes": "string (optional)"
}
```

## Task
- **Model:** `Task`
- **Endpoints:**
  - `POST /api/tasks` (Inferred)
- **Enums:**
  - `task_type`: `["marketing", "design", "development", "other"]`
  - `status`: `["pending", "completed", "cancelled"]`
- **Payload (`POST`):**
```json
{
  "client_id": "integer (required)",
  "employee_id": "integer (optional)",
  "task_type": "enum (optional)",
  "status": "enum (optional)",
  "price": "number (required)",
  "cost": "number (optional, default: 0)",
  "notes": "string (optional)"
}
```

## Transaction
- **Model:** `Transaction`
- **Endpoints:**
  - `GET /api/transactions`
  - `POST /api/transactions`
  - `DELETE /api/transactions/{id}`
- **Enums:**
  - `type`: `["income", "expense"]`
  - `category` (income): `["task_payment", "manual_collection", "general_income"]`
  - `category` (expense): `["salary", "ads", "rent", "other"]`
  - `payment_method`: `["cash", "instapay", "vodafone_cash", "other"]`
- **Payload (`POST`):**
```json
{
  "type": "enum (required)",
  "category": "enum (required)",
  "amount": "number (required)",
  "payment_method": "enum (optional, default: 'cash')",
  "transaction_date": "date (optional)",
  "notes": "string (optional)",
  "client_id": "integer (optional)",
  "employee_id": "integer (optional)",
  "collector_id": "integer (optional)",
  "task_id": "integer (optional)"
}
```
