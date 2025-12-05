import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/card-grid', {
	edit: Edit,
	save: save
});
