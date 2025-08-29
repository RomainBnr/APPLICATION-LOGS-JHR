# 📊 APPLICATION-LOGS-JHR

Une application web moderne de visualisation et gestion de logs système développée en PHP avec une interface élégante et intuitive.

## 🎯 Description

Cette application permet de collecter, visualiser et filtrer des logs générés par des services applicatifs. Elle offre une interface web sécurisée pour consulter les événements système en temps réel avec des fonctionnalités de recherche et de filtrage avancées.

## ✨ Fonctionnalités

- 🔐 **Authentification sécurisée** avec gestion de sessions
- 📈 **Dashboard interactif** pour visualiser les logs
- 🔍 **Filtrage avancé** par niveau, hostname, message
- 🎨 **Interface moderne** avec thème sombre
- 📱 **Design responsive** adaptatif
- 🐍 **Générateur de logs** automatisé en Python
- 🗄️ **Base de données MariaDB** optimisée

## 🏗️ Architecture

```
APP-LOGS/
├── 📄 index.php              # Page principale du dashboard
├── 🔑 logout.php            # Déconnexion
├── 📊 export.php            # Export des données
├── 📁 controllers/          # Contrôleurs MVC
│   ├── ConnexionController.php
│   └── LogsController.php
├── 📁 models/              # Modèles de données
│   └── LogsModels.php
├── 📁 views/               # Vues et templates
│   ├── login.php          # Page de connexion
│   └── dashboard.php      # Interface dashboard
├── 📁 include/             # Fichiers d'inclusion
│   ├── config.inc.php     # Configuration BDD
│   ├── fct.inc.php        # Fonctions utilitaires
│   └── auth.middleware.inc.php
├── 📁 python/              # Scripts Python
│   └── loggen.py          # Générateur de logs
└── 📁 style/               # Feuilles de style
    └── style.css          # CSS principal
```

## 🚀 Installation

### Prérequis

- PHP 8.0+ avec extensions PDO/MySQL
- MariaDB 10.3+ ou MySQL 5.7+
- Python 3.7+ avec PyMySQL
- Serveur web (Apache/Nginx) ou PHP built-in server

### Configuration de la base de données

1. **Créer la base de données :**
```sql
CREATE DATABASE projetphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Créer les tables nécessaires :**
```sql
-- Table des utilisateurs
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    mdp VARCHAR(255) DEFAULT NULL
);

-- Table des logs
CREATE TABLE log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME NOT NULL,
    level ENUM('INFO', 'WARNING', 'ERROR', 'CRITICAL') NOT NULL,
    application VARCHAR(100) NOT NULL,
    hostname VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    INDEX idx_timestamp (timestamp),
    INDEX idx_level (level),
    INDEX idx_hostname (hostname)
);
```

3. **Créer un utilisateur admin par défaut :**
```sql
INSERT INTO user (username, password) VALUES ('admin', 'admin123');
```

### Installation de l'application

1. **Cloner le repository :**
```bash
git clone https://github.com/RomainBnr/APPLICATION-LOGS-JHR.git
cd APPLICATION-LOGS-JHR
```

2. **Configurer la base de données :**
   - Modifier les paramètres dans `include/config.inc.php`
   - Adapter selon votre configuration MariaDB

3. **Installer les dépendances Python :**
```bash
pip install pymysql
```

## 🎮 Utilisation

### Démarrage du serveur

```bash
# Démarrer le serveur PHP de développement
php -S 192.168.1.128:8080

# Ou utiliser votre serveur web configuré
```

### Accès à l'application

- **URL principale :** `http://192.168.1.128:8080/index.php`
- **Page de connexion :** `http://192.168.1.128:8080/views/login.php`

### Connexion par défaut

- **Utilisateur :** `admin`
- **Mot de passe :** `admin123`

### Génération de logs de test

```bash
# Exécuter le générateur de logs Python
cd python/
python3 loggen.py
```

Le script génère automatiquement des logs réalistes avec différents niveaux de sévérité.

## 🔧 Configuration

### Paramètres de base de données

Fichier : `include/config.inc.php`

```php
define('DB_HOST', '127.0.0.1');    // Adresse du serveur
define('DB_NAME', 'projetphp');    // Nom de la base
define('DB_USER', 'jorge');        // Utilisateur
define('DB_PASS', 'jhr');          // Mot de passe
```

## 🎨 Interface

L'application dispose d'une interface moderne avec :

- **Thème sombre** professionnel
- **Filtres dynamiques** par niveau, hostname et message
- **Tableau responsive** avec pagination
- **Badges colorés** pour les niveaux de log
- **Recherche en temps réel**

### Niveaux de logs

| Niveau | Couleur | Description |
|--------|---------|-------------|
| 🔵 INFO | Bleu | Informations générales |
| 🟡 WARNING | Orange | Avertissements |
| 🔴 ERROR | Rouge | Erreurs |
| 🚨 CRITICAL | Rouge foncé | Erreurs critiques |

## 🛠️ Développement

### Structure MVC

- **Models :** Gestion des données (`models/LogsModels.php`)
- **Views :** Interface utilisateur (`views/`)
- **Controllers :** Logique métier (`controllers/`)

### Sécurité

- Authentification par sessions sécurisées
- Protection CSRF
- Échappement HTML automatique
- Requêtes préparées PDO

## 📝 Logs d'exemple

Le générateur Python crée des logs réalistes pour une application de gestion de tâches :

```
2025-08-29 10:30:15 | INFO | todo-service | PHP-APPS | Nouvelle tâche créée
2025-08-29 10:30:45 | WARNING | todo-service | PHP-APPS | Limite de requêtes atteinte
2025-08-29 10:31:02 | ERROR | todo-service | PHP-APPS | Échec temporaire base, nouvelle tentative
```

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## 👥 Auteurs

- **JorgeCastroSilva** 
- **HugoPlr**
- **RomainBnr**
