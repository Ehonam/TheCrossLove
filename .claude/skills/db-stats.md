# Skill: Database Stats

Requetes SQL utiles pour TheCrossLove.

## Requetes de statistiques

### Comptage general
```sql
-- Total evenements
SELECT COUNT(*) as total_events FROM event;

-- Total utilisateurs
SELECT COUNT(*) as total_users FROM user;

-- Total inscriptions
SELECT COUNT(*) as total_registrations FROM to_register;

-- Total categories
SELECT COUNT(*) as total_categories FROM category;
```

### Statistiques evenements
```sql
-- Evenements par statut
SELECT status, COUNT(*) as count
FROM event
GROUP BY status;

-- Evenements a venir
SELECT COUNT(*) as upcoming
FROM event
WHERE date_start > NOW() AND status = 'active';

-- Evenements passes
SELECT COUNT(*) as past
FROM event
WHERE date_end < NOW();

-- Taux de remplissage moyen
SELECT
    AVG(
        CASE
            WHEN e.max_participants IS NOT NULL AND e.max_participants > 0
            THEN (SELECT COUNT(*) FROM to_register tr WHERE tr.id_event = e.id_event) * 100.0 / e.max_participants
            ELSE NULL
        END
    ) as avg_fill_rate
FROM event e;
```

### Statistiques utilisateurs
```sql
-- Top 5 utilisateurs par inscriptions
SELECT
    u.first_name,
    u.last_name,
    u.email,
    COUNT(tr.id_registration) as registrations_count
FROM user u
LEFT JOIN to_register tr ON tr.id_user = u.id_user
GROUP BY u.id_user
ORDER BY registrations_count DESC
LIMIT 5;

-- Top 5 createurs d'evenements
SELECT
    u.first_name,
    u.last_name,
    COUNT(e.id_event) as events_created
FROM user u
LEFT JOIN event e ON e.id_user = u.id_user
GROUP BY u.id_user
ORDER BY events_created DESC
LIMIT 5;

-- Admins
SELECT email, first_name, last_name
FROM user
WHERE JSON_CONTAINS(roles, '"ROLE_ADMIN"');
```

### Statistiques inscriptions
```sql
-- Inscriptions par statut
SELECT status, COUNT(*) as count
FROM to_register
GROUP BY status;

-- Inscriptions des 30 derniers jours
SELECT DATE(registered_at) as date, COUNT(*) as count
FROM to_register
WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(registered_at)
ORDER BY date;

-- Evenements les plus populaires
SELECT
    e.title,
    COUNT(tr.id_registration) as registrations,
    e.max_participants,
    CASE
        WHEN e.max_participants IS NOT NULL
        THEN ROUND(COUNT(tr.id_registration) * 100.0 / e.max_participants, 1)
        ELSE NULL
    END as fill_rate_percent
FROM event e
LEFT JOIN to_register tr ON tr.id_event = e.id_event
GROUP BY e.id_event
ORDER BY registrations DESC
LIMIT 10;
```

### Statistiques par categorie
```sql
-- Evenements par categorie
SELECT
    c.name as category,
    COUNT(e.id_event) as events_count
FROM category c
LEFT JOIN event e ON e.id_category = c.id_category
GROUP BY c.id_category
ORDER BY events_count DESC;
```

## Execution

### Via Symfony console
```bash
php bin/console doctrine:query:sql "SELECT COUNT(*) FROM event"
```

### Via MySQL client
```bash
mysql -u root -p thecrosslove < query.sql
```

## Workflow

1. Identifier la metrique souhaitee
2. Selectionner la requete appropriee
3. Executer via doctrine:query:sql
4. Formater et presenter les resultats
