
# 🚀 WHAT IS and NOT

> A simple, fast, and minimal PHP framework.  
> **Philosophy**: Write less code, achieve more.

---

## 📜 License
**MIT License** – Free to use, modify, and distribute.  
🔗 [Read more about MIT License](https://en.wikipedia.org/wiki/MIT_License)

---

## ⚙️ Requirements
| Requirement | Details |
|-------------|---------|
| **PHP** | Version ≥ 8.0 |
| **Extensions** | `PDO` (required only for `sitemin` and `api` modules) |
| **Database** | MySQL (via PDO) |

---

## 📐 Naming Conventions

### 🔹 PHP (Backend)
| Type | Convention | Example |
|------|-----------|---------|
| Public variables | `snake_case` | `$nick_name` |
| Local/Private variables | Prefix with `_` | `$_name` *(optional)* |

### 🔹 Folders & Files
| Type | Convention | Example |
|------|-----------|---------|
| Folders | `kebab-case` | `local-folder/storage/css/new-css-2024/` |
| CSS Files | `kebab-case` | `new-css-2024.css` |

### 🔹 Frontend
- Follow **Bootstrap** / **jQuery** standards.

### 🔹 JavaScript
| Type | Convention | Example |
|------|-----------|---------|
| Global variables | `snake_case` | `var server_name` |
| Local/Private variables | Prefix with `_` or short names | `_name`, `i`, `j`, `k`, `m` |
| Results/Response | Short aliases | `rs` = results/rows/response, `r` = record/row |

### 🔹 HTML & CSS
| Type | Convention | Example |
|------|-----------|---------|
| `id` / `class` attributes | `kebab-case` + optional suffix | `abc-def-0t` |
| CSS selectors | Same as HTML | `.abc-def-0t` |

---

## 🐳 Running Locally with Docker

```bash
cd dockers
            docker-compose up -d --remove-orphans

- port 8058
- set folder ./src to /var/www (of container)

- also check docker network
            # Connect to PostgreSQL container
            docker network connect docker_default csp8

            # Connect to MySQL container  
            docker network connect bridge csp8

- start container terminal use docker-desktop


# INSTALLATION

- clone or just download to your project folder

# USAGE

## Before your start:

- under your project folder,

  - copy "config/general.sample" to "config/general.php"
  - copy "config/local.sample" to "config/local.php"

- to enable vendor under .package/:
  - uncommet the line in "config/general.php"
  -       define('_LOAD_VENDOR', true);

## Enter point for WEB

- web call is handled by "public/index.php"
- so please point your document-root here
- e.g. http://www.myproject.roo -> .../my-project/public

## Enter point - CLI

- CLI call is handled by file "x2cli" under the project folder
- $php x2cli [ROUTER] [PARAMETERS]
  - e.g.
  -       $/var/www/my-project/php x2cli foo/bar id=5\&date=2008-11-11

## Config files

- general: "config/general.php"
- local: "config/local.php"
- cli: "config/x2cli.php" - extra for cli

## Enabling module

- "config/enabled/YOURMODULE.php"
- e.g. "config/enabled/foo.php"
-       $modules[] = "/foo";

## Overwriting model

- "config/overwrite/MODEL_2_NEWMODEL.php"
- e.g. "config/enabled/foo_model_2_bar_model.php"
  -       $overwrites['foo_model']= 'bar_model';
- note: this only works with "\_factory('xxx')" - factory-singleton

## Module structure

    module/
    └── YOURMODULE/          # e.g., module/foo/
        ├── .router.php      # Router definitions
        ├── indexController.php
        └── view/
            └── controller/
                └── method.phtml  # e.g., view/index/bar.phtml


## -View

- "module/YOURMODEL/view/[controller]/[method].phtml"
- e.g. "module/foo/view/index/bar.phtml"

## -Router mapping

- router file is under your module path, wihch defined when you put in your enabled module
- e.g. $modules[] = "/foo";
- router file is : "foo/.router.php
-         $routers = array(
      		  #"FRONT-URI" => "MODULEPATH/CONTROLLERNAME@METHODNAME"
      		  "/foo/bar" => "/foo/index@bar",
          );

## -Controller

- controller is defined in the router file
- e.g. "/foo/bar" => "/foo/index@bar",
-      "foo/indexController.php"
          ..
          function barAction(){
                  ..
          }
          ..

## Packagem and library

1. composed PACKAGE :
   - folder ".package/\_vendor"
2. just use lazyloader:
   - folder ".package/\_lib/\*"

- _xs framework requires ".package/xp/_"

## Layout

- folder: "layout/"
  - this is recommand common layout files, but not enforced.

## Data storage

- folder "data/\*"

## System core

- folder ".system/\*"

## Public

- folder "public"
  - MEDIA, JS.. .. ..
  - ..
  - "index.php" #system file donot touch unless you know what you doing.
  - ".htaccess" #system file donot touch unless you know what you doing.
  - "maintenance.html" #for maintenance model [option]
  - "robots.txt" #robot file [option]


## Git git@bitbucket.org:petertwa/cs-p8.git
