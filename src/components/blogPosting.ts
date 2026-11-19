import { SITE, isoDate, type Post } from '../site';

/** schema.org BlogPosting, mirroring what the WordPress site emitted. */
export function blogPosting( post: Post, url: URL | string ) {
	const href = String( url );
	return {
		'@type': 'BlogPosting',
		'@id': href,
		mainEntityOfPage: href,
		url: href,
		headline: post.data.title,
		abstract: post.data.description,
		author: SITE.author,
		publisher: SITE.author,
		inLanguage: SITE.language,
		datePublished: isoDate( post.data.date ),
		dateModified: isoDate( post.data.updated ),
		keywords: post.data.tags,
		timeRequired: `PT${ post.data.readingTime }M`,
	};
}
