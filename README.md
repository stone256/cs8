# 🚀 WHAT IS / WHAT IS NOT

> A simple, fast, and minimal PHP framework.  
> **Philosophy:** Write less code, achieve more.
> Not full packed bulky frame
> Idea for programmer who know php,(js,css)
> It pack with basic model/view/control function and fast response time for API call

---

## 📜 License
**MIT License** – Free to use, modify, and distribute.  
🔗 https://en.wikipedia.org/wiki/MIT_License

---

## ⚙️ Requirements

| Requirement | Details |
|------------|--------|
| **PHP** | ≥ 8.0 |
| **Extensions** | `PDO` (required only for `sitemin` and `api` modules) |
| **Database** | MySQL (via PDO) |

---

## 📐 Naming Conventions

### 🔹 PHP (Backend)

| Type | Convention | Example |
|------|-----------|--------|
| Public variables | `snake_case` | `$nick_name` |
| Local/Private variables | Prefix with `_` (optional) | `$_name` |

---

### 🔹 Folders & Files

| Type | Convention | Example |
|------|-----------|--------|
| Folders | `kebab-case` | `local-folder/storage/css/new-css-2024/` |
| CSS files | `kebab-case` | `new-css-2024.css` |

---

### 🔹 Frontend
- Follow **Bootstrap** / **jQuery** conventions.

---

### 🔹 JavaScript

| Type | Convention | Example |
|------|-----------|--------|
| Global variables | `snake_case` | `var server_name` |
| Local/Private variables | `_` prefix or short names | `_name`, `i`, `j`, `k` |
| Results/Response | Short aliases | `rs` (results), `r` (row) |

---

### 🔹 HTML & CSS

| Type | Convention | Example |
|------|-----------|--------|
| `id` / `class` | `kebab-case` (+ optional suffix) | `abc-def-0t` |
| CSS selectors | Match HTML | `.abc-def-0t` |

---

## 🐳 Running Locally with Docker

```bash
cd dockers
docker-compose up -d --remove-orphans
````

* Default port: **8058**
* Map `./src` → `/var/www` (inside container)

### Docker Network Setup

```bash
# Connect to PostgreSQL container
docker network connect docker_default csp8

# Connect to MySQL container
docker network connect bridge csp8
```

* Use Docker Desktop to access container terminal if needed.

---

## 📦 Installation

Clone the repository:

```bash
git clone git@bitbucket.org:petertwa/cs-p8.git
```

---

## 🛠️ Usage Guide

### 🔧 Initial Setup

Inside your project folder:

* Copy config templates:

  * `config/general.sample` → `config/general.php`
  * `config/local.sample` → `config/local.php`

* Enable vendor loading (optional):

```php
define('_LOAD_VENDOR', true);
```

---

## 🌐 Web Requests

* Entry point: `public/index.php`
* Set your web server document root to:
    ../your-project-folder/public


Example:

```
http://www.myproject.root → /var/www/my-project/public
```

---

## 💻 CLI Requests

* Entry file: `x2cli` under your project folder

```bash
php x2cli [ROUTER] [PARAMETERS]
```

Example:

```bash
/var/www/my-project/php x2cli foo/bar id=5\&date=2008-11-11
```

---

## ⚙️ Configuration Files

| Type    | File                 |
| ------- | -------------------- |
| General | `config/general.php` |
| Local   | `config/local.php`   |
| CLI     | `config/x2cli.php`   |

---

## 🔌 Enabling Modules

Create:

```
config/enabled/YOURMODULE.php
```

Example:
 ## config/enabled/foo.php
```php
$modules[] = "/foo";
```
or
 ## config/enabled/welcome_example.php
```php
$modules[] = "/welcome/example";
```

---

## 🔁 Model Overwriting

```
config/overwrite/MODEL_2_NEWMODEL.php
```

Example:

```php
$overwrites['foo_model'] = 'bar_model';
```

⚠️ Only works when using `_factory('xxx')` (factory + singleton pattern).

---

## 📁 Module Structure

```
module/
└── YOURMODULE/
    ├── .router.php
    ├── indexController.php
    ├── view/
    │       └── index/
    │                 ├── gallery.phtml
    │                 └── about-us.phtml
    └── model/
             └── photo.php
```

---

## 👁️ Views

```
module/YOURMODULE/view/[controller]/[method].phtml
```

Example:

```
module/foo/view/index/bar.phtml
```

---

## 🧭 Router Mapping

Defined in `.router.php`:

```php
$routers = array(
    "/foo/bar" => "/foo/index@bar",
);
```

---

## 🎮 Controllers

Defined via router mapping:

```
module/foo/indexController.php
```

```php
function barAction() {
    // ...
}
```

---

## 📦 Packages & Libraries

1. Composer-style packages:

```
.package/_vendor
```

2. Lazy-loaded libraries:

```
.package/_lib/*
```

* Core dependency:

```
.package/xp/_
```

---

## 🎨 Layout

Recommended (not required):

```
layout/
```

---

## 💾 Data Storage

```
data/*
```

---

## ⚙️ System Core

```
.system/*
```

---

## 🌍 Public Directory

```
public/
├── index.php          # DO NOT modify unless necessary
├── .htaccess          # DO NOT modify unless necessary
├── maintenance.html   # optional
├── robots.txt         # optional
```

---

## 📝 Notes

* ✅ Keep `public/index.php` and `.htaccess` untouched unless necessary.
* ✅ Use `_factory()` for model instantiation (enables overriding).
* ✅ Follow naming conventions for autoloading compatibility.

---

## 💡 Tip

Start by exploring:

```
module/example/
```



