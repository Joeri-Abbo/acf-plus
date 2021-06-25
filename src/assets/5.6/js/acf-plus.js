jQuery(document).ready(function() {

    try {

        // Add Elements button
        var acf_fc_render = acf.fields.flexible_content.render;

        acf.fields.flexible_content.render = function() {
            acf_fc_modal_init();
            return acf_fc_render.apply(this, arguments);
        }

        // ACF FC Modal
        acf_fc_modal_init();

    } catch(e) {}

});

// Init Modal
function acf_fc_modal_init() {

    jQuery('.acf-flexible-content .layout:not(.acf-clone)').each(function() {
        var layout = jQuery(this);

        layout.addClass('fc-modal');

        // Remove Toggle button and click event
        layout.find('> .acf-fc-layout-handle').off('click');
        layout.find('> .acf-fc-layout-controlls > li:last-child').remove(); // ACF 5.4
        layout.find('> .acf-fc-layout-controlls > a:last-child').remove(); // ACF 5.5

        // Open modalbox when cliked on ACF handle bar
        layout.find('> .acf-fc-layout-handle').on('click', acf_fc_modal_open);

        // Edit button
        var controls = layout.find('> .acf-fc-layout-controlls');
        if(controls.is('ul'))
            controls.append('<li><a class="acf-icon -pencil small light" href="#" data-event="edit-layout" title="Edit layout"></a></li>');
        else
            controls.append('<a class="acf-icon -pencil small light" href="#" data-event="edit-layout" title="Edit layout"></a>');
        layout.find('> .acf-fc-layout-controlls a.-pencil').on('click', acf_fc_modal_open);

        // Add modal elements
        if(layout.find('> .acf-fc-modal-title').length == 0) {
            layout.prepend('<div class="acf-fc-modal-title"></div>');
            layout.find('> .acf-fields, > .acf-table').wrapAll('<div class="acf-fc-modal-content"></div>');
        }

    });

}

// Open Modal
function acf_fc_modal_open() {
    var layout = jQuery(this).parents('.layout');
    if(!layout.hasClass('-modal')) {
        layout.removeClass('-collapsed');
        var caption = layout.find('> .acf-fc-layout-handle').html();
        layout.find('.acf-fc-modal-title').html(caption + '<a class="acf-icon -cancel" href="javascript:acf_fc_modal_remove()">');
        layout.addClass('-modal');
        jQuery("body").append("<div id='acf-flexible-content-modal-overlay'></div>");
        jQuery('body').addClass('acf-modal-open');

        // Close the modal when clicking on the body net to modal
        jQuery("#acf-flexible-content-modal-overlay").click(acf_fc_modal_remove);

        // Close the modal when clicking on the esc button
        $(document).keyup(function (e) {
            if (e.keyCode === 27) {
                acf_fc_modal_remove();
            }
        });

    }
}

// Close Modal
function acf_fc_modal_remove() {
    jQuery('body').removeClass('acf-modal-open');
    jQuery('.acf-flexible-content .layout.-modal > .acf-fc-layout-handle').click(); // To reload layout title
    jQuery('.acf-flexible-content .layout').removeClass('-modal');
    jQuery("#acf-flexible-content-modal-overlay").remove();
}
