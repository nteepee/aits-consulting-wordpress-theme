import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/feature-card', {
	edit: Edit,
	save: save
});
