The code for my personal website [goncalovf.com](https://goncalovf.com/), built with [Astro](https://astro.build/) and hosted on Cloudflare Pages.

## Develop

```sh
npm install
npm run dev       # http://localhost:4321
npm run build     # static site in dist/
```

To test the build the way Cloudflare serves it, including `public/_redirects`:

```sh
npx wrangler pages dev dist      # http://localhost:8788
node scripts/check-urls.mjs      # every old WordPress URL answers 200 or the expected redirect
```

## Content

- Posts are Markdown files in `src/content/posts/`. Frontmatter is validated by `src/content.config.ts`.
- The multi-page guide's pages are in `src/content/guide/` (one file per page), with its index in `src/data/guide-index.json`.
- The two 2018 data visualisations are MDX posts that use the components in `src/components/viz/`.
- Images and downloads stay at their old WordPress paths under `public/wp-content/uploads/`, so existing links keep working.

## Analytics

Set `PUBLIC_GA_ID` to a Google Analytics 4 measurement ID (in Cloudflare Pages → Settings → Environment variables). Analytics only load in production builds when it's set.

## Migration from WordPress

The site used to run on WordPress. The posts were exported to Markdown once, and the WordPress code is gone from this branch. The last WordPress version is tagged `wordpress-final`.
