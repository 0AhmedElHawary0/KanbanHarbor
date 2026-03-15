# Tenant Management Phase 1

This document describes the first implemented tenant-management slice in KanbanHarbor.

## Endpoints

- `POST /api/tenants`
  - Creates a new tenant.
- `POST /api/tenants/{tenantId}/members`
  - Adds a new member to the tenant.
- `PATCH /api/tenants/{tenantId}/members/{userId}/role`
  - Changes a member role inside the tenant.
- `GET /api/tenants/{tenantId}/members`
  - Lists members that belong to the tenant.

## CQRS flow

### Create tenant
- Request: `Presentation\TenantManagement\Requests\CreateTenantRequest`
- Command: `Application\Tenant\Commands\CreateTenantCommand`
- Handler: `Application\Tenant\CommandHandlers\CreateTenantCommandHandler`
- Query: `Application\Tenant\Queries\GetTenantByIdQuery`
- Query handler: `Application\Tenant\QueryHandlers\GetTenantByIdQueryHandler`

### Add tenant member
- Request: `Presentation\TenantManagement\Requests\AddTenantMemberRequest`
- Command: `Application\Tenant\Commands\AddTenantMemberCommand`
- Handler: `Application\Tenant\CommandHandlers\AddTenantMemberCommandHandler`
- Query: `Application\Tenant\Queries\GetTenantMemberByIdQuery`
- Query handler: `Application\Tenant\QueryHandlers\GetTenantMemberByIdQueryHandler`

### Assign member role
- Request: `Presentation\TenantManagement\Requests\UpdateTenantMemberRoleRequest`
- Command: `Application\Tenant\Commands\AssignTenantMemberRoleCommand`
- Handler: `Application\Tenant\CommandHandlers\AssignTenantMemberRoleCommandHandler`
- Query: `Application\Tenant\Queries\GetTenantMemberByIdQuery`
- Query handler: `Application\Tenant\QueryHandlers\GetTenantMemberByIdQueryHandler`

### List members
- Query: `Application\Tenant\Queries\ListTenantMembersQuery`
- Query handler: `Application\Tenant\QueryHandlers\ListTenantMembersQueryHandler`

## Domain model choices

- `Tenant` is its own aggregate root.
- Tenant membership is currently modeled as a tenant-scoped `User` with a `role` attribute.
- `UserRole` supports: `owner`, `admin`, `member`.
- This keeps the current architecture coherent while the system still assumes a user belongs to one tenant.

## Next likely step

Build the `Project` aggregate under a tenant boundary:
- create project
- list tenant projects
- archive project
- enforce role-based permissions (`owner` and `admin` first)
