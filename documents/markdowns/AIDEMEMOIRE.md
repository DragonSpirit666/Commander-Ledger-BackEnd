## Commandes utiles
### Migrations
- sail artisan migrate --> Exécute les migrations
- sail artisan migrate:refresh --> Pour réexécuter les migrations qui ont déja été rouler
- sail artisan make:migration <nom_migration> --create=nomTable --> Pour créer une nouvelle table
    - Ex : sail artisan make:migration create_utilisateurs_table --create=Utilisateurs
- sail artisan make:migration <nom_migration> --table=nomTable --> Pour faire une migration pour modifier une table existante
    - Ex : sail artisan make:migration add_pseudo_to_utilisateurs_table -table=Utilisateurs
- sail artisan migrate:fresh --seed --> Refaire les migrations et seed la base de données
- sail artisan migrate:fresh --> Refait les migrations (drop les tables et les recrée)

### Seeders
- sail artisan make:seeder <NomTableSeeder> --> Pour ajouter des données à la base de données lors de sa création
    - Ex : sail artisan make:seeder UtilisateurSeeder
```sh 
  sail artisan db:seed --class=<NomTableSeeder>
 ```
- --> Peupler la bd avec le seeder
- sail artisan db:seed --> Peupler la bd avec tous les seeders
- sail artisan migrate:fresh --seed --> Refaire les migrations et seed la base de données

### Models
- sail artisan make:model NomTableSingulier --> Pour créer le model apres avoir fait la migration d'une table
    - Ex : sail artisan make:model Utilisateur

### Factory
- sail artisan make:factory <NomTableFactory> --model=NomModel --> Generer des valeurs par defaut random (peut utiliser des faker)
    - Ex : sail artisan make:factory UtilisateurFactory --model:Utilisateur

### Controllers
- sail artisan make:controller <NomTableController> --> Pour créer un controlleur
    - Ex : sail artisan make:controller UtilisateurController

### Requests (validation)
- sail artisan make:request <NomTableRequest> --> Pour créer une request

### Authentification
- sail composer require laravel/breeze --dev --> Ajouter une dépendance a breeze
- sail artisan breeze:install --> ajoute les ressources nécessaire pour la gestion de l'authentification
    - Blade with Alpine
    - No
    - PHPUnit

### Création de plusieurs ressources
- sail artisan make:model <NomModel/Table> --migration --controller --resource --requests --factory --seed
  --> Creer tout les elements mentionner precedement (les --smth). On peut retirer des elements selon nos besoins
    - Ex : sail artisan make:model Utilisateur --migration --controller --requests

### Policy
- sail artisan make:policy <NomTablePolicy> --model=NomTable --> Pour gerer les autorisations pour les routes
    - Ex : sail artisan make:policy UtilisateurPolicy --model=Utilisateur

### Traduction
- sail artisan lang:publish --> Pour creer le dossier et les fichiers nécessaire pour la gestion de la traduction
