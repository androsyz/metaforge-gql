# Metaforge GQL

**Metaforge GQL** is a metadata-driven GraphQL API server built with PHP. It uses [Workerman](https://github.com/walkor/Workerman) for a fast, event-driven server and [webonyx/graphql-php](https://github.com/webonyx/graphql-php) to handle schema execution.

Instead of writing static GraphQL schemas, you define metadata that describes your types, fields, and relationships — the API and schema are generated and served automatically. Ideal for rapid prototyping, headless systems, and dynamic data models.

> 💡 While schemas are generated automatically, some setups may require database migrations to create the underlying tables referenced in your metadata.

---

### ✨ Features

- ⚙️ Generates and serves a full GraphQL API from metadata definitions
- 🚀 High-performance HTTP server using Workerman
- 🔧 Powered by the extensible `webonyx/graphql-php` library
- 🧪 GraphiQL UI support with live schema inspection

---

### 🚀 Getting Started

### Prerequisites

* **Docker:** Make sure you have Docker installed on your system. You can download it from [https://www.docker.com/get-started](https://www.docker.com/get-started).

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/androsyz/metaforge-gql.git
    cd metaforge-gql
    ```
    
2.  **Create `.env` file:**

    Before running Docker Compose, you might need to create a `.env` file based on a `.env.example` file.

    ```bash
    cp .env.example .env
    ```

    **Note:** You might need to adjust the values in your `.env` file (like database credentials, API keys, etc.) to match your local setup or the defaults specified in the `.env.example` file.
3.  **Run Docker Compose:**

    ```bash
    docker compose up -d
    ```
