import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('stitch/testimonial', {
	edit: Edit,
	save: save
});
