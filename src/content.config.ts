import { defineCollection } from 'astro:content';
import { z } from 'astro/zod';
import { glob } from 'astro/loaders';

const posts = defineCollection( {
	loader: glob( { pattern: '*.{md,mdx}', base: './src/content/posts' } ),
	schema: z.object( {
		title: z.string(),
		description: z.string(),
		date: z.coerce.date(),
		updated: z.coerce.date(),
		tags: z.array( z.string() ).default( [] ),
		pinned: z.boolean().default( false ),
		readingTime: z.number().int().positive(),
		cover: z.string().optional(),
		// The body lives in the `guide` collection, one entry per page.
		multiPage: z.boolean().default( false ),
		draft: z.boolean().default( false ),
	} ),
} );

const guide = defineCollection( {
	loader: glob( { pattern: '*.md', base: './src/content/guide' } ),
	schema: z.object( {
		page: z.number().int().positive(),
	} ),
} );

export const collections = { posts, guide };
