# La Fabrique — Design Tokens

Design system basé sur le bundle Claude Design `la-fabrique-benfeld-design-system`.

## Palette de couleurs

| Nom | Token CSS | Valeur | Usage |
|-----|-----------|--------|-------|
| Brique primary | `--brique-500` | `#c85226` | Boutons, accents, logo |
| Brique hover | `--brique-600` | `#b1411c` | Hover sur boutons primaires |
| Brique dark | `--brique-700` | `#8e3217` | Section hero inverted, sidebar login |
| Ocre | `--ocre-400` | `#e3a528` | Accent secondaire, filet cartes événements |
| Mousse | `--mousse-500` | `#4a6e30` | Pilier écologie, badges succès |
| Crème page | `--creme-50` | `#fdfaf3` | Fond de page (jamais blanc pur) |
| Crème surface | `--creme-100` | `#f8f1e1` | Cartes, surfaces |
| Encre headlines | `--encre-600` | `#1d1a10` | Titres, fond footer/sidebar |
| Encre body | `--encre-500` | `#2b2517` | Corps de texte |

## Typographie

| Rôle | Font | Usage |
|------|------|-------|
| Display | Newsreader (Google Fonts) | H1, H2, H3, quotes, leads |
| Sans | Public Sans (Google Fonts) | Corps, boutons, labels, UI |
| Script | Caveat (Google Fonts) | Signatures, annotations, footer tagline |
| Mono | JetBrains Mono (Google Fonts) | Dates, codes, métadonnées d'événements |

## Classes CSS disponibles

### Typographie
- `.fb-h1` / `.fb-h2` / `.fb-h3` — Titres display
- `.fb-eyebrow` — Label MAJUSCULES brique au-dessus d'un titre
- `.fb-lead` — Chapô Newsreader
- `.fb-body` — Corps Public Sans
- `.fb-script` — Accent Caveat
- `.fb-mono` — Mono pour données/dates
- `.fb-italic-accent` — Italique brique (ex: titres hero)

### Boutons
- `.fb-btn .fb-btn-primary` — Brique plein (CTA principal)
- `.fb-btn .fb-btn-outline` — Contour brique
- `.fb-btn .fb-btn-ghost` — Transparent
- `.fb-btn-sm` / `.fb-btn-lg` — Tailles
- `.fb-btn-block` — Pleine largeur

### Badges
- `.fb-badge .fb-badge-mousse` — Vert (actif, succès)
- `.fb-badge .fb-badge-ocre` — Or (neutre)
- `.fb-badge .fb-badge-brique` — Rouge (alerte)

### Layout public (`fb-*`)
- `.fb-header` — Nav sticky, fond crème semi-transparent
- `.fb-hero` — Hero 2 colonnes avec motif colombage
- `.fb-section` — Section standard max-width 1280px
- `.fb-section-creme` — Section fond crème-100
- `.fb-section-brique` — Section fond brique-700 (testimonial)
- `.fb-piliers-grid` — Grille 4 piliers
- `.fb-pilier` — Carte pilier
- `.fb-events-grid` — Grille 3 événements
- `.fb-event-card` — Carte événement avec filet coloré en haut
- `.fb-cta` — Bloc CTA 2 colonnes
- `.fb-footer` — Footer encre-600 avec motif colombage

### Espace adhérent (`ea-*`)
- `.ea-login-page` — Layout split 2 colonnes (brique + crème)
- `.ea-app` — Layout dashboard (sidebar + main)
- `.ea-side` — Sidebar encre-600
- `.ea-main` — Zone principale dashboard
- `.ea-stats` — Grille 4 statistiques
- `.ea-panel` — Panneau blanc avec ombre
- `.ea-event-row` — Ligne événement avec date en bloc

### Admin (`admin-*`)
- `.admin-layout` — Grid sidebar + main
- `.admin-sidebar` — Sidebar encre-600
- `.admin-table` — Tableau HTML sobre

## Ombres

Toutes teintées brun-rouge (`rgba(74, 28, 16, …)`) :
- `--shadow-xs` / `--shadow-sm` / `--shadow-md` / `--shadow-lg` / `--shadow-xl`
- `--shadow-inset` — Pour champs de formulaire
- `--glow-brique` — Focus ring chaleureux

## Assets SVG

Dans `public/images/` :
- `logo-mark.svg` — Pictogramme maison alsacienne (cercle crème, toit brique, colombages)
- `logo-horizontal.svg` / `logo-stacked.svg` — Variantes logo
- `pattern-colombage.svg` — Motif décoratif (filigrane 4-8% opacité)
- `pilier-*.svg` — Pictogrammes des 4 piliers

## Motif colombage

Utilisé en filigrane sur : hero, section testimonial, footer, sidebar login, CTA.
Toujours en opacité basse (4-8%) avec un filtre CSS pour colorer.
Ne jamais l'utiliser comme élément dominant.
