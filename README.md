# SPIPHOTO FSE

**Thème WordPress Full Site Editing** du club photo SPIPHOTO de Gournay-en-Bray : mode sombre par défaut, palette olive & mauve prune, mise en page magazine pensée pour la photographie.

Version actuelle : **1.4.23** (voir l'en-tête de `style.css`).

---

## ✨ Caractéristiques

### Design & UX
- **Mise en page magazine** : sur l'accueil, le dernier article occupe toute la largeur, puis une grille de cartes sur deux colonnes (9 articles par page, soit 1 + 8) ; les articles épinglés sont dans la barre latérale
- **En-tête en trois niveaux** : barre supérieure (menu + recherche), logo, barre de catégories
- **Mode sombre par défaut**, avec bouton **Jour / Nuit** ; le choix est mémorisé dans le navigateur, et la préférence système sert de valeur initiale
- **Menu réservé aux membres** : un menu différent s'affiche dans la barre supérieure quand on est connecté
- Pied de page sur quatre colonnes

### Performance
- Polices **hébergées localement** (`assets/fonts/`, woff2), sans appel à Google Fonts (RGPD) ; les deux polices critiques sont préchargées
- JavaScript vanilla, sans dépendance, chargé en `defer`
- Scripts WordPress inutiles retirés (emoji, oEmbed, wlwmanifest, RSD)

### Accessibilité
- Lien d'évitement vers le contenu
- Labels ARIA sur les éléments interactifs ajoutés par le thème
- Respect de `prefers-reduced-motion` : les animations d'apparition sont alors entièrement désactivées
- Lisibilité vérifiée en mode sombre et en mode clair

### SEO & sécurité
- Données structurées JSON-LD (Schema.org `Article`) sur les articles
- En-têtes HTTP de sécurité (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`)

### Interface
- Animations discrètes, au service des photos :
  - les cartes d'articles apparaissent en fondu au défilement, en léger décalé ;
  - les photos s'affichent en fondu à la fin de leur chargement ;
  - au survol d'une carte, un léger voile sombre s'efface sur la photo et le titre passe en mauve ;
  - le filet olive sous les titres de section se trace de gauche à droite quand la section apparaît ;
  - à l'ouverture d'un article, le titre puis le bloc auteur-date apparaissent en fondu, suivis de la photo à la une avec un très léger dézoom (une seule fois, en CSS seul) ;
  - les boutons s'enfoncent légèrement au clic.
  Sans JavaScript, tout reste visible ; ce qui est déjà à l'écran au chargement n'est jamais masqué.
- Barre de progression de lecture
- Bouton de retour en haut de page
- Défilement doux vers les ancres, en tenant compte de l'en-tête fixe
- Surbrillance de la section active dans le bloc Table des matières
- Styles de blocs supplémentaires : boutons *Outline* et *Ghost*, image *Shadow*, groupe *Card*, citation *Pull Quote*, liste *Check List*
- Compositions prêtes à l'emploi, dans l'inséreur de blocs sous « SPIPHOTO – … » :

| Catégorie | Composition |
|---|---|
| En-têtes | En-tête de page (surtitre, titre, chapô) |
| Sections | Infos pratiques (lieu, réunions, contact) |
| Articles | Derniers articles en grille · À ne pas manquer (articles épinglés) |

---

## 📁 Structure des fichiers

```
spiphoto_web/
├── style.css          # En-tête du thème + styles globaux (dont le mode clair)
├── theme.json         # Palette, typographies, espacements, templates personnalisés
├── functions.php      # Setup, scripts, menu membres, styles de blocs, SEO, sécurité
├── templates/
│   ├── index.html       # Accueil (mise en avant + grille + barre latérale)
│   ├── single.html      # Article
│   ├── archive.html     # Catégories, étiquettes, archives
│   ├── search.html      # Résultats de recherche
│   ├── page.html        # Page standard
│   ├── full-width.html  # Page pleine largeur
│   ├── no-sidebar.html  # Article ou page sans barre latérale
│   ├── blank.html       # Contenu seul, sans en-tête ni pied de page
│   └── 404.html         # Page introuvable
├── patterns/            # Compositions (une par fichier, en-tête Title/Slug/Categories)
├── menus/               # Menus de navigation livrés avec le thème (créés s'ils manquent)
├── parts/
│   ├── header.html      # En-tête + bouton Jour/Nuit
│   └── footer.html      # Pied de page
└── assets/
    ├── fonts/           # Playfair Display, Source Serif 4, DM Sans (woff2)
    └── js/main.js       # Comportements de l'interface
```

---

## 🚀 Installation

1. Copiez le dossier du thème dans `wp-content/themes/`.
2. Activez-le dans **Apparence → Thèmes**.
3. Personnalisez-le dans **Apparence → Éditeur**.

### Menus

Les menus sont des menus Navigation (objets `wp_navigation`) désignés **par leur slug**, jamais par leur identifiant, qui change d'une installation à l'autre. Le thème choisit le menu à l'affichage d'après la classe du bloc Navigation (`functions.php`, section 3) :

| Emplacement | Classe du bloc | Slug du menu | Fichier de référence |
|---|---|---|---|
| Barre supérieure (visiteurs) | `topbar-navigation` | `menu-principal` | `menus/menu-principal.html` |
| Barre supérieure (membres connectés) | `topbar-navigation` | `loggedmenu` | `menus/loggedmenu.html` |
| Barre des catégories | `cats-navigation` | `categorie` | `menus/categorie.html` |

- **Création automatique** : à l'activation du thème, et à la première page d'administration après une mise à jour, le thème crée les menus absents à partir des fichiers `menus/*.html`. Un menu existant n'est jamais modifié : après une retouche dans l'éditeur, recopiez le menu dans son fichier pour que la version livrée avec le thème reste à jour.
- **Liens portables** : chaque lien vers une page ou une catégorie porte son identifiant, et son adresse est recalculée à l'affichage sur le site courant (site local ou production). Les adresses enregistrées sont relatives (`/agenda/`) et servent de repli.
- Le `ref` écrit dans `parts/header.html` ne sert qu'à l'éditeur du site local.

### Bon à savoir

Les modifications faites dans **Apparence → Éditeur** sont enregistrées dans la base de données et **prennent le pas sur les fichiers** `templates/` et `parts/`. Si une modification d'un fichier semble sans effet, vérifiez que le template ou la partie n'a pas été personnalisé dans l'éditeur (option « Réinitialiser »).

---

## 📋 Exigences

| Logiciel  | Version minimum |
|-----------|-----------------|
| WordPress | 6.4 (testé jusqu'à 7.1) |
| PHP       | 8.1 |

---

## 🎨 Palette de couleurs

Définie dans `theme.json`. Les couleurs de marque sont les mêmes dans les deux modes ; seuls les fonds et les textes changent en mode clair (redéfinis dans `style.css`).

| Couleur | Slug | Mode sombre | Mode clair |
|---|---|---|---|
| Vert olive | `primary` | `#4b5a22` | — |
| Olive foncé | `primary-dark` | `#3a4519` | — |
| Olive clair | `primary-light` | `#6a7e30` | — |
| Mauve prune | `secondary` | `#936893` | — |
| Mauve foncé | `secondary-dark` | `#7a5579` | — |
| Mauve clair | `secondary-light` | `#b08ab0` | — |
| Fond | `bg` | `#0d0e09` | `#f7f3ec` |
| Surface | `surface` | `#161a0e` | `#ffffff` |
| Surface haute | `surface-raised` | `#1e2412` | `#eee9df` |
| Surface top | `surface-top` | `#252c15` | `#e5dfd3` |
| Bordure | `border` | `#2a3118` | `#ddd8cb` |
| Texte principal | `text` | `#f2efe8` | `#1c1e0f` |
| Texte secondaire | `text-secondary` | `#9e9a8e` | `#3e4028` |
| Texte atténué | `text-muted` | `#6b6860` | `#7a7660` |

---

## 🔤 Typographies

| Police           | Usage                          |
|------------------|--------------------------------|
| Playfair Display | Titres                         |
| Source Serif 4   | Corps de texte                 |
| DM Sans          | Interface, navigation, méta    |

---

## 📝 Changelog

### 1.4.23 — optimisation du code (aucun changement visuel)
- Modèles : 31 blocs déclaraient dans leurs réglages des styles absents de leur HTML (marges, polices, bordures, couleurs) ; l'éditeur de site les affichait autrement que le site et les aurait réécrits à la première sauvegarde. Réglages retirés, HTML inchangé : l'éditeur montre désormais exactement le site
- En-tête : le script du bouton Jour/Nuit, de l'ombre au défilement et du lien actif passe du bloc HTML de `parts/header.html` à `main.js` (chargé en différé et mis en cache) ; l'en-tête ne contient plus que des blocs natifs ; le marquage du lien actif, qui comparait des adresses complètes au chemin de la page, fonctionne enfin
- `theme.json` : modèles « page », « search » et « 404 » retirés des modèles personnalisés (ce sont des modèles de la hiérarchie, pas des choix à proposer) ; dégradés (3) et ombres (5) retirés, utilisés nulle part (thème ni contenus)
- Accueil, archives, recherche : la première image à la une est chargée tout de suite, en priorité haute (plus grand élément visible, LCP) ; sur l'accueil elle était en chargement différé
- Partenaires : logos en taille « medium » avec leurs dimensions (pas de décalage à l'affichage), reconnus par leur chemin dans le dossier des médias quel que soit le domaine
- Sous 900 px : loupe alignée sur le bouton Jour/Nuit (le champ masqué la remontait de 4 px) et ronde comme lui (la règle générale des boutons lui imposait 4 px d'arrondi), les deux boutons espacés de 0,5 rem au lieu de 2,5 rem
- CSS : règles mortes retirées (`.skip-to-content`, WordPress utilise `.skip-link` ; variable `--sp-header-h`, plus utilisée)

### 1.4.22
- Pied de page : les partenaires viennent du gestionnaire de liens (catégorie « Nos partenaires », liens publics, dans l'ordre du champ « Note » du lien (1 en premier ; non notés ensuite, par ordre alphabétique) ; logo = « Adresse de l'image » du lien, ou le nom en texte à défaut) au lieu d'une liste écrite dans le thème ; sans partenaire public, l'intitulé « Partenaires » disparaît aussi

### 1.4.21
- Feuille de style et script : leur adresse (`?ver=`) comprend désormais la date de modification du fichier ; un cache (Cloudflare, navigateur) ne peut plus servir une ancienne version après une mise à jour sans changement de numéro

### 1.4.20
- Corps des pages : marge latérale de 16 à 32 px, comme l'en-tête et le pied de page ; sous 1 200 px de large, articles et colonne de droite ne touchent plus les bords de la fenêtre
- Pied de page : bloc « Partenaires » aligné sur le bord droit (à gauche sous 900 px, colonnes empilées)
- Article : la colonne du texte défile au rythme de la galerie, souvent bien plus longue ; les deux colonnes finissent ensemble en bas (au-dessus de 1 024 px, quand elles sont côte à côte)
- Colonne de droite (accueil, catégories) : défilement synchronisé avec les articles ; les deux colonnes partent ensemble en haut et finissent ensemble en bas, la colonne de droite restant visible entre les deux (plus de vide de 134 px au-dessus d'elle)
- Grille commune : en-tête, corps et pied de page alignés sur 1 200 px de contenu, avec la même marge latérale (variables `--sp-gutter` et `--sp-frame`) ; l'en-tête et le pied de page n'ont plus 32 px de retrait par rapport au corps sur grand écran
- Pied de page resserré : plus d'espace automatique de 32 px entre ses blocs, ni entre le titre, l'accroche, le texte et les logos, ni entre le copyright et la licence ; marges du bloc principal de 40 à 28 px ; la place du bouton « retour en haut » est réservée à droite de la licence au lieu de 74 px de vide en bas
- En-tête : plus d'espace automatique de 2rem (32 px) entre la barre du haut, la ligne du titre et de la pellicule, et la barre des catégories (espacement global du thème appliqué au groupe de l'en-tête) ; chaque bande garde ses propres marges
- Recherche : le champ propose des exemples (« Randonnée, moto, Gerberoy… ») et, au clic, une liste d'idées de recherche cliquables ; la liste est fixée dans `functions.php` (`SEARCH_IDEAS`)
- Page de résultats : « Autres idées » sous le titre, et « Essayez plutôt » quand rien n'est trouvé (nouveau bloc « Idées de recherche »)
- Téléphone et tablette : une loupe à droite du logo, qui déplie le champ de recherche (la barre supérieure, qui le contenait, est masquée sous 900 px)
- Bouton Jour/Nuit : il flottait à une distance fixe du bord droit et chevauchait la loupe de recherche tant que la fenêtre ne laissait pas de marge à droite du site (en dessous d'environ 1 350 px). Il est désormais rangé dans l'en-tête : après le champ de recherche de la barre du haut, ou sous 900 px dans la ligne du logo, juste avant la loupe, en bouton rond

### 1.4.19
- Recherche : 9 résultats par page au lieu de 8, soit trois rangées complètes sur la grille de trois colonnes ; les archives gardent 8 articles par page (quatre rangées de deux)

### 1.4.18
- Barre des catégories : les intitulés sont inclinés et posés sur un pas fixe, pour que la barre tienne en largeur (elle gagne un peu en hauteur) : 20 degrés à partir de 850 px de large, sur une barre d'environ 770 px (800 px au plus), 30 degrés et entrées serrées entre 600 et 850 px ; les catégories sont classées de l'intitulé le plus long au plus court, pour un profil régulier et plus d'espace entre elles ; la catégorie en cours est surlignée en olive. Le menu mobile garde ses intitulés à l'horizontale

### 1.4.17
- Page d'article : le texte (30 %) et la galerie (70 %) s'affichent côte à côte ; sous 1 024 px, la galerie passe sous le texte. La photo à la une n'est plus affichée sur la page de l'article (elle reste utilisée pour les cartes et les partages)
- « À lire aussi » : 6 articles au lieu de 3 (plus l'article épinglé, qui s'y ajoutait), choisis d'abord parmi ceux qui partagent à la fois un lieu (étiquette) et une catégorie avec l'article, puis un lieu seul, puis la catégorie seule, puis parmi les plus récents ; sans l'article affiché ni les articles épinglés
- Les galeries (Meow Gallery ou galerie WordPress) restent dans le contenu des articles : elles sont retirées du texte au rendu et affichées par le nouveau bloc « Galerie de l'article » ; un article sans galerie s'affiche sur une seule colonne
- Les encadrés de la barre latérale passent sous l'article : « À ne pas manquer » à gauche, « Séries » et « Lieux » l'un sous l'autre à droite, les deux colonnes finissant à la même hauteur

### 1.4.3
- Galeries Meow : elles occupent toute la largeur de la colonne de l'article au lieu de la largeur de lecture du texte ; en mise en page « justified », les photos ne s'affichent plus une par ligne

### 1.4.2
- Liste des catégories : le nombre d'articles reste sur la ligne de sa catégorie, aligné à droite (il passait à la ligne)
- Encadrés de la barre latérale : retour de l'espacement et du filet court sous le titre, perdus avec le passage en pile flex de la 1.4.1

### 1.4.1
- Boutons : texte en `--sp-on-brand` (le texte sombre sur fond olive était illisible en mode clair)
- Templates et parties : plus aucun bloc invalide dans l'éditeur (JSON réparé, commentaires retirés de l'intérieur des blocs, styles alignés sur les attributs) ; le rendu public est inchangé
- Éditeur de site : les groupes déclarés en `"layout":{"type":"vertical"}` (type inexistant, qui faisait planter l'aperçu de l'en-tête, des cartes et des encadrés) passent en pile flex verticale ; leurs espacements `blockGap` s'appliquent désormais, d'où un en-tête et des cartes un peu plus compacts
- Barres latérales : balise `<aside>` portée par un groupe, la colonne ne pouvant pas changer de balise
- Citations : correction d'une variable de couleur inexistante dans `theme.json`

### 1.4.0
- Quatre compositions réparties dans trois catégories « SPIPHOTO » : en-tête de page, infos pratiques, derniers articles, articles épinglés ; la catégorie CTA, restée vide, est retirée
- Styles de bloc : retrait de ceux qui étaient sans effet ou faisaient doublon avec WordPress ; CSS pour *Shadow*, *Card*, *Pull Quote* et *Check List*
- Retrait du CSS inutilisé de la section « hero »

### 1.3.0
- Palette olive & mauve prune, mode sombre par défaut avec bouton Jour/Nuit
- Polices hébergées localement (suppression de Google Fonts)
- Menu de la barre supérieure réservé aux membres connectés
- Nouveaux templates : pleine largeur, sans barre latérale, vide
- Corrections de lisibilité dans les deux modes
- Retrait du code inutilisé (zones de widgets, chargement AJAX « Load more », variations de blocs)

### 1.0.0
- Première version du thème FSE
