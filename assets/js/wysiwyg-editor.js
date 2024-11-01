(function() {
	if ( typeof xhe_waqt_tags === 'undefined' || typeof xhe_waqt_post_type === 'undefined' || typeof xhe_waqt_js === 'undefined' ) {
		return;
	}

	// wrong post type
	if ( -1 === jQuery.inArray( xhe_waqt_post_type, xhe_waqt_js ) ) {
		return;
	}

	// break, if not an button for visual and post type
	var visual = 0,
		post_type = 0,
		i = 0;

	for ( i; i < xhe_waqt_tags.button_quicktags.length; i++ ) {
		if ( 1 === parseInt( xhe_waqt_tags.button_quicktags[ i ][ 'visual' ] ) ) {
			visual = xhe_waqt_tags.button_quicktags[ i ][ 'visual' ];
		}

		if ( 1 === parseInt( xhe_waqt_tags.button_quicktags[ i ][ xhe_waqt_post_type ] ) ) {
			post_type = xhe_waqt_tags.button_quicktags[ i ][ xhe_waqt_post_type ];
		}
	}

	if ( 1 !== parseInt( visual ) || 1 !== parseInt( post_type ) ) {
		return;
	}

	tinymce.create('tinymce.plugins.wp_addquicktags', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished its initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {

			console.log(xhe_waqt_post_type);

			var tiny_tags = xhe_waqt_tags[ 'button_quicktags' ],
				values = [],
				i = 0;

			for ( i; i < tiny_tags.length; i++ ) {
				if ( 1 === parseInt( tiny_tags[ i ][ xhe_waqt_post_type ] ) ) {
					if ( 1 === parseInt(tiny_tags[ i ].visual) ) {
						values.push( {
							text   : tiny_tags[ i ].button_label,
							value  : String( i ),
							tooltip: ( typeof tiny_tags[ i ].title == 'undefined') ? '' : tiny_tags[ i ].title,
							icon   : ( typeof tiny_tags[ i ].dashicon == 'undefined') ? '' : 'icon dashicons dashicons-before ' + tiny_tags[ i ].dashicon
						} );
					}
				}
			}

			ed.addButton('wp_addquicktags', {
				type : 'listbox',
				text : 'WP AddQuicktags',
				label     : 'Select :',
				fixedWidth: true,
				onselect  : function( v ) {

					var value = v.control.settings.value,
						marked = false;

					if ( typeof( tinymce.activeEditor.selection.getContent() ) !== 'undefined' ) {
						marked = true;
					}

					if ( marked === true ) {
						var content = tinymce.activeEditor.selection.getContent(),
							start_content = tinymce.activeEditor.selection.getStart().nodeName,
							all = tinymce.activeEditor.selection.getNode(),
							start = tiny_tags[ value ].start_tag,
							start_tag = start.match( /[a-z]+/ ),
							end = tiny_tags[ value ].end_tag;

						if ( typeof start === 'undefined' ) {
							start = '';
						}

						if ( typeof end === 'undefined' ) {
							end = '';
						}


						// Add tag to content
						if ( start.match( /[a-z]+/i ) !== start_content.toLowerCase() ) {
							tinymce.activeEditor.selection.setContent(
								tiny_tags[ value ].start_tag + content + tiny_tags[ value ].end_tag
							);
						}

						// Remove existing tag
						if ( start.match( /[a-z]+/i ) === start_content.toLowerCase() ) {

							// Remove content with tag
							tinyMCE.activeEditor.dom.remove(
								tinymce.activeEditor.selection.getNode(
									start_content.toLowerCase()
								)
							);
							// Add content, without tag
							tinymce.activeEditor.selection.setContent(
								content
							);

						}

					}
				},
				values    : values
			});

			ed.addCommand('wp_addquicktags', function() {
				jQuery.ajax({
					type: 'POST',
					url: './admin-ajax.php',
					data: {
						action: 'xhe_getTags'
					},
					success: function(data, textStatus, XMLHttpRequest){
						var l_arr_Tags = JSON.parse(data);
						var return_text = l_arr_Tags['start'] + ed.selection.getContent() + l_arr_Tags['end'];
						ed.execCommand('insertHTML', true, return_text);
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						console.log("Error: " + errorThrown);
					}
				});
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wp_addquicktags', tinymce.plugins.wp_addquicktags);
})();
