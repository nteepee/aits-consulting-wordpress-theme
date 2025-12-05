import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/form', {
	edit: Edit,
	save: save
});
