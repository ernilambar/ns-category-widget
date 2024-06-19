import { toggleElement } from './utils.js';

document.addEventListener( 'DOMContentLoaded', function () {
	document.addEventListener( 'click', ( event ) => {
		if (
			event.target.classList.contains( 'btn-show-advanced-tree-settings' )
		) {
			event.preventDefault();

			const targetContainer =
				event.target.parentNode.parentNode.querySelector(
					'.advanced-tree-settings-wrap'
				);

			toggleElement( targetContainer );
		}
	} );

	document.addEventListener( 'change', ( event ) => {
		if ( event.target.classList.contains( 'nscw-taxonomy' ) ) {
			event.preventDefault();

			const formData = new FormData();

			formData.append( 'action', 'populate_categories' );
			formData.append( 'taxonomy', event.target.value );
			formData.append( 'name', event.target.getAttribute( 'data-name' ) );
			formData.append( 'id', event.target.getAttribute( 'data-id' ) );

			fetch( ns_category_widget_ajax_object.ajaxurl, {
				method: 'POST',
				body: formData,
			} )
				.then( ( res ) => res.json() )
				.then( ( rawData ) => {
					const targetDropdown =
						event.target.parentNode.parentNode.querySelector(
							'.nscw-cat-list'
						);
					targetDropdown.innerHTML = rawData.data.html;
				} )
				.catch( ( err ) => console.log( err ) );
		}
	} );
} );
