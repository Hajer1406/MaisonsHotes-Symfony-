# Getting Started

<cite>
**Referenced Files in This Document**
- [composer.json](file://composer.json)
- [.env](file://.env)
- [config/packages/framework.yaml](file://config/packages/framework.yaml)
- [config/packages/doctrine.yaml](file://config/packages/doctrine.yaml)
- [config/packages/doctrine_migrations.yaml](file://config/packages/doctrine_migrations.yaml)
- [config/packages/asset_mapper.yaml](file://config/packages/asset_mapper.yaml)
- [config/packages/security.yaml](file://config/packages/security.yaml)
- [config/services.yaml](file://config/services.yaml)
- [config/routes.yaml](file://config/routes.yaml)
- [migrations/Version20260322195642.php](file://migrations/Version20260322195642.php)
- [public/index.php](file://public/index.php)
- [bin/console](file://bin/console)
- [src/Kernel.php](file://src/Kernel.php)
- [src/Entity/User.php](file://src/Entity/User.php)
- [src/Controller/LoginController.php](file://src/Controller/LoginController.php)
- [src/Controller/Admin/DashboardController.php](file://src/Controller/Admin/DashboardController.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Project Structure](#project-structure)
3. [Core Components](#core-components)
4. [Architecture Overview](#architecture-overview)
5. [Detailed Component Analysis](#detailed-component-analysis)
6. [Dependency Analysis](#dependency-analysis)
7. [Performance Considerations](#performance-considerations)
8. [Troubleshooting Guide](#troubleshooting-guide)
9. [Conclusion](#conclusion)
10. [Appendices](#appendices)

## Introduction
This guide helps you install, configure, and run the Maisons d'Hôtes application locally. It covers prerequisites, environment setup, database preparation with Doctrine migrations, building assets, starting the development server, creating your first user, and verifying the system works. The project is built with Symfony 7.4 and uses PostgreSQL via Doctrine DBAL.

## Project Structure
The project follows Symfony’s standard structure:
- Configuration under config/packages and config/routes
- PHP source code under src
- Web assets under assets and compiled public assets under public
- Migrations under migrations
- Tests under tests
- Composer-managed dependencies in composer.json

```mermaid
graph TB
A["Root"] --> B["config/"]
A --> C["src/"]
A --> D["templates/"]
A --> E["assets/"]
A --> F["public/"]
A --> G["migrations/"]
A --> H["tests/"]
A --> I["vendor/"]
A --> J[".env"]
A --> K["composer.json"]
```

[No sources needed since this diagram shows conceptual structure]

## Core Components
- PHP runtime and extensions: PHP >= 8.2 with ctype and iconv enabled
- Symfony components: Framework, Security, Doctrine ORM, Twig, Validator, AssetMapper, and others
- Database: PostgreSQL via Doctrine DBAL
- Development tools: Symfony Console, Doctrine Migrations, PHPUnit (dev)

Key indicators:
- PHP requirement and Symfony version constraints are defined in composer.json
- Doctrine DBAL and ORM are configured for PostgreSQL
- Asset Mapper is enabled for asset discovery and production builds
- Security is configured for form-login and role-based access control

**Section sources**
- [composer.json:6-48](file://composer.json#L6-L48)
- [config/packages/doctrine.yaml:1-55](file://config/packages/doctrine.yaml#L1-L55)
- [config/packages/asset_mapper.yaml:1-12](file://config/packages/asset_mapper.yaml#L1-L12)
- [config/packages/security.yaml:1-55](file://config/packages/security.yaml#L1-L55)

## Architecture Overview
High-level flow during a typical request:
- The browser requests a route
- public/index.php boots the Kernel
- Symfony loads configuration and routes
- Controllers render Twig templates or redirect
- Doctrine handles persistence to PostgreSQL
- Assets are served via AssetMapper

```mermaid
graph TB
Client["Browser"] --> Public["public/index.php"]
Public --> Kernel["src/Kernel.php"]
Kernel --> Routes["config/routes.yaml"]
Routes --> Controllers["Controllers (src/Controller/*)"]
Controllers --> Templates["Twig Templates (templates/*)"]
Controllers --> Doctrine["Doctrine DBAL/ORM"]
Doctrine --> PG["PostgreSQL"]
Controllers --> Assets["AssetMapper"]
```

**Diagram sources**
- [public/index.php:1-10](file://public/index.php#L1-L10)
- [src/Kernel.php:1-12](file://src/Kernel.php#L1-L12)
- [config/routes.yaml:1-15](file://config/routes.yaml#L1-L15)
- [config/packages/doctrine.yaml:1-55](file://config/packages/doctrine.yaml#L1-L55)
- [config/packages/asset_mapper.yaml:1-12](file://config/packages/asset_mapper.yaml#L1-L12)

**Section sources**
- [public/index.php:1-10](file://public/index.php#L1-L10)
- [src/Kernel.php:1-12](file://src/Kernel.php#L1-L12)
- [config/routes.yaml:1-15](file://config/routes.yaml#L1-L15)

## Detailed Component Analysis

### Environment Variables (.env)
- Required keys:
  - APP_SECRET: used by the framework for signing sessions and tokens
  - DATABASE_URL: connection string for PostgreSQL (e.g., postgresql://user:pass@host/dbname)
  - MAILJET_API_KEY and MAILJET_API_KEY_SECRET: for email delivery via Mailjet
- These are consumed by configuration files:
  - framework.secret reads APP_SECRET
  - doctrine.dbal.url reads DATABASE_URL
  - services bind Mailjet API keys into application services

Recommended steps:
- Copy the provided .env file to your local environment and adjust values accordingly
- Ensure DATABASE_URL targets a reachable PostgreSQL instance
- Verify APP_SECRET is set to a secure random value

**Section sources**
- [config/packages/framework.yaml:2-4](file://config/packages/framework.yaml#L2-L4)
- [config/packages/doctrine.yaml:2-4](file://config/packages/doctrine.yaml#L2-L4)
- [config/services.yaml:9-21](file://config/services.yaml#L9-L21)
- [.env](file://.env)

### Database Setup with Doctrine Migrations
- Migrations location is configured to load from the migrations directory
- A baseline migration exists to create/reset tables and adjust column types
- Typical steps:
  - Create the PostgreSQL database
  - Set DATABASE_URL in .env
  - Run migrations to apply schema changes
  - Optionally seed initial data after migrations succeed

```mermaid
flowchart TD
Start(["Start"]) --> CreateDB["Create PostgreSQL database"]
CreateDB --> SetEnv["Set DATABASE_URL in .env"]
SetEnv --> Migrate["Run migrations"]
Migrate --> Seed{"Seed initial data?"}
Seed --> |Yes| SeedStep["Run seeds after migrations"]
Seed --> |No| Done(["Done"])
SeedStep --> Done
```

**Diagram sources**
- [config/packages/doctrine_migrations.yaml:1-7](file://config/packages/doctrine_migrations.yaml#L1-L7)
- [migrations/Version20260322195642.php:1-38](file://migrations/Version20260322195642.php#L1-L38)

**Section sources**
- [config/packages/doctrine_migrations.yaml:1-7](file://config/packages/doctrine_migrations.yaml#L1-L7)
- [migrations/Version20260322195642.php:1-38](file://migrations/Version20260322195642.php#L1-L38)

### Asset Compilation
- AssetMapper is enabled and configured to scan assets/
- During development, assets are resolved and served by Symfony
- Production builds rely on AssetMapper and importmap

Typical steps:
- Install dependencies (Composer)
- Build assets via Symfony scripts
- Serve the application via the Symfony local server

**Section sources**
- [config/packages/asset_mapper.yaml:1-12](file://config/packages/asset_mapper.yaml#L1-L12)
- [composer.json:88-99](file://composer.json#L88-L99)

### Development Server Startup
- Use Symfony’s console to start the development server
- The console script validates vendor presence and runtime availability
- The Kernel is bootstrapped with APP_ENV and APP_DEBUG

```mermaid
sequenceDiagram
participant Dev as "Developer"
participant Console as "bin/console"
participant Kernel as "src/Kernel.php"
participant Web as "public/index.php"
Dev->>Console : Run console command
Console->>Kernel : Instantiate Kernel(APP_ENV, APP_DEBUG)
Kernel-->>Console : Ready
Console-->>Dev : Server ready (Symfony local server)
Dev->>Web : Access application in browser
```

**Diagram sources**
- [bin/console:1-22](file://bin/console#L1-L22)
- [src/Kernel.php:1-12](file://src/Kernel.php#L1-L12)
- [public/index.php:1-10](file://public/index.php#L1-L10)

**Section sources**
- [bin/console:1-22](file://bin/console#L1-L22)
- [src/Kernel.php:1-12](file://src/Kernel.php#L1-L12)
- [public/index.php:1-10](file://public/index.php#L1-L10)

### Initial User Account Creation
- Authentication uses form_login against the User entity
- The User entity defines username, roles, and password fields
- Access control requires ROLE_USER for general access and ROLE_ADMIN for the admin area
- Create your first admin user via the admin interface or programmatically

```mermaid
classDiagram
class User {
+int id
+string username
+string password
+string[] roles
+getUserIdentifier() string
+getRoles() string[]
+getPassword() string
}
```

**Diagram sources**
- [src/Entity/User.php:1-119](file://src/Entity/User.php#L1-L119)

**Section sources**
- [config/packages/security.yaml:14-46](file://config/packages/security.yaml#L14-L46)
- [src/Entity/User.php:1-119](file://src/Entity/User.php#L1-L119)

### Basic System Verification
- Visit the login page and ensure the form renders
- After logging in, verify the admin dashboard is accessible
- Confirm routes are registered and the logout path is available

**Section sources**
- [src/Controller/LoginController.php:1-22](file://src/Controller/LoginController.php#L1-L22)
- [src/Controller/Admin/DashboardController.php:1-88](file://src/Controller/Admin/DashboardController.php#L1-L88)
- [config/routes.yaml:10-15](file://config/routes.yaml#L10-L15)

## Dependency Analysis
The application depends on Symfony 7.4 components and Doctrine ORM for persistence. Composer manages these dependencies and runs auto-scripts for cache clearing, assets installation, and importmap setup.

```mermaid
graph LR
Composer["composer.json"] --> Symfony["Symfony 7.4 Components"]
Composer --> Doctrine["Doctrine ORM & DBAL"]
Composer --> Twig["Twig"]
Composer --> Security["Security Bundle"]
Symfony --> App["Application (src/)"]
Doctrine --> DB["PostgreSQL"]
Twig --> Views["Templates (templates/)"]
Security --> Auth["Authentication"]
```

**Diagram sources**
- [composer.json:6-48](file://composer.json#L6-L48)
- [config/packages/doctrine.yaml:1-55](file://config/packages/doctrine.yaml#L1-L55)

**Section sources**
- [composer.json:6-48](file://composer.json#L6-L48)

## Performance Considerations
- Keep APP_ENV=prod in production and clear caches appropriately
- Enable opcode caching (e.g., OPcache) in PHP for performance
- Use production asset build pipeline via AssetMapper
- Tune PostgreSQL parameters for your workload

[No sources needed since this section provides general guidance]

## Troubleshooting Guide
Common setup issues and resolutions:
- Dependencies missing
  - Symptom: Errors indicating missing vendor or runtime
  - Fix: Run Composer install to fetch dependencies
  - Reference: [bin/console:7-13](file://bin/console#L7-L13)
- Database connectivity
  - Symptom: Doctrine connection errors
  - Fix: Verify DATABASE_URL in .env points to a running PostgreSQL instance
  - References: [config/packages/doctrine.yaml:2-4](file://config/packages/doctrine.yaml#L2-L4), [.env](file://.env)
- Migrations not applied
  - Symptom: Missing tables or schema mismatch
  - Fix: Run migrations to update schema
  - References: [config/packages/doctrine_migrations.yaml:1-7](file://config/packages/doctrine_migrations.yaml#L1-L7), [migrations/Version20260322195642.php:1-38](file://migrations/Version20260322195642.php#L1-L38)
- Assets not loading
  - Symptom: Missing styles/scripts in development
  - Fix: Rebuild assets using Composer auto-scripts
  - References: [composer.json:88-99](file://composer.json#L88-L99), [config/packages/asset_mapper.yaml:1-12](file://config/packages/asset_mapper.yaml#L1-L12)
- Login failures
  - Symptom: Authentication errors
  - Fix: Ensure a valid user exists with appropriate roles; confirm form_login configuration
  - References: [config/packages/security.yaml:20-35](file://config/packages/security.yaml#L20-L35), [src/Entity/User.php:1-119](file://src/Entity/User.php#L1-L119)

**Section sources**
- [bin/console:7-13](file://bin/console#L7-L13)
- [config/packages/doctrine.yaml:2-4](file://config/packages/doctrine.yaml#L2-L4)
- [config/packages/doctrine_migrations.yaml:1-7](file://config/packages/doctrine_migrations.yaml#L1-L7)
- [migrations/Version20260322195642.php:1-38](file://migrations/Version20260322195642.php#L1-L38)
- [composer.json:88-99](file://composer.json#L88-L99)
- [config/packages/asset_mapper.yaml:1-12](file://config/packages/asset_mapper.yaml#L1-L12)
- [config/packages/security.yaml:20-35](file://config/packages/security.yaml#L20-L35)
- [src/Entity/User.php:1-119](file://src/Entity/User.php#L1-L119)

## Conclusion
You now have the essentials to install and run Maisons d'Hôtes locally. Ensure PHP 8.2+ and Composer are installed, set up PostgreSQL, configure .env, run migrations, compile assets, and start the Symfony server. Use the admin dashboard to manage content and users, and refer to the troubleshooting section for common issues.

[No sources needed since this section summarizes without analyzing specific files]

## Appendices

### Prerequisites Checklist
- PHP >= 8.2 with ctype and iconv extensions
- Composer
- PostgreSQL server and credentials
- Git (recommended)

**Section sources**
- [composer.json:6-10](file://composer.json#L6-L10)

### Step-by-Step Installation
1. Clone the repository and navigate to the project root
2. Install PHP dependencies:
   - Run Composer install
3. Prepare the environment:
   - Copy .env.example to .env and set APP_SECRET, DATABASE_URL, MAILJET_* keys
4. Create the PostgreSQL database and run migrations
5. Build assets using Composer auto-scripts
6. Start the Symfony development server via bin/console
7. Access the application in your browser

**Section sources**
- [bin/console:1-22](file://bin/console#L1-L22)
- [.env](file://.env)
- [config/packages/doctrine_migrations.yaml:1-7](file://config/packages/doctrine_migrations.yaml#L1-L7)
- [composer.json:88-99](file://composer.json#L88-L99)

### Environment Variable Reference
- APP_SECRET: application secret used by Symfony
- DATABASE_URL: PostgreSQL connection string
- MAILJET_API_KEY and MAILJET_API_KEY_SECRET: Mailjet credentials for sending emails

**Section sources**
- [config/packages/framework.yaml:2-4](file://config/packages/framework.yaml#L2-L4)
- [config/packages/doctrine.yaml:2-4](file://config/packages/doctrine.yaml#L2-L4)
- [config/services.yaml:9-21](file://config/services.yaml#L9-L21)