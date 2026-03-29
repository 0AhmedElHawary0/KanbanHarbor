# KanbanHarbor API Documentation (Postman)

This document lists all current API endpoints in the project and how to test them in Postman.

## 1) Base Setup in Postman

Create a Postman environment with these variables:

- `base_url` = `http://localhost/api`
- `token` = (empty at first)
- `tenant_id` = (set after creating a tenant)
- `user_id` = (set after creating/listing a member)
- `project_id` = (set after creating/listing a project)

Common headers:

- `Accept: application/json`
- `Content-Type: application/json`

For protected routes add:

- `Authorization: Bearer {{token}}`

For tenant scoped routes (recommended even when tenant id is in URL):

- `X-Tenant-Id: {{tenant_id}}`

---

## 2) Authentication APIs

### 2.1 Register User

- Method: `POST`
- URL: `{{base_url}}/auth/register`
- Auth: none
- Body (JSON):

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

Validation rules:

- `name`: required, string, min 3
- `email`: required, email, unique in users table
- `password`: required, min 6, max 20

Success response: `201 Created`

```json
{
  "message": "User registered successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### 2.2 Login

- Method: `POST`
- URL: `{{base_url}}/auth/login`
- Auth: none
- Body (JSON):

```json
{
  "email": "john@example.com",
  "password": "secret123",
  "device_name": "postman"
}
```

Validation rules:

- `email`: required, email
- `password`: required, min 6, max 20
- `device_name`: optional, string, max 100

Success response: `200 OK`

```json
{
  "message": "Login Successful",
  "data": {
    "token": "<sanctum-token>"
  }
}
```

After login: copy token into Postman env `token` variable.

---

### 2.3 Logout

- Method: `POST`
- URL: `{{base_url}}/auth/logout`
- Auth: Bearer token required
- Body (JSON, optional):

```json
{
  "all_devices": false
}
```

Validation rules:

- `all_devices`: optional boolean

Success response: `200 OK`

```json
{
  "message": "Logout successful",
  "data": {
    "logged_out": true
  }
}
```

---

## 3) Tenant Management APIs

### 3.1 Create Tenant

- Method: `POST`
- URL: `{{base_url}}/tenants`
- Auth: currently none (public in current route setup)
- Body (JSON):

```json
{
  "name": "Acme Workspace"
}
```

Validation rules:

- `name`: required, string, min 3, max 120

Success response: `201 Created`

```json
{
  "data": {
    "id": 1,
    "name": "Acme Workspace",
    "slug": "acme-workspace"
  }
}
```

Save returned tenant id into Postman env `tenant_id`.

---

### 3.2 Add Tenant Member

- Method: `POST`
- URL: `{{base_url}}/tenants/{{tenant_id}}/members`
- Auth: Bearer token required
- Middleware requirement: permission `member.invite`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`
- Body (JSON):

```json
{
  "name": "Jane Member",
  "email": "jane@example.com",
  "password": "secret123",
  "status": "active",
  "role": "member"
}
```

Validation rules:

- `name`: required, string, min 3, max 120
- `email`: required, email
- `password`: required, string, min 6, max 20
- `status`: required enum (`active`, `inactive`, `suspended`)
- `role`: required enum (`owner`, `admin`, `member`)

Success response: `201 Created`

```json
{
  "data": {
    "id": 2,
    "name": "Jane Member",
    "email": "jane@example.com",
    "role": "member"
  }
}
```

---

### 3.3 Update Tenant Member Role

- Method: `PATCH`
- URL: `{{base_url}}/tenants/{{tenant_id}}/members/{{user_id}}/role`
- Auth: Bearer token required
- Middleware requirement: permission `member.role.update`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`
- Body (JSON):

```json
{
  "role": "admin"
}
```

Validation rules:

- `role`: required enum (`owner`, `admin`, `member`)

Success response: `200 OK`

```json
{
  "data": {
    "id": 2,
    "role": "admin"
  }
}
```

---

### 3.4 List Tenant Members

- Method: `GET`
- URL: `{{base_url}}/tenants/{{tenant_id}}/members`
- Auth: Bearer token required
- Middleware requirement: permission `member.view`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`

Success response: `200 OK`

```json
[
  {
    "id": 1,
    "name": "Owner User",
    "email": "owner@example.com",
    "role": "owner"
  },
  {
    "id": 2,
    "name": "Jane Member",
    "email": "jane@example.com",
    "role": "admin"
  }
]
```

---

## 4) Project APIs

### 4.1 Create Project

- Method: `POST`
- URL: `{{base_url}}/tenants/{{tenant_id}}/projects`
- Auth: Bearer token required
- Middleware requirement: permission `project.create`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`
- Body (JSON):

```json
{
  "name": "Website Redesign",
  "description": "Q2 redesign initiative"
}
```

Validation rules:

- `name`: required, string, min 3, max 120
- `description`: optional string, max 2000

Success response: `201 Created`

```json
{
  "data": {
    "id": 1,
    "tenant_id": 1,
    "name": "Website Redesign",
    "description": "Q2 redesign initiative"
  }
}
```

Save returned project id into `project_id`.

---

### 4.2 List Projects

- Method: `GET`
- URL: `{{base_url}}/tenants/{{tenant_id}}/projects`
- Auth: Bearer token required
- Middleware requirement: permission `project.view`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`

Success response: `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "name": "Website Redesign",
      "description": "Q2 redesign initiative"
    }
  ]
}
```

---

### 4.3 Get Project by ID

- Method: `GET`
- URL: `{{base_url}}/tenants/{{tenant_id}}/projects/{{project_id}}`
- Auth: Bearer token required
- Middleware requirement: permission `project.view`
- Recommended headers:
  - `Authorization: Bearer {{token}}`
  - `X-Tenant-Id: {{tenant_id}}`

Success response: `200 OK`

```json
{
  "data": {
    "id": 1,
    "name": "Website Redesign",
    "description": "Q2 redesign initiative"
  }
}
```

---

## 5) Common Error Responses

### Validation error

Status: `422 Unprocessable Entity`

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Validation message"
    ]
  }
}
```

### Missing or invalid tenant context

Status: `400 Bad Request`

```json
{
  "message": "A valid tenant context is required."
}
```

### Authenticated user not in tenant

Status: `403 Forbidden`

```json
{
  "message": "You do not have access to this organization."
}
```

### Missing permission

Status: `403 Forbidden`

(Framework default forbidden response)

### Not authenticated

Status: `401 Unauthorized`

---

## 6) Suggested Postman Collection Order (Learning Flow)

1. Register
2. Login
3. Create Tenant
4. Add Tenant Member
5. List Tenant Members
6. Update Member Role
7. Create Project
8. List Projects
9. Get Project by ID
10. Logout

---

## 7) Quick Notes

- Tenant resolution works from either:
  - Header `X-Tenant-Id`
  - URL segment `/tenants/{tenantId}`
- For consistent testing in Postman, always send `X-Tenant-Id` on tenant-scoped routes.
- Protected routes require Sanctum bearer token from login.
