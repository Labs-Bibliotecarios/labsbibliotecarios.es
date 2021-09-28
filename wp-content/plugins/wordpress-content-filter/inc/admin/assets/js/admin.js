
(function ($) {
    "use strict";

    $(document).ready(function() {

        var WCF_Admin = {
            init_events_load : false,
            init : function() {
                this.build_sortable_draggable();
                this.init_element_media();
                this.init_color();
                this.init_events();
                this.init_settings_events();
            },

            //Build sortable event for fields list
            build_sortable_draggable : function() {

                $( '#wcf-forms-search-fields-list').sortable({
                    handle: '.menu-item-handle',
                    revert: true,
                    placeholder: "sortable-placeholder",
                    start: function( e, ui ) {
                        ui.helper.width( '100%');
                        ui.helper.height( 'auto');
                        ui.placeholder.width( '100%');
                        ui.placeholder.height( ui.helper.outerHeight());
                    },
                    stop: function( e,ui ) {

                        var item = ui.item;

                        if (item.hasClass('ui-draggable')) {

                            item.addClass('wcf-loading');
                            item.removeClass('ui-draggable');
                            item.removeClass('wcf-icon-menu');

                            var field_type = item.data( 'type' );
                            var menu_item_settings = $( '.menu-item-settings', item );

                            $.post( ajaxurl, {
                                field_type: field_type,
                                action:'wcf_field_settings_output',
                                wcf_ajax_nonce:wcf_forms_variables.wcf_ajax_nonce
                            }, function( response ) {
                                item.removeClass('wcf-loading');
                                menu_item_settings.prepend(response);
                                var field_id = menu_item_settings.find('.wcf-field-id').val();

                                menu_item_settings.slideDown( 'fast', function() {
                                    item.find('.wcf-item-edit').addClass('open');
                                });

                                WCF_Admin.init_item_events(item, menu_item_settings);
                            });
                        }

                    },

                });

                $( "#wcf-forms-available-field-list > li" ).draggable({
                    connectToSortable: "#wcf-forms-search-fields-list",
                    helper: "clone",
                    revert: "invalid",
                    zIndex: 100,

                    start: function( event, ui ) {
                        var clone = ui.helper;
                        var type = clone.data('type');
                        if (type == 'input_query' && WCF_Admin.check_item(type)) {
                            alert(wcf_forms_variables.input_query_exist);
                            return false;
                        } else if (type == 'submit_button' && WCF_Admin.check_item(type)) {
                            alert(wcf_forms_variables.submit_button_exist);
                            return false;
                        } else if (type == 'rating' && WCF_Admin.check_item(type)) {
                            alert(wcf_forms_variables.rating_exist);
                            return false;
                        }
                    },
                });

            },
            // init events for all items
            init_events : function () {

                $('#wcf-forms-search-fields-list li').each(function() {
                    var $this = $(this);
                    var menu_item_settings = $( '.menu-item-settings', $this );

                    WCF_Admin.init_item_events($this, menu_item_settings);
                });

                this.init_events_load = true;

            },

            init_settings_events: function() {

                var post_type_checkbox = $('.wcf_post_type_checkbox').first();
                var checkbox = post_type_checkbox.find('input[type="checkbox"]');

                $('input[name="wcf-settings[search_type]"]').on('change', function() {
                    if ($(this).val() == 'single') {
                        checkbox.prop('checked', false);
                        checkbox.first().prop('checked', true);
                    } else {

                    }
                });

                checkbox.on('click', function() {
                    var search_type = $('input[name="wcf-settings[search_type]"]:checked');
                    var count = post_type_checkbox.find('input[type="checkbox"]:checked').length;
                    if (search_type.val() == 'single') {
                        checkbox.prop('checked', false);
                        $(this).prop('checked', true);
                    } else {
                        if (count < 1) {
                            alert(wcf_forms_variables.at_least_one_post_type);
                            return false;
                        }
                    }
                });

            },
            // init events for each item
            init_item_events : function (parent, menu_item_settings) {
                var _this = this;
                var field_id = menu_item_settings.find('.wcf-field-id').val();
                var field_type = parent.data('type');
                var thickbox_body = $('#wcf-thickbox-body');

                menu_item_settings.find('.wcf-delete-field').on('click', function(e) {
                    e.preventDefault();
                    WCF_Admin.remove_field($(this));
                });

                menu_item_settings.find('.wcf-cancel-field').on('click', function(e) {
                    e.preventDefault();
                    menu_item_settings.slideUp( 'fast', function() {
                        parent.find('.wcf-item-edit').removeClass('open');
                    });
                });

                parent.find('.wcf-item-edit').unbind().on('click', function(e) {
                    e.preventDefault();
                    var $_this = $( this );
                    menu_item_settings.slideToggle( "fast", function() {
                        $_this.toggleClass('open');
                    });
                });

                // init generate meta key options
                if (field_type == 'meta_field') {
                    $('#wcf_forms_field_meta_key_' + field_id).on('change', function() {
                        $.post( ajaxurl, {
                            meta_key: $(this).val(),
                            field_id: field_id,
                            field_type: field_type,
                            action:'wcf_generate_meta_key_options',
                            wcf_ajax_nonce:wcf_forms_variables.wcf_ajax_nonce
                        }, function( response ) {

                            $('#wcf_forms_field_options_' + field_id).val(response);

                        } );
                    });
                } else if (field_type == 'taxonomy') {

                    var display_type = parent.find('#wcf_forms_field_display_type_' + field_id);
                    var default_value = parent.find('#wcf_forms_field_default_value_' + field_id);
                    var exclude_terms = parent.find('#wcf_forms_field_exclude_terms_' + field_id);
                    var taxonomy = parent.find('#wcf_forms_field_taxonomy_' + field_id);
                    var terms_color = $('#forms_field_wcf_forms_field_terms_color_' + field_id);

                    taxonomy.on('change', function() {
                        $.post( ajaxurl, {
                            taxonomy: $(this).val(),
                            field_id: field_id,
                            field_type: field_type,
                            field_name: 'wcf_forms_field_terms_color_' + field_id,
                            action:'wcf_terms_color',
                            wcf_ajax_nonce:wcf_forms_variables.wcf_ajax_nonce
                        }, function( response ) {

                            terms_color.find('.wcf-form-field-wrapper, table').remove();
                            terms_color.find('.wcf-field-label').after(response);
                            _this.init_color(terms_color);
                        } );
                    });
                    terms_color.find('.wcf-generate-terms-color-button').on('click', function(e) {
                        e.preventDefault();
                        taxonomy.trigger('change');
                    });

                    display_type.on('change', function(e) {
                        if ($(this).val() == 'color') {
                            terms_color.show();
                        } else {
                            terms_color.hide();
                        }
                    });

                    if (!_this.init_events_load) {
                        display_type.trigger('change');
                    }
                    //
                    parent.on('click', '.wcf-select-terms-button', function (e) {
                        e.preventDefault();
                        thickbox_body.html('Loading ...');
                        var select_type = $(this).data('type');

                        if (taxonomy.val() == '') {
                            thickbox_body.html(wcf_forms_variables.please_select_taxonomy);
                        } else {

                            var is_single = false;
                            if (display_type.val() == 'select' || display_type.val() == 'radio') {
                                is_single = true;
                            }

                            $.post( ajaxurl, {
                                field_id: field_id,
                                field_type: field_type,
                                select_type: select_type,
                                is_single: is_single,
                                taxonomy: taxonomy.val(),
                                action:'wcf_select_terms',
                                wcf_ajax_nonce:wcf_forms_variables.wcf_ajax_nonce
                            }, function( response ) {
                                thickbox_body.html(response);
                                $('.insert_term', thickbox_body.parent()).unbind().on('click', function(e) {
                                    e.preventDefault();

                                    var taxonomy_value = thickbox_body.find('#wcf-taxonomy-select').val();

                                    if (taxonomy_value.length > 0) {
                                        if (select_type == 'default') {
                                            if (is_single == false) {
                                                taxonomy_value = taxonomy_value.join("\n");
                                            }
                                            default_value.val(taxonomy_value);
                                        } else if (select_type == 'exclude') {
                                            taxonomy_value = taxonomy_value.join(",");
                                            exclude_terms.val(taxonomy_value);
                                        }

                                    }

                                    tb_remove();
                                });


                            } );

                        }

                   });
                }

            },

            init_color : function (ob) {

                var color_picker = $('.wcf-color-picker');
                if (ob != null) {
                    color_picker = $('.wcf-color-picker', ob);
                }
                color_picker.each(function () {
                    var $this = $(this);
                    $this.wpColorPicker();
                });
            },
            // init elements media for a object
            init_element_media : function () {
                var _this = this;
                $('.wcf-upload-image').on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var field_key = $this.attr('data-field');
                    _this.upload_media(field_key, $this.parent());
                });
            },

            upload_media : function (field_key, ob) {

                var media = wp.media({
                    title: 'Insert a media',
                    library: {type: 'image'},
                    multiple: false,
                    button: {text: 'Insert'}
                });

                media.on('select', function () {
                    var first = media.state().get('selection').first().toJSON();
                    $('#' + field_key, ob).val(first.url);
                });

                media.open();

                return false;

            },

            // remove a item
            remove_field : function (el) {
                var parent = el.closest('.wcf_forms_field_li');
                parent.animate({
                    opacity : 0,
                    height: 0
                }, 350, function() {
                    parent.remove();
                });
            },

            check_item : function(field_type) {
                if ($( '#wcf-forms-search-fields-list li[data-type="'+field_type+'"]').length) {
                    return true;
                } else {
                    return false;
                }
            }
        };

        WCF_Admin.init();

    });

})(jQuery);

