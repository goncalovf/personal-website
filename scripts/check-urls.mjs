// URL parity check: every URL the WordPress site answered must answer on the new site,
// either 200 or the expected redirect. Never 404.
// Usage: node scripts/check-urls.mjs [origin]   (default http://localhost:8788, from `wrangler pages dev dist`)
import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const ORIGIN = ( process.argv[ 2 ] || 'http://localhost:8788' ).replace( /\/$/, '' );
const ROOT = new URL( '..', import.meta.url ).pathname;
const GUIDE = '/how-im-investing-my-savings-in-my-20s-and-30s-a-guide/';

const ok = [ '/', '/sitemap-index.xml' ];
const redirects = [
	[ '/muiengineeringlevels', 301, 'https://docs.google.com/spreadsheets/d/1dDdPD-flNXlgZ0E3ZxVvCDx27RFuhVWJrcfcjNu_I8k/edit#gid=0' ],
	[ '/case-study-convert-a-wordpress-multisite-instance-with-1200-websites-to-gutenberg-talk-at-oporto-wp-meetup', 301, '/avada-to-gutenberg/' ],
	[ '/phpcs-wordcamp', 302, '/presentation-in-wordcamp-lisbon-2023/' ],
	[ '/investing-in-your-20s-30s', 302, GUIDE ],
	[ '/pprs', 301, '/pprs-when-are-they-worth-investing-in/' ],
	[ '/visualization-on-how-students-associate-courses-when-applying-to-colleges/', 301, '/vis-students-associate-courses/' ],
	[ '/visualization-of-admission-grades-to-college-courses/', 301, '/vis-college-admission-grades/' ],
	[ '/omeututor-reflection-on-an-entrepreneurial-venture/', 301, '/omeututor-a-reflection/' ],
	[ '/buy-vs-rent-a-calculator/', 301, '/buy-vs-rent-calculator/' ],
	[ `${ GUIDE }1/`, 301, GUIDE ],
	[ '/category/explorations/', 301, '/' ],
	[ '/tag/personal-finances/', 301, '/' ],
	[ '/feed/', 301, '/' ],
	[ '/pprs-when-are-they-worth-investing-in/feed/', 301, '/' ],
];
// Short links and old slugs answer with and without the trailing slash.
for ( const [ from, code, to ] of [ ...redirects ] ) {
	if ( !from.includes( '/', 1 ) || /^\/[^/]+\/$/.test( from ) ) {
		const other = from.endsWith( '/' ) ? from.slice( 0, -1 ) : `${ from }/`;
		redirects.push( [ other, code, to ] );
	}
}

// Every post permalink and guide page.
for ( const f of readdirSync( join( ROOT, 'src/content/posts' ) ) ) {
	// Drafts aren't built, so their URLs 404, as private posts did in WordPress.
	if ( /^draft: true$/m.test( readFileSync( join( ROOT, 'src/content/posts', f ), 'utf8' ).split( /^---$/m )[ 1 ] ) ) continue;
	ok.push( `/${ f.replace( /\.mdx?$/, '' ) }/` );
}
for ( let p = 2; p <= 11; p++ ) ok.push( `${ GUIDE }${ p }/` );

// Every upload referenced by the content (src and href), plus the chart data.
const content = [ 'src/content/posts', 'src/content/guide' ].flatMap( ( d ) =>
	readdirSync( join( ROOT, d ) ).map( ( f ) => readFileSync( join( ROOT, d, f ), 'utf8' ) ) ).join( '\n' );
ok.push( ...new Set( [ ...content.matchAll( /\/wp-content\/uploads\/[^"')\s]+/g ) ].map( ( m ) => m[ 0 ] ) ) );
ok.push( '/wp-content/themes/goncalovf-theme/content/post-data/courses-college-admission-grade.csv' );
ok.push( '/wp-content/themes/goncalovf-theme/content/post-data/courses-preference-association.csv' );

let failures = 0;
const fail = ( msg ) => { failures++; console.log( `FAIL ${ msg }` ); };

for ( const path of ok ) {
	const res = await fetch( ORIGIN + path, { redirect: 'manual', method: 'HEAD' } );
	if ( res.status !== 200 ) fail( `${ path } → ${ res.status }` );
}
for ( const [ path, code, to ] of redirects ) {
	const res = await fetch( ORIGIN + path, { redirect: 'manual', method: 'HEAD' } );
	const loc = ( res.headers.get( 'location' ) || '' ).replace( ORIGIN, '' );
	if ( res.status !== code || loc !== to ) fail( `${ path } → ${ res.status } ${ loc } (want ${ code } ${ to })` );
}
// Content must not link back to the old host, or link checks on a preview would silently hit WordPress.
if ( /https?:\/\/(www\.)?goncalovf\.(com|local)\//.test( content ) ) fail( 'content still has absolute goncalovf.com links' );

console.log( `${ ok.length } pages/files and ${ redirects.length } redirects checked against ${ ORIGIN }: ${ failures ? `${ failures } failed` : 'all good' }` );
process.exit( failures ? 1 : 0 );
