# ⚽ Golstalgia Liga Trivial

Web para gestionar la liga de trivial de los patreons del podcast **Golstalgia**, creado por Josep.

## Stack
- **PHP 8.2** puro con PDO (sin framework)
- **MySQL 8.0**
- **Apache** con mod_rewrite
- **Docker** para desarrollo local

---

## 🚀 Arrancar en local (Docker)

```bash
# 1. Clona el repo
git clone https://github.com/Zerf8/golstalgia.git
cd golstalgia

# 2. Copia el .env (ya incluido con config Docker)
#    Edita si necesitas cambiar puertos u otras variables

# 3. Levanta los contenedores
docker compose up -d --build

# 4. La BD se inicializa automáticamente con database/migrations/001_schema.sql
#    Espera ~10s para que MySQL arranque

# 5. Abre el navegador
open http://localhost:8080
```

### Accesos locales
| Servicio   | URL                      |
|------------|--------------------------|
| App        | http://localhost:8080    |
| phpMyAdmin | http://localhost:8081    |

### Usuario admin por defecto
| Campo | Valor |
|-------|-------|
| Email | admin@golstalgia.com |
| Pass  | Admin1234! |

> ⚠️ Cambia la contraseña del admin tras el primer login.

---

## 🏗️ Estructura del proyecto

```
golstalgia/
├── config/
│   ├── app.php          # Bootstrap, sesión, autoload
│   └── database.php     # Conexión PDO singleton
├── database/
│   └── migrations/
│       └── 001_schema.sql
├── docker/
│   ├── Dockerfile.php
│   └── apache.conf
├── public/
│   ├── css/app.css
│   └── js/app.js
├── src/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── AdminController.php
│   ├── helpers/
│   │   ├── Auth.php
│   │   └── Router.php
│   ├── models/
│   │   ├── UsuarioModel.php
│   │   ├── LigaModel.php
│   │   └── PartidaModel.php  (incluye JornadaModel)
│   └── views/
│       ├── auth/login.php
│       ├── admin/
│       ├── partials/layout.php
│       ├── dashboard.php
│       └── 404.php
├── .env
├── .gitignore
├── .htaccess
├── docker-compose.yml
└── index.php            # Front controller + rutas
```

---

## 🗄️ Base de datos

| Tabla           | Descripción                              |
|-----------------|------------------------------------------|
| `usuarios`      | Participantes y admins                   |
| `ligas`         | Temporadas de la liga                    |
| `liga_usuarios` | Relación N:N usuarios ↔ ligas            |
| `jornadas`      | Jornadas por liga                        |
| `partidas`      | Partidas individuales por jornada        |
| `disponibilidad`| Franjas horarias (Fase 2)                |
| `resultados`    | Resultados de cada partida               |
| `clasificacion` | Tabla de clasificación (calculada auto.) |
| `estadisticas`  | Preguntas acertadas por tema (Fase 3)    |

---

## 🚢 Deploy a Hostinger

```bash
# Conectar via FTP o git
# Copiar todos los archivos excepto docker/, .env

# En Hostinger configurar .env con:
DB_HOST=46.202.172.197
DB_NAME=u214755203_golstalgia2026
DB_USER=u214755203_golstalgia
DB_PASS=/5z*TB7jdMWynV+gol
APP_ENV=production
APP_URL=https://saddlebrown-squirrel-979095.hostingersite.com

# Importar database/migrations/001_schema.sql via phpMyAdmin de Hostinger
```

---

## 📋 Roadmap

- [x] **Fase 1** — Auth, panel admin, CRUD usuarios/ligas/jornadas/resultados, clasificación
- [ ] **Fase 2** — Sistema de quedadas: disponibilidad, confirmación, email, Google Calendar
- [ ] **Fase 3** — Estadísticas: preguntas acertadas por participante y por tema
