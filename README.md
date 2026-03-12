# SPIPHOTO FSE

**Un thème Full Site Editing WordPress** conçu pour le club photo de Gournay-en-Bray, entièrement repensé avec une architecture moderne et une expérience utilisateur optimisée.

---

## ✨ Caractéristiques

### Design & UX
- **Layout magazine** multi-colonnes propre à SPIPHOTO
- **Hero section** avec article mis en avant (sticky posts)
- **Cartes d'articles** avec effets hover subtils
- **Sidebar sticky** avec widgets optimisés
- **Topbar** avec ticker actualités animé
- **Dark mode** automatique via `prefers-color-scheme`

### Performance
- Zéro dépendance JavaScript externe
- Scripts chargés en mode `defer`
- **Lazy loading** natif + IntersectionObserver pour les images
- Google Fonts avec `preconnect` et `display=swap`
- Suppression des scripts inutiles (emoji, oEmbed, wlwmanifest)
- Hints de ressources pour les fonts

### Accessibilité
- ✅ Lien "Skip to content" visible au focus
- ✅ Tous les éléments interactifs ont des labels ARIA
- ✅ Respect de `prefers-reduced-motion`
- ✅ Contraste WCAG AA
- ✅ Navigation au clavier complète
- ✅ Landmarks sémantiques (`<header>`, `<main>`, `<aside>`, `<footer>`)

### SEO
- Données structurées JSON-LD pour les articles (Schema.org `Article`)
- Breadcrumb sémantique avec microdata
- En-têtes de sécurité HTTP (X-Content-Type-Options, X-Frame-Options, etc.)

### Fonctionnalités JS
- **Barre de progression** de lecture sur les articles
- **Back to top** animé
- **Overlay de recherche** accessible
- **Smooth scroll** avec offset pour le header sticky
- **Table des matières** avec surbrillance de section active
- **Load More** AJAX pour les archives
- **Bouton copier le lien** pour le partage

---

## 📁 Structure des fichiers

```
spiphoto-fse/
├── style.css                    # Header du thème + styles globaux
├── theme.json                   # Configuration FSE (couleurs, typo, espacements)
├── functions.php                # Setup, widgets, hooks, AJAX
├── templates/
│   ├── index.html               # Accueil (hero + grille + sidebar)
│   ├── single.html              # Article seul
│   ├── archive.html             # Archives, catégories, tags
│   ├── search.html              # Résultats de recherche
│   ├── 404.html                 # Page erreur
│   ├── page.html                # Page standard
│   ├── blank.html               # Template blank (sans header/footer)
│   └── magazine-home.html       # Template accueil magazine
├── parts/
│   ├── header.html              # En-tête (topbar + logo + nav)
│   ├── footer.html              # Pied de page (4 colonnes)
│   ├── sidebar.html             # Sidebar réutilisable
│   ├── breadcrumb.html          # Fil d'Ariane
│   ├── post-meta.html           # Méta d'article
│   └── comments.html            # Section commentaires
├── patterns/                    # Blocs réutilisables (à créer)
├── assets/
│   ├── css/                     # CSS additionnels
│   └── js/
│       ├── main.js              # JS principal (vanilla, 0 dépendances)
│       └── block-variations.js  # Variations de blocs pour l'éditeur
└── languages/                   # Fichiers de traduction
└── .github/workflows/           # Github Action
        └── deploy.yml           # Déploiement automatique
```

---

## 🚀 Installation

1. Téléchargez le thème et décompressez-le dans `/wp-content/themes/spiphoto-fse/`
2. Activez le thème dans **Apparence → Thèmes**
3. Allez dans **Apparence → Éditeur** pour personnaliser via FSE
4. Configurez vos menus dans **Apparence → Éditeur → Navigation**

## ⚙️ Personnalisation

Toute la personnalisation se fait via l'**Éditeur de site** WordPress :
- **Styles globaux** : couleurs, typographies, espacements
- **Templates** : modifiez directement les templates visuellement
- **Template Parts** : header et footer éditables
- **Patterns** : blocs réutilisables préconfigurés

---

## 📋 Exigences

| Logiciel  | Version minimum |
|-----------|----------------|
| WordPress | 6.4+           |
| PHP       | 8.1+           |
| MySQL     | 5.7+ / MariaDB 10.4+ |

---

## 🎨 Palette de couleurs

| Variable            | Valeur     | Usage                    |
|---------------------|------------|--------------------------|
| `--primary`         | `#E8472A`  | CTA, liens, accents      |
| `--primary-dark`    | `#C73A20`  | Hover états              |
| `--secondary`       | `#1A1A2E`  | Header sombre, footer    |
| `--accent`          | `#F5A623`  | Éléments mis en avant    |
| `--surface`         | `#F8F7F4`  | Fond de page             |
| `--text-primary`    | `#1A1A2E`  | Texte principal          |
| `--text-secondary`  | `#5A5A72`  | Texte secondaire         |

---

## 🔤 Typographies

| Police           | Usage                          |
|------------------|-------------------------------|
| Playfair Display | Titres, headlines              |
| Source Serif 4   | Corps de texte, contenu        |
| DM Sans          | UI, labels, navigation, meta   |

---

## 📝 Changelog

### 1.0.0 – Initial Release
- Thème FSE complet propre à SPIPHOTO
- Architecture blocks-first
- Optimisations performance et accessibilité
- Dark mode automatique
- Données structurées JSON-LD
