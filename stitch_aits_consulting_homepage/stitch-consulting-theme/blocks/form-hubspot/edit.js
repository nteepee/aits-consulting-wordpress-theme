import { registerBlockType } from '@wordpress/blocks';
import {
	InspectorControls,
	useBlockProps,
	AlignmentToolbar,
	BlockAlignmentToolbar,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	__experimentalInputControl: InputControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import './edit.css';

registerBlockType( 'stitch/form-hubspot', {
	title: __( 'HubSpot Form', 'stitch-consulting' ),
	description: __( 'Embed a HubSpot form with custom styling and configuration', 'stitch-consulting' ),
	category: 'stitch',
	icon: 'format-aside',
	edit: ( { attributes, setAttributes } ) => {
		const {
			portalId,
			formId,
			title,
			description,
			showTitle,
			align,
		} = attributes;

		const blockProps = useBlockProps( {
			className: `align${align || 'none'}`,
		} );

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'HubSpot Settings', 'stitch-consulting' ) }>
						<TextControl
							label={ __( 'Portal ID', 'stitch-consulting' ) }
							value={ portalId }
							onChange={ ( value ) => setAttributes( { portalId: value } ) }
							help={ __( 'Your HubSpot Portal ID (numeric)', 'stitch-consulting' ) }
							type="text"
						/>
						<TextControl
							label={ __( 'Form ID', 'stitch-consulting' ) }
							value={ formId }
							onChange={ ( value ) => setAttributes( { formId: value } ) }
							help={ __( 'The HubSpot Form ID to embed', 'stitch-consulting' ) }
							type="text"
						/>
					</PanelBody>

					<PanelBody title={ __( 'Form Appearance', 'stitch-consulting' ) }>
						<TextControl
							label={ __( 'Title', 'stitch-consulting' ) }
							value={ title }
							onChange={ ( value ) => setAttributes( { title: value } ) }
							help={ __( 'Optional title to display above the form', 'stitch-consulting' ) }
							type="text"
						/>
						<ToggleControl
							label={ __( 'Show Title', 'stitch-consulting' ) }
							checked={ showTitle }
							onChange={ ( value ) => setAttributes( { showTitle: value } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Alignment', 'stitch-consulting' ) }>
						<BlockAlignmentToolbar
							value={ align }
							onChange={ ( value ) => setAttributes( { align: value || 'none' } ) }
							controls={ [ 'wide', 'full' ] }
						/>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<div className="hubspot-form-editor">
						{ ! portalId || ! formId ? (
							<div className="hubspot-form-placeholder">
								<p>
									<strong>{ __( 'HubSpot Form', 'stitch-consulting' ) }</strong>
								</p>
								<p>
									{ __( 'Configure Portal ID and Form ID in the block settings to display the form.', 'stitch-consulting' ) }
								</p>
							</div>
						) : (
							<div className="hubspot-form-preview">
								<div className="hubspot-form-info">
									{ showTitle && title && (
										<h2>{ title }</h2>
									) }
									<p>
										{ __( 'Portal ID:', 'stitch-consulting' ) } <strong>{ portalId }</strong>
									</p>
									<p>
										{ __( 'Form ID:', 'stitch-consulting' ) } <strong>{ formId }</strong>
									</p>
									<p className="hubspot-form-note">
										{ __( 'The form will be displayed on the frontend.', 'stitch-consulting' ) }
									</p>
								</div>
							</div>
						) }
					</div>
				</div>
			</>
		);
	},
	save: () => null, // Use PHP render.php for output
} );
