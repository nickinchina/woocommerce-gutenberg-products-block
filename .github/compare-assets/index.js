/**
 * External dependencies
 */
const { getOctokit, context } = require( '@actions/github' );
const { setFailed, getInput } = require( '@actions/core' );

const runner = async () => {
	try {
		const token = getInput( 'repo-token', { required: true } );
		const octokit = getOctokit( token );
		const payload = context.payload;
		const repo = payload.repository.name;
		const owner = payload.repository.owner.login;
		const oldAssets = require( '../../' +
			getInput( 'compare', {
				required: true,
			} ) );

		if ( ! oldAssets ) {
			return;
		}

		const newAssets = require( '../../build/assets.json' );

		if ( ! newAssets ) {
			return;
		}

		const changes = Object.fromEntries(
			Object.entries( newAssets )
				.map( ( [ key, { dependencies = [] } ] ) => {
					const oldDependencies = oldAssets[ key ].dependencies || [];
					/*const added = dependencies.filter(
						( dependency ) =>
							! oldDependencies.includes( dependency )
					);
					const removed = oldDependencies.filter(
						( dependency ) =>
							! dependencies.includes( dependency )
					);*/
					const currentChanges = {
						added: dependencies,
						removed: [],
					};
					return currentChanges.length
						? [ key, currentChanges ]
						: null;
				} )
				.filter( Boolean )
		);

		if ( changes.length === 0 ) {
			return;
		}

		let reportContent = '';
		Object.entries( changes ).forEach(
			( [ handle, { added, removed } ] ) => {
				const addedDeps = added.length
					? '`' + added.implode( '`, `' ) + '`'
					: '';
				const removedDeps = removed.length
					? '`' + removed.implode( '`, `' ) + '`'
					: '';

				let icon = '';

				if ( added.length && removed.length ) {
					icon = '❓';
				} else if ( added.length ) {
					icon = '⚠️';
				} else if ( removed.length ) {
					icon = '🎉';
				}

				reportContent +=
					`| \`${ handle }\` | ${ addedDeps } | ${ removedDeps } | ${ icon } |` +
					'\n';
			}
		);

		console.log( reportContent );

		await octokit.rest.issues.createComment( {
			owner,
			repo,
			issue_number: payload.pull_request.number,
			body:
				'# Script Dependencies Report' +
				'The `compare-assets` action has detected some changed script dependencies between this branch and ' +
				'trunk. Please review and confirm the following are correct before merging.' +
				'| Script Handle | Added | Removed | |' +
				'| ------------- | ------| ------- | |' +
				reportContent +
				'__This comment was automatically added by the `./github/compare-assets` action.__',
		} );
	} catch ( error ) {
		setFailed( error.message );
	}
};

runner();
