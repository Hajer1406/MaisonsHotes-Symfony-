# Property Management System

<cite>
**Referenced Files in This Document**
- [Maison.php](file://src/Entity/Maison.php)
- [MaisonController.php](file://src/Controller/MaisonController.php)
- [MaisonType.php](file://src/Form/MaisonType.php)
- [MaisonRepository.php](file://src/Repository/MaisonRepository.php)
- [MaisonSearch.php](file://src/Entity/MaisonSearch.php)
- [MaisonSearchType.php](file://src/Form/MaisonSearchType.php)
- [MaisonCrudController.php](file://src/Controller/Admin/MaisonCrudController.php)
- [index.html.twig](file://templates/maison/index.html.twig)
- [show.html.twig](file://templates/maison/show.html.twig)
- [base.html.twig](file://templates/base.html.twig)
- [app.js](file://assets/app.js)
- [composer.json](file://composer.json)
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
This document provides comprehensive documentation for the property management system focused on the Maison (house) entity and related components. It covers entity structure, business logic, CRUD operations, form handling, data persistence, property search and filtering, availability checks, image upload and management, property owner associations, validation rules, frontend templates with Bootstrap integration, responsive design, property status management, pricing calculations, seasonal availability, and administrative property management via EasyAdmin.

## Project Structure
The system follows a standard Symfony application layout with clear separation of concerns:
- Entities define domain objects and relationships
- Controllers handle HTTP requests and orchestrate responses
- Forms encapsulate presentation logic and validation
- Repositories provide data access and queries
- Templates render HTML with Bootstrap styling
- Assets integrate JavaScript and CSS via AssetMapper

```mermaid
graph TB
subgraph "Backend"
E["Entities<br/>Maison, Proprietaire, MaisonSearch"]
C["Controllers<br/>MaisonController, Admin\\MaisonCrudController"]
F["Forms<br/>MaisonType, MaisonSearchType"]
R["Repositories<br/>MaisonRepository"]
S["Services<br/>EntityManagerInterface"]
end
subgraph "Frontend"
T["Templates<br/>maison/*.html.twig, base.html.twig"]
A["Assets<br/>app.js, Stimulus/Bootstrap integration"]
end
E --> R
C --> E
C --> F
C --> R
C --> S
F --> E
T --> C
A --> T
```

**Diagram sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)
- [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)
- [MaisonCrudController.php:1-51](file://src/Controller/Admin/MaisonCrudController.php#L1-L51)
- [index.html.twig:1-42](file://templates/maison/index.html.twig#L1-L42)
- [show.html.twig:1-43](file://templates/maison/show.html.twig#L1-L43)
- [base.html.twig:1-184](file://templates/base.html.twig#L1-L184)
- [app.js:1-11](file://assets/app.js#L1-L11)

**Section sources**
- [composer.json:1-111](file://composer.json#L1-L111)

## Core Components
This section documents the Maison entity, associated forms, repositories, and controllers that implement CRUD operations and search/filtering.

- Maison entity
  - Attributes: title, description, price, city, image, and a ManyToOne relationship to Proprietaire
  - Methods include getters/setters for all attributes and a string representation returning the title
  - References: [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)

- MaisonController
  - Routes: index, new, show, edit, delete
  - Uses MaisonType for form handling and EntityManagerInterface for persistence
  - Implements CSRF protection for deletion
  - References: [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)

- MaisonType form
  - Fields: title, description, price, city, image, and a dropdown for Proprietaire
  - Binds to Maison entity
  - References: [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)

- MaisonRepository
  - Provides countAll, findByCity (top cities by count), and findLatest methods
  - References: [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)

- Search model and form
  - MaisonSearch holds a Maison filter object
  - MaisonSearchType provides a dropdown to select a Maison for filtering
  - References: [MaisonSearch.php:1-19](file://src/Entity/MaisonSearch.php#L1-L19), [MaisonSearchType.php:1-33](file://src/Form/MaisonSearchType.php#L1-L33)

**Section sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)
- [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)
- [MaisonSearch.php:1-19](file://src/Entity/MaisonSearch.php#L1-L19)
- [MaisonSearchType.php:1-33](file://src/Form/MaisonSearchType.php#L1-L33)

## Architecture Overview
The system employs a layered architecture:
- Presentation layer: Twig templates and Bootstrap styling
- Application layer: Controllers and forms
- Domain layer: Entities and value objects
- Data access layer: Repositories and Doctrine ORM

```mermaid
graph TB
Client["Browser"]
Router["Symfony Routing"]
Controller["MaisonController"]
Form["MaisonType"]
Repo["MaisonRepository"]
Entity["Maison Entity"]
ORM["Doctrine ORM"]
DB["Database"]
Client --> Router --> Controller
Controller --> Form
Controller --> Repo
Repo --> ORM --> DB
Controller --> Entity
Form --> Entity
```

**Diagram sources**
- [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)

## Detailed Component Analysis

### Maison Entity
The Maison entity defines the core property record with essential attributes and relationships.

```mermaid
classDiagram
class Maison {
+int id
+string title
+string description
+float price
+string city
+string image
+Proprietaire proprietaires
+__toString() string
}
class Proprietaire {
+int id
+string name
+string surname
+string phone
+__toString() string
}
Maison --> Proprietaire : "ManyToOne"
```

**Diagram sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)
- [Proprietaire.php:1-70](file://src/Entity/Proprietaire.php#L1-L70)

**Section sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)

### CRUD Operations via MaisonController
The controller implements standard CRUD actions with form handling and persistence.

```mermaid
sequenceDiagram
participant U as "User"
participant C as "MaisonController"
participant F as "MaisonType"
participant E as "EntityManagerInterface"
participant V as "View (Twig)"
U->>C : "GET /maison"
C->>V : "Render index"
U->>C : "GET /maison/new"
C->>F : "Create form"
C-->>V : "Render new form"
U->>C : "POST /maison/new"
C->>F : "handleRequest()"
F-->>C : "isValid()"
C->>E : "persist(entity)"
C->>E : "flush()"
C-->>U : "Redirect to index"
U->>C : "GET /maison/{id}"
C-->>V : "Render show"
U->>C : "GET/POST /maison/{id}/edit"
C->>F : "Create form"
C->>F : "handleRequest()"
F-->>C : "isValid()"
C->>E : "flush()"
C-->>U : "Redirect to index"
U->>C : "POST /maison/{id} (CSRF token)"
C->>E : "remove(entity)"
C->>E : "flush()"
C-->>U : "Redirect to index"
```

**Diagram sources**
- [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)

**Section sources**
- [MaisonController.php:1-82](file://src/Controller/MaisonController.php#L1-L82)

### Form Handling with MaisonType
The form integrates with the Maison entity and includes a dropdown for selecting a property owner.

```mermaid
flowchart TD
Start(["Build Form"]) --> AddTitle["Add field 'title'"]
AddTitle --> AddDescription["Add field 'description'"]
AddDescription --> AddPrice["Add field 'price'"]
AddPrice --> AddCity["Add field 'city'"]
AddCity --> AddImage["Add field 'image'"]
AddImage --> AddOwner["Add field 'proprietaires'<br/>EntityType: Proprietaire"]
AddOwner --> Defaults["Configure Options<br/>data_class=Maison"]
Defaults --> End(["Form Ready"])
```

**Diagram sources**
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)

**Section sources**
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)

### Data Persistence via MaisonRepository
The repository provides convenience methods for querying properties.

```mermaid
flowchart TD
Start(["Repository Call"]) --> CountAll["countAll()"]
Start --> FindByCity["findByCity()"]
Start --> FindLatest["findLatest(limit)"]
CountAll --> QueryBuilder1["CreateQueryBuilder('m')"]
QueryBuilder1 --> SelectCount["SELECT COUNT(m.id)"]
SelectCount --> GetScalar["getSingleScalarResult()"]
FindByCity --> QueryBuilder2["CreateQueryBuilder('m')"]
QueryBuilder2 --> SelectGroup["SELECT m.city, COUNT(m.id)"]
SelectGroup --> GroupBy["GROUP BY m.city"]
GroupBy --> OrderBy["ORDER BY count DESC"]
OrderBy --> SetMax["setMaxResults(5)"]
SetMax --> GetResult1["getResult()"]
FindLatest --> QueryBuilder3["CreateQueryBuilder('m')"]
QueryBuilder3 --> OrderById["ORDER BY m.id DESC"]
OrderById --> SetLimit["setMaxResults(limit)"]
SetLimit --> GetResult2["getResult()"]
```

**Diagram sources**
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)

**Section sources**
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)

### Property Search and Filtering
The system supports filtering by a specific Maison using a dedicated search form and model.

```mermaid
sequenceDiagram
participant U as "User"
participant C as "MaisonController"
participant F as "MaisonSearchType"
participant R as "MaisonRepository"
participant V as "View (Twig)"
U->>C : "GET /maison (with query params)"
C->>F : "Create form (method=GET)"
C->>F : "handleRequest()"
F-->>C : "Bind data (MaisonSearch)"
C->>R : "Custom query (based on selected Maison)"
R-->>C : "Filtered results"
C-->>V : "Render index with filters"
```

**Diagram sources**
- [MaisonSearchType.php:1-33](file://src/Form/MaisonSearchType.php#L1-L33)
- [MaisonSearch.php:1-19](file://src/Entity/MaisonSearch.php#L1-L19)
- [MaisonRepository.php:1-47](file://src/Repository/MaisonRepository.php#L1-L47)

**Section sources**
- [MaisonSearchType.php:1-33](file://src/Form/MaisonSearchType.php#L1-L33)
- [MaisonSearch.php:1-19](file://src/Entity/MaisonSearch.php#L1-L19)

### Availability Checking
Availability logic is not implemented in the current codebase. To support availability checking:
- Add date range parameters to search forms
- Extend MaisonSearch with check-in/check-out dates
- Implement repository methods to exclude booked dates
- Integrate with Reservation entity if present

[No sources needed since this section provides general guidance]

### Property Image Upload and Management
Image handling is configured in the EasyAdmin controller for the Maison entity.

```mermaid
flowchart TD
Start(["Admin Upload"]) --> EA["EasyAdmin Field: ImageField"]
EA --> BasePath["Base Path: uploads/images"]
EA --> UploadDir["Upload Dir: public/uploads/images"]
EA --> Pattern["Randomized Filename Pattern"]
EA --> Required["Optional (required=false)"]
Required --> Persist["Persist filename to Maison.image"]
Persist --> End(["Display in Admin List"])
```

**Diagram sources**
- [MaisonCrudController.php:1-51](file://src/Controller/Admin/MaisonCrudController.php#L1-L51)

**Section sources**
- [MaisonCrudController.php:1-51](file://src/Controller/Admin/MaisonCrudController.php#L1-L51)

### Property Owner Associations
The Maison entity maintains a ManyToOne relationship with Proprietaire, allowing association of properties to owners.

```mermaid
classDiagram
class Maison {
+int id
+string title
+string description
+float price
+string city
+string image
+Proprietaire proprietaires
}
class Proprietaire {
+int id
+string name
+string surname
+string phone
}
Maison --> Proprietaire : "ManyToOne"
```

**Diagram sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)
- [Proprietaire.php:1-70](file://src/Entity/Proprietaire.php#L1-L70)

**Section sources**
- [Maison.php:1-118](file://src/Entity/Maison.php#L1-L118)
- [Proprietaire.php:1-70](file://src/Entity/Proprietaire.php#L1-L70)

### Validation Rules
Validation is not explicitly defined in the provided files. Recommended validation rules for Maison:
- title: Not blank, max length
- description: Optional but recommended
- price: Numeric, positive
- city: Not blank, max length
- image: Optional path or file name
- proprietaires: Not blank (required association)

[No sources needed since this section provides general guidance]

### Frontend Templates and Bootstrap Integration
The frontend uses Bootstrap 5.1.3 with custom styling and responsive design.

```mermaid
graph TB
Base["base.html.twig"]
Index["maison/index.html.twig"]
Show["maison/show.html.twig"]
Base --> |"Extends base"| Index
Base --> |"Extends base"| Show
Base --> Bootstrap["Bootstrap CSS/JS CDN"]
Base --> FontAwesome["Font Awesome Icons"]
Base --> CustomCSS["Custom CSS Variables & Styles"]
```

**Diagram sources**
- [base.html.twig:1-184](file://templates/base.html.twig#L1-L184)
- [index.html.twig:1-42](file://templates/maison/index.html.twig#L1-L42)
- [show.html.twig:1-43](file://templates/maison/show.html.twig#L1-L43)

**Section sources**
- [base.html.twig:1-184](file://templates/base.html.twig#L1-L184)
- [index.html.twig:1-42](file://templates/maison/index.html.twig#L1-L42)
- [show.html.twig:1-43](file://templates/maison/show.html.twig#L1-L43)

### Responsive Design
The base template includes viewport meta tag and responsive navigation with collapsible menus. Cards and buttons use Bootstrap utility classes for responsive behavior.

**Section sources**
- [base.html.twig:1-184](file://templates/base.html.twig#L1-L184)

### Property Status Management
Status management is not implemented in the current codebase. To implement:
- Add a status field to Maison (enum or string)
- Define statuses (available, booked, maintenance)
- Update EasyAdmin fields to include status selection
- Filter listings by status in templates

[No sources needed since this section provides general guidance]

### Pricing Calculations and Seasonal Availability
Pricing logic and seasonal rates are not implemented. To implement:
- Add rate tiers or seasonal pricing fields
- Implement calculation logic in service or repository
- Extend search to consider date ranges and pricing filters
- Display calculated totals in property views

[No sources needed since this section provides general guidance]

### Examples of Property Listing Display and Detailed Views
- Listing view: [index.html.twig:1-42](file://templates/maison/index.html.twig#L1-L42)
- Detailed view: [show.html.twig:1-43](file://templates/maison/show.html.twig#L1-L43)
- Navigation and branding: [base.html.twig:94-161](file://templates/base.html.twig#L94-L161)

**Section sources**
- [index.html.twig:1-42](file://templates/maison/index.html.twig#L1-L42)
- [show.html.twig:1-43](file://templates/maison/show.html.twig#L1-L43)
- [base.html.twig:94-161](file://templates/base.html.twig#L94-L161)

### Administrative Property Management via EasyAdmin
EasyAdmin provides a streamlined interface for managing properties with image upload capabilities.

```mermaid
sequenceDiagram
participant Admin as "Admin User"
participant EA as "EasyAdmin MaisonCrudController"
participant FS as "File System"
participant DB as "Database"
Admin->>EA : "Open Maison CRUD"
EA->>FS : "Upload image to uploads/images"
FS-->>EA : "Store randomized filename"
EA->>DB : "Persist Maison with image path"
DB-->>EA : "Confirm save"
EA-->>Admin : "Show updated list with image preview"
```

**Diagram sources**
- [MaisonCrudController.php:1-51](file://src/Controller/Admin/MaisonCrudController.php#L1-L51)

**Section sources**
- [MaisonCrudController.php:1-51](file://src/Controller/Admin/MaisonCrudController.php#L1-L51)

## Dependency Analysis
External dependencies relevant to the property management system include Symfony components, Doctrine ORM, and EasyAdmin.

```mermaid
graph TB
Composer["composer.json"]
Symfony["symfony/* packages"]
Doctrine["doctrine/* packages"]
EasyAdmin["easycorp/easyadmin-bundle"]
Twig["twig/* packages"]
Composer --> Symfony
Composer --> Doctrine
Composer --> EasyAdmin
Composer --> Twig
```

**Diagram sources**
- [composer.json:1-111](file://composer.json#L1-L111)

**Section sources**
- [composer.json:1-111](file://composer.json#L1-L111)

## Performance Considerations
- Use repository methods for efficient queries (e.g., findByCity, findLatest)
- Leverage database indexing on frequently filtered columns (city, id)
- Consider pagination in listings for large datasets
- Optimize image sizes and use lazy loading in templates
- Minimize N+1 queries by eager-loading associations where appropriate

[No sources needed since this section provides general guidance]

## Troubleshooting Guide
Common issues and resolutions:
- CSRF token errors during deletion: Ensure the CSRF token is present in the request payload
- Form validation failures: Verify form fields match entity constraints and required fields are filled
- Image upload issues: Confirm upload directory permissions and EasyAdmin field configuration
- Navigation problems: Check route names and base template links

**Section sources**
- [MaisonController.php:74-77](file://src/Controller/MaisonController.php#L74-L77)
- [MaisonType.php:1-36](file://src/Form/MaisonType.php#L1-L36)
- [MaisonCrudController.php:31-35](file://src/Controller/Admin/MaisonCrudController.php#L31-L35)

## Conclusion
The property management system provides a solid foundation for managing properties with CRUD operations, form handling, and administrative capabilities through EasyAdmin. The Maison entity and related components support essential property attributes, owner associations, and basic search/filtering. Extending the system with availability checking, pricing calculations, seasonal rates, and status management would enhance its functionality for a production environment.

## Appendices
- Asset integration: [app.js:1-11](file://assets/app.js#L1-L11)
- Bootstrap integration: [base.html.twig:8-89](file://templates/base.html.twig#L8-L89)

**Section sources**
- [app.js:1-11](file://assets/app.js#L1-L11)
- [base.html.twig:8-89](file://templates/base.html.twig#L8-L89)