# SPIPHOTO FSE

**Thème WordPress Full Site Editing** du club photo SPIPHOTO de Gournay-en-Bray : mode sombre par défaut, palette olive & mauve prune, mise en page magazine pensée pour la photographie.

Version actuelle : **1.3.0** (voir l'en-tête de `style.css`).

---

## ✨ Caractéristiques

### Design & UX
- **Mise en page magazine** : article mis en avant (articles épinglés) puis grille de cartes sur deux colonnes, avec barre latérale
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
- Respect de `prefers-reduced-motion`
- Lisibilité vérifiée en mode sombre et en mode clair

### SEO & sécurité
- Données structurées JSON-LD (Schema.org `Article`) sur les articles
- En-têtes HTTP de sécurité (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`)

### Interface
- Barre de progression de lecture
- Bouton de retour en haut de page
- Défilement doux vers les ancres, en tenant compte de l'en-tête fixe
- Surbrillance de la section active dans le bloc Table des matières
- Styles de blocs supplémentaires : boutons *Outline* et *Ghost*, images *Rounded*, *Shadow* et *Frame*, groupes *Card* et *Glass*, séparateur *Dots*, etc.

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

Les menus sont des blocs Navigation qui pointent vers des menus existants de la base WordPress, par leur identifiant :

| Emplacement | Fichier | Identifiant |
|---|---|---|
| Barre supérieure (visiteurs) | `parts/header.html` | 64676 |
| Barre supérieure (membres connectés) | `functions.php`, constante `NAV_MEMBRES` | 64675 |
| Barre des catégories | `parts/header.html` | 64677 |

Sur une autre installation, ces identifiants n'existent pas : il faut réaffecter les menus dans l'éditeur, et mettre à jour `NAV_MEMBRES`.

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

### 1.3.0
- Palette olive & mauve prune, mode sombre par défaut avec bouton Jour/Nuit
- Polices hébergées localement (suppression de Google Fonts)
- Menu de la barre supérieure réservé aux membres connectés
- Nouveaux templates : pleine largeur, sans barre latérale, vide
- Corrections de lisibilité dans les deux modes
- Retrait du code inutilisé (zones de widgets, chargement AJAX « Load more », variations de blocs)

### 1.0.0
- Première version du thème FSE
