import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/cta', {
	edit: Edit,
	save: save
});
