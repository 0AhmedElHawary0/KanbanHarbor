# User Module Target Tree (Clean Architecture + DDD + CQRS)

This document is the canonical tree for the User module in this repository.
Goal: one structure, no duplicates, clear ownership per layer.

## Canonical tree

```text
src/
  Domain/
    User/
      Entities/
      ValueObjects/
      Events/
      Repositories/
      Exceptions/
      Enums/
      Policies/
  Application/
    User/
      Commands/
      CommandHandlers/
      Queries/
      QueryHandlers/
      Data/
      Contracts/
      Services/
  Infrastructure/
    User/
      Persistence/
        Models/
        Repositories/
      Jobs/
      Notifications/
  Presentation/
    UserManagement/
      Controllers/
      Requests/
      Resources/
      routes/
```

## Layer responsibilities

- Domain: business rules and invariants only. No framework dependencies.
- Application: use cases (CQRS orchestration), contracts, and data transfer structures.
- Infrastructure: framework and IO implementations (Eloquent, queues, external services).
- Presentation: HTTP transport concerns (controllers, request validation, API/web resources).

## CQRS rules used in this project

- Commands mutate state and return minimal output (id, email, or void).
- CommandHandlers execute one use case and delegate persistence via domain contracts.
- Queries never mutate state.
- QueryHandlers read via repository contracts and return read-model/data objects.

## Existing class placement (current)

- Domain User entity: `Domain\\User\\Entities\\User`
- Domain repository contract: `Domain\\User\\Repositories\\UserRepositoryContract`
- Create command: `Application\\User\\Commands\\CreateUserCommand`
- Create command handler: `Application\\User\\CommandHandlers\\CreateUserCommandHandler`
- Get-by-email query: `Application\\User\\Queries\\GetUserByEmailQuery`
- Get-by-email query handler: `Application\\User\\QueryHandlers\\GetUserByEmailQueryHandler`
- Get-by-id query: `Application\\User\\Queries\\GetUserByIdQuery`
- Get-by-id query handler: `Application\\User\\QueryHandlers\\GetUserByIdQueryHandler`
- Data objects: `Application\\User\\Data\\UserData`, `Application\\User\\Data\\UsersListData`
- Infrastructure repository implementation: `Infrastructure\\User\\Persistence\\Repositories\\UserRepository`
- Presentation controller: `Presentation\\UserManagement\\Controllers\\UserController`
- Presentation request: `Presentation\\UserManagement\\Requests\\UserFormRequest`

## Naming policy to prevent duplication

- Use `Data` in Application (do not add `DTOs` in parallel).
- Use `UserManagement` in Presentation for this repository (do not add parallel `Http/User` tree).
- Use `QueryHandlers` folder for handlers and `Queries` folder for query messages.

## Next implementation checklist

1. Add domain value objects (for example `UserEmail`, `UserName`) in `Domain/User/ValueObjects`.
2. Keep command/query messages thin and move business validation into domain value objects/entities.
3. If read models diverge, introduce dedicated read data classes under `Application/User/Data`.
4. Add architecture tests to enforce no cross-layer leaks.
