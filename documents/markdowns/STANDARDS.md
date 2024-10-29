
# Documentation des standards

Voici le document des standards pour Commander_Ledger Back_End. Nous utiliserons PHP_CodeSniffer qui respecte les standards PSR-12. PHP_CodeSniffer permettre de vérifié le code sous une commande ou/et par une vérification git.


## Auteur

- Paul Bilodeau


## Installation

Pour installer dans le projet.

À ajouter au fichier docker-compose.yml

```bash
  phpcs:
    image: "squizlabs/phpcs"
    container_name: laravel_phpcs
    volumes:
      - .:/var/www
    working_dir: /var/www
    command: "--standard=PSR12 ./app"
    networks:
      - laravel_net
```


## Utilisation

Après l'installation, tu peux l'utiliser comme ligne de commande.
```bash
./vendor/bin/sail phpcs --standard=PSR12 ./app
```

Automatisation via sail
```json
"scripts": {
    "phpcs": "./vendor/bin/sail phpcs --standard=PSR12 ./app"
}
```
pour exécuter
```bash
sail phpcs --standard=PSR12 ./app
```


## Normes

Voici un aperçu des règles principales de PSR-12 et des exemples pour adjacente.

### 1.Espaces et indentations
- Indentation par 4 espaces : PSR-12 recommande d'utiliser 4 espaces pour l'indentation (pas de tabulations).
```php
function example() 
{
    if (true) {
        echo 'PSR-12 Standard!';
    }
}
```

### 2.Ligne de code maximale: 120 caractères
- Le nombre de caractères par ligne ne doit pas dépasser 120. Si une ligne est trop longue, elle doit être découpée en plusieurs lignes.

### 3.Namespace et utilise
- Les namespaces doivent toujours être déclarés sur la première ligne d’un fichier, suivis d’un saut de ligne.
- Les déclarations de use doivent être regroupées et placées après le namespace.

### 4. Classes, propriétés et méthodes
- Nom des classes en PascalCase : Les noms des classes doivent utiliser le style PascalCase (Majuscule pour chaque mot).
- Visibilité explicite : Les propriétés et méthodes doivent déclarer explicitement leur visibilité (public, protected ou private). 

### 5. Constance de classe
- Les constantes de classe doivent être en majuscules avec des underscores entre les mots.

### 6. Retour de ligne accolade
- Après l’accolade ouvrante d’une classe ou d’une méthode, il doit y avoir un saut de ligne.
```php
class User
{
    public function getName()
    {
        return $this->name;
    }
}
```

### 7. Formatage des parenthèses
- Les parenthèses doivent coller à l’expression qu’elles encadrent, et un espace doit être utilisé avant les accolades ouvrantes d’une structure de contrôle.
```php
if ($condition) {
    // code
}
```

### 8. Utilisation des déclarations strictes
- Avec une vérification stricte des types, le code devient plus prévisible et les erreurs potentielles sont détectées plus tôt.
```php
<?php

declare(strict_types=1);

function add(int $a, int $b): int
{
    return $a + $b;
}
```

### 9.Fonctionnalités modernes de php
- Les fonctions/méthodes doivent déclarer explicitement leur type de retour si applicable.
```php
function getUserName(): string
{
    return 'John';
}
```

### 10. Saut de ligne entre les méthodes
- Il doit y avoir un saut de ligne entre deux méthodes dans une classe pour plus de lisibilité.

### 11.Code conditionnel ternaire et nul coalescent
- Le ternaire et l'opérateur nul coalescent (??) doivent être utilisés pour simplifier les expressions conditionnelles lorsque cela est approprié.
```php
$status = $user->isActive() ? 'active' : 'inactive';

$username = $request->username ?? 'guest';
```

### 12.Utilisation des lambdas et fonctions anonymes
- Les fonctions anonymes doivent être formatées avec des parenthèses autour des paramètres et des accolades pour le corps de la fonction.
```php
$numbers = array_filter([1, 2, 3], function ($number) {
    return $number > 1;
});
```
## Git
Pour respecter les standards de codage avant de pousser le code sur Bitbucket, nous pouvons ajouter un hook Git.

### 1. Crée un fichier pre-commit dans .git/hooks/
```bash
#!/bin/sh

docker-compose run phpcs
```

### 2. Fichier exécutable
```bash
chmod +x .git/hooks/pre-commit
```
