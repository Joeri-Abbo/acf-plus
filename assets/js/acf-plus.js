(function($) {

    // Define the Rokit Flexible Content Modules scope
	var RFCM = {

		modals: [],
		postStatus: '',
		screen: '',

		init: function() {

			// Check if ACF is defined
            if(typeof acf === 'undefined') {
				console.error('ACF JavaScript library not found!');
				return false;
			}

			// Grab the post status from ACF localized data
			// We need this to define if post must be saved as draft or published
			$(document).ready(function() {
				if(acf.has('post_status')) {
					RFCM.postStatus = acf.get('post_status');
				}

				if(acf.has('post_label')) {
					RFCM.postLabel = acf.get('post_label');
				}

				if(acf.has('screen')) {
					RFCM.screen = acf.get('screen');
				}
			});

			// Add modal to current layouts
            acf.addAction('load_field/type=flexible_content', function(field) {
				field.$el.find('.acf-flexible-content:first > .values > .layout:not(.fc-modal)').each(function() {
					RFCM.addModal($(this));
				});
			});

			// Add modal to new layouts
            acf.addAction('after_duplicate', function($clone, $el) {
				if($el.is('.layout')){
                    RFCM.addModal($el);
                }
			});

			// Automatically open the new layout after append it, to improve usability
            acf.addAction('append', function($el) {
				if($el.is('.layout')) {
                    $el.find('> .acf-fc-layout-controls a.-pencil').trigger('click');
                }
			});

			// Point error messages inside FC
            acf.addAction('invalid_field', function(field) {
				// Add invalid field status to closed modalbox layout
				RFCM.invalidField(field.$el);
			});

			acf.add_filter('validation_complete', function( json, $form ){
				RFCM.validateEnd(json, $form);
				return json;
			});

			// Remove error messages
            acf.addAction('valid_field', function(field) {
				RFCM.validField(field.$el);
			});

			// Pressing ESC makes the modal to close

			$(document).keyup(function(e) {
				if(e.keyCode == 27 && $('body').hasClass('acf-modal-open')) {
                    RFCM.close();
                }
			});

		},

		validateEnd: function(json, $form) {

			if(json.errors.length > 0) {

				var $openModal 	= $(document).find('.fc-modal.-modal'),
					openModalErrors = 0;

				for(i=0; i<json.errors.length; i++) {
					if($openModal.find('[name="' + json.errors[i].input + '"]').length > 0) {
						openModalErrors++;
					}
				}
			}

			// Check if the open modalbox has error else close
			if(openModalErrors === 0) {
				RFCM.close();
			}

			// Remove the save state from the modalbox layout
			RFCM.removeModalSaveState();

		},

		getPageType: function() {

			if(RFCM.screen === 'options') {
				return acf.__('options');
			} else if(RFCM.postLabel !== 'undefined')  {
				return RFCM.postLabel;
			}

			return 'content';
		},

		addModal: function($layout) {

			$layout.addClass('fc-modal');
			$layout.removeClass('-collapsed');

			// Remove collapse button and click event
            $layout.find('> .acf-fc-layout-handle').off('click');
            $layout.find('> .acf-fc-layout-controls > a.-collapse').remove();

            // Open modalbox when cliked on handle bar
            $layout.find('> .acf-fc-layout-handle').on('click', RFCM.open);

			// Edit button
            var edit = $('<a class="acf-icon -pencil small light" href="#" data-event="edit-layout" title="' + acf.__('Edit layout') + '" />');
			edit.on('click', RFCM.open);
			$layout.find('> .acf-fc-layout-controls').append(edit);

			// Add modal elements
            $layout.prepend('<div class="acf-fc-modal-title" />');
			$layout.find('> .acf-fields, > .acf-table').wrapAll('<div class="acf-fc-modal-content" />');

			// Add modal buttons
			RFCM.addModalButtons($layout);

		},

		addModalButtons: function($layout) {

			// Check if this is post edit or oprions screen
			if(RFCM.screen === 'post' || RFCM.screen === 'options') {

				// Build modal footer buttons
				var $footerActions = $('<div class="acf-fc-modal-footer"><div>'),
					$buttons = $('<div class="acf-fc-modal-actions"><div>'),
					action = RFCM.isDraft() ? acf.__('Close & Save Draft') : acf.__('Close & Update'),
					$buttonSave = $('<a class="button button-primary button-large">' + action + '</a>'),
					$buttonClose = $('<a class="button button-secondary button-large">' + acf.__('Close') + '</a>');

				// Hook actions to the buttons
				$buttonClose.on('click', RFCM.close);
				$buttonSave.on('click', function(event) {
					RFCM.save();
				});

				// Append the buttons to the modules
				$buttons.append($buttonClose);
				$buttons.append($buttonSave);
				$footerActions.append($buttons);
				$layout.append($footerActions);

			}

		},

		addModalSaveState: function($layout) {

			// Check if this is post edit or oprions screen
			if(RFCM.screen === 'post' || RFCM.screen === 'options') {

				var $contentContainer = $('.acf-fc-modal-content'),
					$actions = $('.acf-fc-modal-actions'),
					$buttons = $actions.find('.button');

				$buttons.hide();
				$actions.append('<span class="spinner is-active"></span><span class="save-overlay-notice">' + acf.__('Saving') + ' ' + RFCM.getPageType() + '...</span>');
				$contentContainer.append('<div class="save-overlay" />');

			}

		},

		removeModalSaveState: function($layout) {

			// Check if this is post edit or oprions screen
			if(RFCM.screen === 'post' || RFCM.screen === 'options') {

				var $contentContainer = $('.acf-fc-modal-content'),
					$actions = $('.acf-fc-modal-actions'),
					$buttons = $actions.find('.button');

				$buttons.show();
				$contentContainer.find('.save-overlay').remove();
				$actions.find('.spinner').remove();
				$actions.find('.save-overlay-notice').remove();

			}

		},

		open: function() {

			var $layout = $(this).parents('.layout:first');

			var caption = $layout.find('> .acf-fc-layout-handle').html();
			var a = $('<a class="dashicons dashicons-no -cancel" />').on('click', RFCM.close);

			$layout.find('> .acf-fc-modal-title').html(caption).append(a);
			$layout.addClass('-modal');

			RFCM.modals.push($layout);

			RFCM.overlay(true);

		},

		close: function() {

			var $layout = RFCM.modals.pop();

			if($layout) {

				// Refresh layout title
				// var fc = $layout.parents('.acf-field-flexible-content:first');
				// fc = acf.getInstance(fc);
				// var field = fc.getField(fc.data.key);
				// field.closeLayout(field.$layout($layout.index()));

				// Close
				$layout.find('> .acf-fc-modal-title').html(' ');
				$layout.removeClass('-modal').css('visibility', '');

				RFCM.overlay(false);

			}

		},

		save: function() {

			var $publish = $(document).find('#publish'),
				$saveConcept = $(document).find('#save-post');

			// Add save state animation
			RFCM.addModalSaveState();

			// If post status is draft save draft else publish
			if(RFCM.screen === 'post' && RFCM.isDraft()) {
				$saveConcept.trigger('click');
			} else {
				$publish.trigger('click');
			}

			return false;
		},

		isDraft : function() {

			if($.inArray(RFCM.postStatus, ['draft', 'auto-draft']) !== -1) {
				return true;
			}

			return false;

		},

		overlay: function(show) {

			if(show === true && !$('body').hasClass('acf-modal-open')) {

				var overlay = $('<div id="acf-flexible-content-modal-overlay" />').on('click', RFCM.close);
				$('body').addClass('acf-modal-open').append(overlay);

			} else if(show === false && RFCM.modals.length == 0) {

				$('#acf-flexible-content-modal-overlay').remove();
				$('body').removeClass('acf-modal-open');

			}

			RFCM.refresh();

		},

		refresh: function() {

			$.each(RFCM.modals, function() {
				$(this).css('visibility', 'hidden').removeClass('-animate');
			});

			var index = RFCM.modals.length - 1;

			if(index in RFCM.modals) {
				RFCM.modals[index].css('visibility', 'visible').addClass('-animate');
			}

		},

		invalidField: function($el) {

			$el.parents('.layout').addClass('layout-error-messages');

		},

		validField: function($el) {

			$el.parents('.layout').each(function() {
				var $layout = $(this);
				if($layout.find('.acf-error').length == 0) {
					$layout.removeClass('layout-error-messages');
				}
			});

		}

	};

	RFCM.init();

})(jQuery);