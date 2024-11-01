(function($) {
	"use strict";

    function str_replace( search, replace, subject ) {
        return subject.split(search).join(replace);
    }

    
    function fieldSelect2() {
		$(".xhe-tags").select2({
			tags: true,
			width: '100%'
		});
    }

	var XHESetting = {
		init: function() {
			$(document).on( 'click', '.xhe-button', this.repeaterButton );
			$(document).on( 'click', '.xhe-icon', this.action );

			fieldSelect2();

			if( $('#repeater-button_quicktags-wrapper tbody > tr').length <= 0 ) {
				var rowId = $('[data-event="add-row"]').attr('data-repeater_id');
				XHESetting.addRow(rowId);
			}
		},

		repeaterButton: function(e) {
			e.preventDefault();

			var _this = $(this),
				event = _this.attr('data-event'),
				id = _this.attr('data-repeater_id');

			if( event == 'add-row' ) {
				XHESetting.addRow(id);
			}
		},

		addRow: function(id, insert = false) {
			var wrapper = $('#repeater-' + id + '-wrapper'),
				tpl = $('#tpl-repeater-' + id).html(),
				rowId = new Date().getTime(),
				totalRow = wrapper.find('tr').length;

			tpl = str_replace('[:order]', totalRow, tpl);
			tpl = str_replace('[:id]', rowId, tpl);
			tpl = str_replace('[:repeater_name]', id, tpl);

			if( insert ) {
				insert.after(tpl);
			}else {
				wrapper.find('tbody').append(tpl);
			}

			fieldSelect2();
		},

		action: function(e) {
			e.preventDefault();

			var _this = $(this),
				el = _this.closest('tbody');

			if( _this.hasClass('-minus') ) {
				_this.closest('tr').remove();
			}else {
				var id = _this.attr('data-repeater_id');
				XHESetting.addRow(id, _this.closest('tr'));
			}

			XHESetting.order( el );
		},

		order: function(el) {
			el.addClass('class_name');
			el.find('tr').each(function( index ) {
				$(this).find('.xhe-row-handle.order span').text(index + 1);
			});
		}
	}

	XHESetting.init();

})(jQuery);