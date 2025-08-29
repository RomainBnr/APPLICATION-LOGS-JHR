#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import pymysql
from datetime import datetime

DB_HOST = "192.168.1.128"
DB_USER = "jorge"
DB_PASS = "jhr"
DB_NAME = "projetphp"   # <- IMPORTANT: base existante

FORCED_SOURCE = "PHP-APPS"  # demandé

def insert_log(level: str, message: str, source: str = FORCED_SOURCE, host: str = "PHP-APPS"):
    conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME, charset="utf8mb4")
    try:
        with conn.cursor() as cur:
            sql = """
                INSERT INTO logs(level, source, host, message, created_at)
                VALUES (%s, %s, %s, %s, %s)
            """
            cur.execute(sql, (level, source, host, message, datetime.now()))
        conn.commit()
    finally:
        conn.close()

if __name__ == "__main__":
    # Exemples
    insert_log("info",     "Démarrage du service applicatif")
    insert_log("warning",  "Latence élevée détectée sur une requête")
    insert_log("error",    "Erreur applicative non bloquante")
    insert_log("critical", "Panne critique: service indisponible")  # log critique demandé
