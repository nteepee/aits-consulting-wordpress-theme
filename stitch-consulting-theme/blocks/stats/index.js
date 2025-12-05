import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/stats', {
	edit: Edit,
	save: save
});
