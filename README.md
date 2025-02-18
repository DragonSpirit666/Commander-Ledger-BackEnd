### Repo original sur bitbucket j'ai changer l'origine pour l'importer sur git.
## Sur bitbucket les gitblames fonctionne diffiramant donc il ne sont pas tous correcte.
La branche P2E2-43-pipeline-automatique-test-back-e est la branche ou j'ai tenter de set up un pipeline, mais le techno que le prof voulait que on utilise fonctionnait pas avec laravel, il a donc désider que pour le cours il n'étais pas nécessaire de continuer

# Guide d'utilisation
## Pour pouvoir utiliser sail :
- ouvrir ~./bashrc
- avec les alias (vers la fin du fichier) ajouter --> alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

## Installation du projet
- Clone le repertoire git dans un environnement linux (ex: Ubuntu) -->
- Ouvrir le projet cloné dans un IDE qui supporte php (ex: phpStorm)
- Ouvrir Docker desktop
- Ajouter vendor en exécutant la commande suivante dans le dossier du projet (sur un terminal bash) [ouvrir le MD en mode edition pour copier] -->
  docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php83-composer:latest \
  composer install --ignore-platform-reqs
- Copier le fichier .env.exemple dans un fichier .env

## Démarrer le projet
- Ouvrir Docker desktop
- Exécuter la commmande dans le dossier du projet --> sail up OU sail up -d

## Fermer le projet
- Exécuter la commmande dans le dossier du projet --> sail down
 OU fermer manuellement le container sur Docker desktop

## Testing
./vendor/bin/sail artisan migrate:fresh --env=testing
./vendor/bin/sail artisan test


