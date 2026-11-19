// @ts-check
import { defineConfig } from 'astro/config';
import mdx from '@astrojs/mdx';
import sitemap from '@astrojs/sitemap';

export default defineConfig( {
	site: 'https://goncalovf.com',
	// WordPress served every page at /slug/ — keep the same URLs.
	trailingSlash: 'always',
	build: { format: 'directory' },
	integrations: [ mdx(), sitemap() ],
	markdown: {
		shikiConfig: { theme: 'github-light' },
	},
} );
