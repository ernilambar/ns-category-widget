const toggleElement = ( el ) => {
	if ( el.style.display == 'none' ) {
		el.style.display = '';
	} else {
		el.style.display = 'none';
	}
};

export { toggleElement };
