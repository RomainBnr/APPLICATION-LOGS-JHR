#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import pymysql
import random
from datetime import datetime

# --- CONFIG BDD ---
DB_HOST = "localhost"
DB_NAME = "projetphp"
DB_USER = "logdash"
DB_PASS = "jhr"
TABLE   = "log"

# --- VALEURS FIXES ---
HOSTNAME    = "PHP-APPS"        # Toujours ce nom
APPLICATION = "todo-service"    # Nom de l’app

# Actions possibles -> (level, message)
ACTIONS = {
    # INFO
    "task_created":        ("INFO",     "Nouvelle tâche créée"),
    "task_updated":        ("INFO",     "Tâche mise à jour"),
    "task_completed":      ("INFO",     "Tâche complétée"),
    # WARNING
    "task_deleted":        ("WARNING",  "Tâche supprimée"),
    "rate_limit_hit":      ("WARNING",  "Limite de requêtes atteinte"),
    # ERROR
    "task_failed":         ("ERROR",    "Erreur lors du traitement de la tâche"),
    "db_retry":            ("ERROR",    "Échec temporaire base, nouvelle tentative"),
    # CRITICAL
    "data_corruption":     ("CRITICAL", "Corruption détectée sur une tâche"),
    "security_alert":      ("CRITICAL", "Accès non autorisé détecté"),
    "queue_stalled":       ("CRITICAL", "File de tâches bloquée"),
}

# Pondérations (CRITICAL plus rare)
WEIGHTS = {
    "task_created": 15,
    "task_updated": 15,
    "task_completed": 12,
    "task_deleted": 8,
    "rate_limit_hit": 8,
    "task_failed": 10,
    "db_retry": 7,
    "data_corruption": 3,
    "security_alert": 2,
    "queue_stalled": 2,
}

def insert_todo_log():
    # Choix pondéré d’une action
    actions = list(WEIGHTS.keys())
    weights = [WEIGHTS[a] for a in actions]
    action = random.choices(actions, weights=weights, k=1)[0]

    level, human_msg = ACTIONS[action]
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    full_message = f"{action} - {human_msg}"

    conn = pymysql.connect(
        host=DB_HOST, user=DB_USER, password=DB_PASS,
        database=DB_NAME, charset="utf8mb4",
        cursorclass=pymysql.cursors.Cursor,
    )
    try:
        with conn.cursor() as cur:
            cur.execute(
                f"INSERT INTO {TABLE} (timestamp, hostname, application, level, message) "
                "VALUES (%s, %s, %s, %s, %s)",
                (now, HOSTNAME, APPLICATION, level, full_message),
            )
        conn.commit()
    finally:
        conn.close()

    print(f"✅ Insert OK : [{now}] {HOSTNAME} {APPLICATION} {level} - {full_message}")

if __name__ == "__main__":
    insert_todo_log()
