# HospitalBack

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4-EF4223)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1)
![JWT](https://img.shields.io/badge/Auth-JWT-black)
![Composer](https://img.shields.io/badge/Composer-2.x-885630)
![License](https://img.shields.io/badge/Licence-MIT-blue)

---

**API REST de gestion hospitalière — patients, personnel, consultations, pharmacie et planning.**

HospitalBack est le backend d'un système de gestion d'hôpital (HMS). Construit avec CodeIgniter 4, il expose une API REST consommée par un frontend séparé (Vue/Vite) et couvre l'ensemble du parcours de soin : enregistrement du patient, prise des constantes, consultation médicale, prescription, dispensation des produits en pharmacie, ainsi que la gestion du personnel (fonctions, groupes de travail, shifts, absences et remplacements).

---

## Table des matières

1. [Architecture](#1-architecture)
2. [Structure du projet](#2-structure-du-projet)
3. [Domaine fonctionnel](#3-domaine-fonctionnel)
4. [Modèle de données](#4-modèle-de-données)
5. [Flux de données — Parcours patient](#5-flux-de-données--parcours-patient)
6. [Sécurité — JWT et CORS](#6-sécurité--jwt-et-cors)
7. [Endpoints de l'API](#7-endpoints-de-lapi)
8. [Configuration](#8-configuration)
9. [Installation et exécution](#9-installation-et-exécution)
10. [Tests](#10-tests)
11. [Contribution](#11-contribution)
12. [Auteur](#12-auteur)

---

## 1. Architecture

### Patron MVC (CodeIgniter 4)

```
┌──────────────────────────────────────────────────────────┐
│  CLIENT HTTP (Frontend Vue/Vite — localhost:5173)         │
└──────────────────┬──────────────────────────────────────┘
                    │ JSON sur HTTP/HTTPS
┌───────────────────▼──────────────────────────────────────┐
│  FILTER CHAIN                                             │
│  CorsFilter     → autorise l'origine du frontend           │
│  AuthFilter     → valide le Bearer token JWT (routes       │
│                    protégées : filter 'auth')              │
└──────────────────┬──────────────────────────────────────┘
                    │ Requête autorisée
┌───────────────────▼──────────────────────────────────────┐
│  CONTROLLER (Couche C)                                    │
│  20 contrôleurs REST (Patient, Personnel, MedicalAct...)   │
│  - Valide les entrées (règles CodeIgniter Validation)      │
│  - Utilise ResponseTrait → respond() / fail()              │
│  - Orchestre les modèles, gère les transactions            │
└──────────────────┬──────────────────────────────────────┘
                    │
┌───────────────────▼──────────────────────────────────────┐
│  MODEL (Couche M — CodeIgniter Model)                      │
│  20 modèles, jointures SQL via Query Builder                │
│  - Timestamps automatiques (created_at/updated_at)          │
│  - Soft delete (deleted_at)                                 │
└──────────────────┬──────────────────────────────────────┘
                    │
              ┌─────▼─────┐
              │  MySQL /   │
              │  MariaDB   │
              └───────────┘
```

### Principes clés

- **Stateless** : authentification par token JWT signé (`firebase/php-jwt`), aucune session serveur.
- **Séparation par domaine** : chaque table métier a son couple Contrôleur/Modèle/Migration dédié.
- **Traçabilité** : toutes les tables portent `created_at`, `updated_at`, `deleted_at` (soft delete).
- **Intégrité référentielle** : clés étrangères `CASCADE` entre les entités liées (patient ↔ user, acte médical ↔ patient/personnel, etc.).

---

## 2. Structure du projet

```
HospitalBack/
├── app/
│   ├── Config/
│   │   ├── Routes.php          ← Déclaration de tous les groupes de routes REST
│   │   ├── Filters.php         ← Enregistrement des alias 'auth' et 'corsfilter'
│   │   └── Database.php        ← Connexion MySQL/MariaDB
│   ├── Controllers/            ← Couche C — 20 contrôleurs REST (ResponseTrait)
│   │   ├── UserController.php
│   │   ├── PersonnelController.php
│   │   ├── PatientController.php
│   │   ├── MedicalActController.php
│   │   ├── VitalController.php
│   │   ├── ConsultationController.php
│   │   ├── PrescribController.php
│   │   ├── ProductPrescriptionController.php
│   │   ├── ProductController.php / StockController.php / StockMovementController.php
│   │   ├── PurchaseController.php / PurchaseLineController.php
│   │   ├── GroupController.php / GroupAffecterController.php / ShiftController.php
│   │   ├── AbsenceController.php / SubstitutionController.php
│   │   └── FunctionController.php
│   ├── Models/                 ← Couche M — un modèle par table
│   ├── Filters/
│   │   ├── AuthFilter.php      ← Vérifie et décode le Bearer token (HS256)
│   │   └── CorsFilter.php      ← En-têtes CORS pour le frontend
│   └── Database/Migrations/    ← 22 migrations, schéma versionné
├── tests/                      ← PHPUnit (unit, database, session)
├── writable/                   ← Cache, logs, sessions, uploads
├── PROJECT BACHELOR UML DIAGRAMS.pdf/  ← Diagrammes de séquence et de classes
├── composer.json
└── .env                        ← Variables d'environnement (JWT_SECRET, DB...)
```

---

## 3. Domaine fonctionnel

- **Utilisateurs & identité** : compte `User` unique (nom, email, téléphone), spécialisé en `Patient` ou `Personnel` (code d'agent + fonction).
- **Prise en charge médicale** : un `MedicalAct` (constantes ou consultation) rattache un patient à un membre du personnel ; il porte les `VitalSigns` et/ou la `Consultation`.
- **Prescription et pharmacie** : une `Consultation` génère une `Prescribtion`, elle-même liée aux `Product` via `ProductPrescription` ; le stock (`Stock`, `StockMovement`) est mouvementé lors des achats (`Purchase`, `PurchaseLine`).
- **Ressources humaines** : `Function`, `Group` (équipes), `GroupAffecter` (affectations mensuelles), `Shift` (créneaux horaires), `Absence` et `Substitution` (remplacements).

---

## 4. Modèle de données

### Entités principales

| Table | Clé primaire | Champs notables | Relations |
|---|---|---|---|
| `users` | `id_user` | `name_user`, `surname_user`, `email` (unique), `password`, `telephone`, `quarter` | — |
| `patient` | `id_patient` | `emergency_number` | `id_user` → `users` |
| `personel` | `id_personel` | `staff_code` | `id_user` → `users`, `id_function` → `function` |
| `function` | `id_function` | `name`, `description`, `status` (`active`/`desactivated`) | — |
| `MedicalAct` | `id_medicalAct` | `type_act` (`parametre`/`consultation`), `date_act`, `status` | `id_patient`, `id_personel` |
| `VitalSigns` | `id_vi` | `temperature`, `weight`, `blood_pressure`, `height`, `heart_beat` | `id_medicalAct` |
| `consultation` | `id_consult` | `motif`, `symptoms`, `diagnois`, `observation`, `recommendation` | `id_medicalAct` |
| `prescrib` | `id_presc` | `date_presc`, `instructions` | `id_consult` |
| `ProductPres` | `id_ProductPres` | — | `id_presc`, `id_product` |
| `Product` | `id_product` | `name_product`, `description`, `minimum_quantity`, `price` | — |
| `Stock` | `id_stock` | `name_stock`, `expiry_date`, `quantity_available` | — |
| `StockMovement` | `id_stockMvt` | `movement_type`, `quantity`, `movement_date` | `id_stock`, `id_product` |
| `Purchase` | `id_purchase` | `final_amount` | — |
| `PurchaseL` | `id_purchaseL` | — | `id_purchase`, `id_product`, `id_stockMvt` |
| `group` | `id_group` | `name_group`, `type_group`, `working_days`, `start_time`, `end_time` | — |
| `Shift` | `id_shift` | `shift_name`, `start_time`, `end_time` | `id_group` |
| `groupAffecter` | `id_groupAff` | `month`, `year`, `week_number` | `id_group`, `id_personel`, `id_shift` |
| `Absence` | `id_absence` | `motif`, `date`, `statut` (`accepted`/`modified`/`cancel`) | `id_personel` |
| `substitution` | `id_subs` | `date`, `statut` | `id_absence`, `id_personel` |

> Toutes les tables incluent `created_at`, `updated_at` et `deleted_at` (soft delete) et utilisent des clés étrangères en cascade.

---

## 5. Flux de données — Parcours patient

### 5.1 Création d'un acte médical (`POST /medic/create`)

```
Client
  │ POST /medic/create
  │ Body: { id_patient, id_personel, type_act, date_act }
  ▼
MedicalActController::createMediActs()
  ├── Validation (id_patient/id_personel entiers requis, type_act, date_act valides)
  ├── patient_model->where('id_patient', ...)->first()   → 404 si absent
  ├── perso_model->where('id_personel', ...)->first()    → 404 si absent
  ├── DB transaction (transStart)
  └── mediAct_model->insert(...)                         → INSERT MedicalAct
  ▼
201 Created + { message, success, data }
```

### 5.2 Envoi vers consultation (`POST /medic/medical-act/{id}/send-to-consultation`)

```
Client
  │ POST /medic/medical-act/{id}/send-to-consultation
  ▼
MedicalActController::sendToConsultation()
  ├── Recherche de l'acte médical par id
  ├── Passage du statut de l'acte à "en attente de consultation"
  └── Disponible ensuite via GET /medic/medical-act/pending
  ▼
200 OK + acte médical mis à jour
```

### 5.3 Consultation → Prescription → Pharmacie

```
consult/createConsult
  │ motif, symptoms, diagnois, observation, recommendation
  │ id_medicalAct
  ▼
presc/createPresc
  │ date_presc, instructions, id_consult
  ▼
productPresc/create (répété pour chaque produit prescrit)
  │ id_presc, id_product
  ▼
stockMvt/createStockMvt
  │ movement_type, quantity, id_stock, id_product
  └── Décrémente quantity_available du Stock concerné
```

---

## 6. Sécurité — JWT et CORS

### Chaîne de filtres

```
Requête HTTP → CorsFilter (en-têtes CORS + gestion OPTIONS) → AuthFilter (si route protégée) → Contrôleur
```

### `AuthFilter` — Vérification du token

```
1. Extraction du header "Authorization: Bearer <token>"
2. Absence de token → 401 "Access denied"
3. JWT::decode($token, new Key(JWT_SECRET, 'HS256'))
4. Signature invalide ou token expiré → 401 "Access denied"
5. Token valide → la requête continue vers le contrôleur
```

Le filtre `auth` est appliqué sélectivement dans `Routes.php` (ex. `perso.index`, `perso.me`, `purchase.createPurchase`) plutôt qu'à l'ensemble de l'API, ce qui permet de garder certaines routes publiques (connexion, consultation de listes).

### `CorsFilter` — Autorisations cross-origin

```
Access-Control-Allow-Origin: http://localhost:5173
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

> L'origine autorisée est actuellement codée en dur pour le frontend de développement ; à externaliser en variable d'environnement pour la production.

---

## 7. Endpoints de l'API

### Utilisateurs (`/user`)

| Méthode | URL | Description | Corps (JSON) |
|---|---|---|---|
| GET | `/user` | Lister tous les utilisateurs | – |
| GET | `/user/{id}` | Récupérer un utilisateur | – |
| POST | `/user/create` | Créer un utilisateur | `{ name_user, surname_user, email, password, telephone, quarter }` |
| POST | `/user/connect` | Connexion | `{ staff_code, password }` |
| PUT | `/user/update/{id}` | Mettre à jour un utilisateur | Champs partiels |
| DELETE | `/user/delete/{id}` | Supprimer un utilisateur | – |

### Personnel (`/perso`)

| Méthode | URL | Description | Auth |
|---|---|---|---|
| GET | `/perso` | Lister le personnel | 🔒 `auth` |
| GET | `/perso/{id}` | Fiche d'un agent | – |
| GET | `/perso/me` | Profil de l'agent connecté | 🔒 `auth` |
| PUT | `/perso/me` | Modifier son propre profil | 🔒 `auth` |
| POST | `/perso/connect` | Connexion personnel | – |
| POST | `/perso/create` | Créer un agent | – |
| PUT | `/perso/updatePerso/{id}` | Mettre à jour un agent | – |
| DELETE | `/perso/deletePerso/{id}` | Supprimer un agent | – |

### Patients (`/patient`)

| Méthode | URL | Description |
|---|---|---|
| GET | `/patient` | Lister les patients |
| GET | `/patient/{id}` | Fiche patient |
| GET | `/patient/patientHistory/{id}` | Historique médical complet |
| POST | `/patient/create` | Enregistrer un patient |
| PUT | `/patient/update/{id}` | Mettre à jour un patient |
| DELETE | `/patient/delete/{id}` | Supprimer un patient |

### Actes médicaux, constantes et consultations

| Ressource | Base URL | Endpoints clés |
|---|---|---|
| Actes médicaux | `/medic` | `GET /`, `GET /{id}`, `POST /create`, `GET /medical-act/pending`, `POST /medical-act/{id}/send-to-consultation` |
| Constantes vitales | `/vital` | `GET /`, `POST /createVitals`, `POST /updateVitals/{id}`, `GET /medicalAct/{id}` |
| Consultations | `/consult` | `GET /`, `GET /showConsult/{id}`, `POST /createConsult`, `PUT /updateConsult/{id}`, `DELETE /deleteConsult/{id}` |

### Prescriptions et pharmacie

| Ressource | Base URL | Endpoints clés |
|---|---|---|
| Prescriptions | `/presc` | `GET /`, `GET /showPresc/{id}`, `POST /createPresc`, `PUT /updatePresc/{id}`, `DELETE /deletePresc/{id}` |
| Lignes de prescription | `/productPresc` | `GET /`, `GET /presc/{id}`, `POST /create`, `DELETE /delete/{id}` |
| Produits | `/product` | `GET /`, `GET /showProdu/{id}`, `POST /createProdu`, `PUT /updateProdu/{id}`, `DELETE /deleteProdu/{id}` |
| Stock | `/stock` | `GET /`, `GET /showStock/{id}`, `POST /createStock`, `PUT /updateStock/{id}`, `DELETE /deleteStock/{id}` |
| Mouvements de stock | `/stockMvt` | `GET /`, `GET /showStockMvt/{id}`, `POST /createStockMvt`, `DELETE /deleteStockMvt/{id}` |
| Achats | `/purchase` | `GET /`, `GET /showPurchase/{id}`, `GET /receipt/{id}`, `POST /createPurchase` 🔒 `auth`, `DELETE /deletePurchase/{id}` |

### Ressources humaines et planning

| Ressource | Base URL | Endpoints clés |
|---|---|---|
| Fonctions | `/funct` | `GET /`, `GET /{id}`, `POST /create`, `POST /update/{id}`, `POST /delete/{id}` |
| Groupes | `/group` | `GET /`, `GET /showGroup/{id}`, `POST /createGroup`, `PUT /updateGroup/{id}`, `DELETE /deleteGroup/{id}` |
| Affectations | `/groupA` | `GET /`, `GET /showGroupAff/{id}`, `POST /createGroupAff`, `PUT /updateGroupAff/{id}`, `DELETE /deleteGroupAff/{id}` |
| Shifts | `/shift` | `GET /`, `GET /group/{id}`, `POST /createShift`, `PUT /updateShift/{id}`, `DELETE /deleteShift/{id}` |
| Absences | `/absence` | `GET /`, `GET /showAbsence/{id}`, `POST /createAbsence`, `PUT /updateAbsence/{id}`, `DELETE /deleteAbsence/{id}` |
| Remplacements | `/subs` | `GET /`, `GET /showSubstitution/{id}`, `POST /createSubstitution`, `PUT /updateSubstitution/{id}`, `DELETE /deleteSubstitution/{id}` |

> **Format de réponse standard** : chaque endpoint renvoie `{ "message": "...", "success": true|false, "data": {...} }` via le `ResponseTrait` de CodeIgniter.

---

## 8. Configuration

Créez un fichier `.env` à la racine du projet (basé sur `env`) :

```dotenv
CI_ENVIRONMENT = development

# Sécurité
JWT_SECRET = votre_cle_secrete_min_32_caracteres

# Base de données
database.default.hostname = localhost
database.default.database = hospitalback
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

# Journalisation
logger.threshold = 9
```

### Variables d'environnement

| Variable | Défaut | Description |
|---|---|---|
| `CI_ENVIRONMENT` | `production` | Environnement d'exécution (`development`/`testing`/`production`) |
| `JWT_SECRET` | – | Clé secrète HS256 utilisée pour signer/valider les tokens |
| `database.default.hostname` | `localhost` | Hôte MySQL/MariaDB |
| `database.default.database` | – | Nom de la base |
| `database.default.DBDriver` | `MySQLi` | Pilote de base de données |
| `database.default.port` | `3306` | Port MySQL/MariaDB |

---

## 9. Installation et exécution

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/<votre-org>/HospitalBack.git
   cd HospitalBack
   ```
2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```
3. **Configurer l'environnement** : copier `env` vers `.env` et renseigner les variables (voir [§8](#8-configuration)).
4. **Exécuter les migrations**
   ```bash
   php spark migrate
   ```
5. **Lancer le serveur de développement**
   ```bash
   php spark serve
   ```
   L'API démarre par défaut sur `http://localhost:8080`.

---

## 10. Tests

Le projet embarque une suite PHPUnit (`tests/unit`, `tests/database`, `tests/session`).

```bash
composer test
# équivalent à :
vendor/bin/phpunit
```

---

## 11. Contribution

Les contributions sont les bienvenues. Merci d'ouvrir une *issue* pour discuter de tout changement important avant de soumettre une *pull request*, et de veiller à ce que les migrations et les routes restent cohérentes avec la structure existante.

---

## 12. Auteur

**Jizz**

GitHub : [@GyzShaNice](https://github.com/GyzShaNice))

Email : [berenicewandji@gmail.com](mailto:berenicewandji@gmail.com)

---

## 🙏 Remerciements

- CodeIgniter 4
- Firebase PHP-JWT
- PHPUnit
