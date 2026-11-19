import { getCollection, type CollectionEntry } from 'astro:content';

export const SITE = {
	name: 'Randomness from Gonçalo',
	tagline: 'A site for my experimentation and sharing.',
	email: 'me@goncalovf.com',
	ogImage: '/wp-content/uploads/2020/06/site-logo-ogp.jpg',
	author: {
		'@type': 'Person',
		name: 'Gonçalo Figueiredo',
		givenName: 'Gonçalo',
		familyName: 'Figueiredo',
		email: 'me@goncalovf.com',
		sameAs: 'https://twitter.com/goncalovf',
	},
	language: { '@type': 'Language', name: 'English', alternateName: 'en-US' },
	social: [
		{ name: 'Twitter', href: 'https://twitter.com/goncalovf', icon: 'twitter' },
		{ name: 'GitHub', href: 'https://github.com/goncalovf', icon: 'github' },
		{ name: 'LinkedIn', href: 'https://www.linkedin.com/in/goncalovf/', icon: 'linkedin' },
		{ name: 'Behance', href: 'https://www.behance.net/goncalovf', icon: 'behance' },
	],
} as const;

export const GUIDE_SLUG = 'how-im-investing-my-savings-in-my-20s-and-30s-a-guide';

export type Post = CollectionEntry<'posts'>;

/** Published posts, newest first. */
export async function getPosts(): Promise<Post[]> {
	const posts = await getCollection( 'posts', ( p ) => !p.data.draft );
	return posts.sort( ( a, b ) => b.data.date.getTime() - a.data.date.getTime() );
}

export const postUrl = ( post: Post ) => `/${ post.id }/`;

export const formatDate = ( d: Date ) =>
	d.toLocaleDateString( 'en-GB', { day: 'numeric', month: 'long', year: 'numeric' } );

export const isoDate = ( d: Date ) => d.toISOString().slice( 0, 10 );
