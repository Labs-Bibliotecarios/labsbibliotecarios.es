/**
 * jQuery WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 */

(function ($) {
    "use strict";


    function WCF_Form(element, options) {

        this.$el = $(element);

        this.options = $.extend({
            scroll_top: 'yes',
            tooltip: 'yes',
            ajax_loader: '<div class="wcf-ajax-loading"></div>',
            ajax_complete: '',
            ajax_url: 'yes',
            clear_class: '',
            clear_icon: '&times;'
        }, options);

        // Initialize the plugin instance
        this.init();

    }

    WCF_Form.prototype = {

        //
        // Initialize the plugin instance
        //
        init: function () {
            this.toggle_field();
            this.form_search();
        },

        toggle_field: function () {
            $('.wcf-arrow-field .wcf-label').on('click', function (e) {
                e.preventDefault();
                var $this = $(this);

                $this.parent().find('.wcf-field-body').slideToggle(300, function () {
                    $this.toggleClass('wcf-closed');
                });
            });
        },

        //init date range
        init_date: function (form) {

            $('.range_date_wrapper', form).each(function () {
                var $this = $(this);
                var format_date = $this.data('format-date');
                var dateFrom = $('.date_from', $this);
                var dateTo = $('.date_to', $this);
                if (dateFrom.length > 0) {

                    dateFrom.datepicker({
                        defaultDate: "+1w",
                        changeMonth: false,
                        showOtherMonths: true,
                        dateFormat: format_date,
                        onClose: function (selectDate) {
                            dateTo.datepicker("option", "minDate", selectDate);
                        },
                        showOn: 'button',
                        buttonText: '<span class="dashicons dashicons-calendar-alt"></span>',
                        beforeShow: function (el, ob) {
                            ob.dpDiv.addClass('wcf-date-picker-skin');
                        }
                    });

                    dateTo.datepicker({
                        defaultDate: "+1w",
                        changeMonth: false,
                        showOtherMonths: true,
                        dateFormat: format_date,
                        onClose: function (selectDate) {
                            dateFrom.datepicker("option", "maxDate", selectDate);
                        },
                        showOn: 'button',
                        buttonText: '<span class="dashicons dashicons-calendar-alt"></span>',
                    });

                }
            });
        },

        // init range slider
        init_range_slider: function (form, auto_filter) {

            var _this = this;

            var slider_range = $(".slider-range", form);
            if (slider_range.length > 0) {

                slider_range.each(function () {
                    var $this = $(this),
                        parent = $this.parent(),
                        step = $this.data('step'),
                        emin = $(".range_min", parent),
                        emax = $(".range_max", parent),
                        spanFrom = $("span.range_from", parent.parent()),
                        spanTo = $("span.range_to", parent.parent()),
                        min = emin.data('min'),
                        max = emax.data('max'),
                        currentMin = parseInt(emin.val()),
                        currentMax = parseInt(emax.val());

                    $this.slider({
                        range: true,
                        step: step,
                        min: min,
                        max: max,
                        values: [currentMin, currentMax],
                        slide: function (event, ui) {
                            spanFrom.html(ui.values[0]);
                            spanTo.html(ui.values[1]);
                        },
                        stop: function (event, ui) {

                            emin.val(ui.values[0]);
                            emax.val(ui.values[1]);
                            _this.form_submit(form, auto_filter);

                        },
                        change: function (event, ui) {

                        }
                    });
                });
            }

        },

        init_checkbox: function (form, auto_filter) {

            var _this = this;

            $('.wcf-checkbox-all', form).on('click', function (e) {
                var $this = $(this),
                    parent = $this.closest('.wcf-field-checkbox');
                if ($this.is(':checked')) {
                    parent.find('.wcf-checkbox-item').prop('checked', true);
                } else {
                    parent.find('.wcf-checkbox-item').prop('checked', false);
                }

                _this.form_submit(form, auto_filter);

            });

            $('.wcf-field-checkbox, .wcf-field-taxonomy_checkbox, .wcf-field-checkbox_color', form).each(function () {
                var parent = $(this),
                    all_item = $('.wcf-checkbox-all', parent),
                    item = $('.wcf-checkbox-item', parent),
                    count_item = item.length;
                all_item.on('click', function (e) {

                    var $this = $(this);
                    if ($this.is(':checked')) {
                        item.prop('checked', true);
                    } else {
                        item.prop('checked', false);
                    }

                    _this.form_submit(form, auto_filter);

                });

                item.on('click', function () {

                    if (parent.find('.wcf-checkbox-item:checked').length == count_item) {
                        all_item.prop('checked', true);
                    } else {
                        all_item.prop('checked', false);
                    }

                    _this.form_submit(form, auto_filter);
                });
            })
        },

        reset: function (form, auto_filter) {
            var _this = this;
            form.find('.wcf-reset-button').on('click', function () {

                form.find('[data-reset]').each(function () {
                    var $this = $(this),
                        type = $this.data('type'),
                        reset = $this.data('reset');

                    if (type == 'rating') {
                        $this.find('input').attr('checked', false);
                        $this.find('input[value="' + reset + '"]').attr('checked', true);
                    } else if (type == 'range_slider' || type == 'price') {
                        var min = $this.find('.range_min');
                        var max = $this.find('.range_max');
                        var min_val = parseInt(min.data('min'));
                        var max_val = parseInt(max.data('max'));
                        $this.find('.slider-range').slider("option", "values", [min_val, max_val]);
                        min.val(min_val);
                        max.val(max_val);
                        $this.find('.range_from').text(min_val);
                        $this.find('.range_to').text(max_val);
                    } else if (type == 'taxonomy' || type == 'meta_field' || type == 'acf') {
                        var display_type = $this.data('display');
                        if (display_type == 'checkbox' || display_type == 'color' || display_type == 'multiselect') {
                            var arr_reset = reset.split('|');

                            $this.find('option').attr('selected', false);
                            $this.find('input[type="checkbox"]').attr('checked', false);

                            $.each(arr_reset, function (index, value) {
                                if (display_type == 'multiselect') {
                                    $this.find('option[value="' + value + '"]').attr('selected', true);
                                } else {
                                    $this.find('input[value="' + value + '"]').attr('checked', true);
                                }
                            });
                        } else if (display_type == 'radio') {
                            $this.find('input[value="' + reset + '"]').attr('checked', true);
                        } else if (display_type == 'select') {
                            $this.find('select').val(reset);
                        } else if (display_type == 'text') {
                            $this.find('input[type="text"]').val(reset);
                        } else if (display_type == 'textarea') {
                            $this.find('input[type="textarea"]').val(reset);
                        }

                    } else if (type == 'date') {
                        $this.find('input[type="text"]').val(reset);
                    } else if (type == 'author' || type == 'sort') {
                        $this.find('select').val(reset);
                    } else if (type == 'input_query') {
                        $this.val(reset);
                    }

                });

                _this.form_submit(form, auto_filter);
            });

        },

        form_search: function () {

            var _this = this;
            var $form = this.$el;
            var form_id = $form.data('form');
            var result_layout = $('#wcf-form-wrapper-' + form_id);
            var result_layout_length = result_layout.length;
            var auto = $form.data('auto');
            var pathname = window.location.pathname;
            var enable_ajax = $form.data('ajax');
            var sort_value = '';
            var template_loop = result_layout.data('loop');
            var columns = result_layout.data('columns');
            var gridtype = result_layout.data('gridtype');

            if (result_layout_length) {
                if (enable_ajax == 'ajax') {
                    _this.pagination_link(result_layout, form_id, template_loop, columns, gridtype, sort_value);
                }
            }

            if (auto == 'yes') {
                //default bind change event for some input type
                $('input[type="text"], input[type="radio"], select, textarea', $form).on('change', function () {
                    $form.submit();
                });
            }

            // process the form
            $form.on('submit', function (event) {

                if (result_layout_length == 0) {
                    // Not found result page then redirect to search.php
                } else {

                    if (enable_ajax == 'ajax') {
                        // stop the form from submitting the normal way and refreshing the page
                        event.preventDefault();


                        var form_data = $form.serialize();
                        if (_this.options.ajax_url == 'yes') {
                            window.history.pushState('', document.title, pathname + '?' + form_data);
                        }
                        //document.location.search = form_data;
                        result_layout.append(_this.options.ajax_loader);
                        if (_this.options.scroll_top == 'yes') {
                            _this.scroll_top(result_layout);
                        }
                        $.post(wcf_variables.ajax_url, {
                            action: 'wcf_search_ajax',
                            wcf_ajax_nonce: wcf_variables.wcf_ajax_nonce,
                            pathname: pathname,
                            search: form_data,
                            form_id: form_id,
                            loop: template_loop,
                            result_columns: columns,
                            gridtype: gridtype,
                            sort_result: sort_value,
                        }, function (response) {
                            _this.response_callback(response, result_layout, form_id, template_loop, columns, gridtype, sort_value);

                        });

                    } else {
                        $form.attr('action', '');
                    }

                }

            });

            // init form fields
            _this.init_date($form);
            _this.init_checkbox($form, auto);
            _this.init_range_slider($form, auto);
            _this.reset($form, auto);
            _this.clear_query($form);
            _this.tooltip($form);
            _this.toggle_searchform($form);
            _this.ini_sorttable(result_layout);
            _this.results_sort(result_layout, form_id, template_loop, columns, gridtype);
            _this.init_masonry(result_layout, columns);

        },

        toggle_searchform: function (form) {

            form.parent().find('.wcf-toggle-searchform').on('click', function (e) {
                e.preventDefault();
                var $this = $(this);
                $('.wcf-off-menu-push').toggleClass('wcf-off-menu-push-right');
                $('.wcf-off-menu').toggleClass('wcf-off-menu-open');
            });

            form.find('.wcf-off-menu-toggle').on('click', function (e) {
                e.preventDefault();
                var $this = $(this);
                $('.wcf-off-menu-push').toggleClass('wcf-off-menu-push-right');
                $('.wcf-off-menu').toggleClass('wcf-off-menu-open');
            });
        },
        tooltip: function (form) {
            var _this = this;
            if (_this.options.tooltip == 'yes') {
                form.find('[data-tooltip]').each(function () {
                    var $this = $(this);
                    var intro = $this.data('tooltip');
                    $this.removeAttr('data-tooltip');
                    if (intro != '') {
                        $this.wrapInner('<span data-tooltip="' + intro + '"></span>');
                    }
                });
            } else {
                form.find('[data-tooltip]').removeAttr('data-tooltip');
            }
        },
        clear_query: function (form) {

            var _this = this,
                $this = form.find('input[name="s"]'),
                clear_button;

            if ($this.length == 0) {
                return;
            }
            if (!$this.parent().hasClass('wcf-clear-query-wrapper')) {
                $this.wrap('<div class="wcf-clear-query-wrapper"></div>');
                $this.after('<a class="wcf-clear-query ' + _this.options.clear_class + '">' + _this.options.clear_icon + '</a>');
            }

            clear_button = $this.next();

            function show_button() {
                if ($this.val().replace(/^\s+|\s+$/g, '').length > 0) {
                    clear_button.show();
                } else {
                    clear_button.hide();
                }
                clear_button.css({
                    top: $this.outerHeight() / 2 - clear_button.height() / 2,
                });
            }

            clear_button.on('click', function (e) {
                e.preventDefault();
                $this.val('');
                show_button();
                $this.focus();
            });

            $this.on('keyup keydown change focus', show_button);
            show_button();
        },
        // use
        ajax_call: function (wrapper, form_id, template_loop, columns) {
            wrapper.trigger("wcf_form_search_ajax_done", [wrapper, form_id, template_loop, columns]);
        },

        form_submit: function (form, auto_filter) {

            if (auto_filter == 'yes') {
                form.submit();
            }
        },
        results_sort: function (wrapper, form_id, template_loop, columns, gridtype) {

            var _this = this;
            var sort_value = '';
            $('#wcf-results-sort', wrapper).unbind().on('change', function (e) {

                sort_value = $(this).val();
                var page_number = $(this).data('page');
                var link = $(this).closest('form').attr('action');
                var link_info = document.createElement("a");
                link_info.href = link;

                if (_this.options.ajax_url == 'yes') {
                    window.history.pushState('', document.title, link_info.pathname + link_info.search);
                }
                wrapper.append(_this.options.ajax_loader);

                var pathname = link_info.pathname;
                var search = link_info.search;
                if (search != '') {
                    if (search.substring(0, 1) == "?") {
                        search = search.replace('?', '');
                    }
                }
                if (_this.options.scroll_top == 'yes') {
                    _this.scroll_top(wrapper);
                }
                $.post(wcf_variables.ajax_url, {
                    action: 'wcf_search_ajax',
                    wcf_ajax_nonce: wcf_variables.wcf_ajax_nonce,
                    pathname: pathname,
                    search: search,
                    page_number: page_number,
                    form_id: form_id,
                    loop: template_loop,
                    result_columns: columns,
                    gridtype: gridtype,
                    sort_result: sort_value,
                }, function (response) {
                    _this.response_callback(response, wrapper, form_id, template_loop, columns, gridtype, sort_value);
                });
            });
        },
        response_callback: function(response, wrapper, form_id, template_loop, columns, gridtype, sort_value) {
            var _this = this;

            wrapper.animate(
                {opacity: 0},
                0,
                function () {
                    wrapper.html(response);
                    _this.init_masonry(wrapper, columns);
                    wrapper.animate(
                        {opacity: 1},
                        300, function () {

                            _this.pagination_link(wrapper, form_id, template_loop, columns, gridtype, sort_value);
                            _this.results_sort(wrapper, form_id, template_loop, columns, gridtype);
                            _this.ini_sorttable(wrapper);
                            _this.ajax_call(wrapper, form_id, template_loop, columns, gridtype);
                            if ($.isFunction(_this.options.ajax_complete)) {
                                _this.options.ajax_complete.call(_this);
                            }

                        }
                    );
                }
            );
        },
        init_masonry: function(result_layout, column) {
            var masonry = result_layout.data('masonry');
            if (masonry == 'yes' && $.fn.masonry) {
                result_layout.find('.wcf-items-results').masonry({
                    itemSelector: ".wcf-column-" + column
                });
            }
        },
        pagination_link: function (wrapper, form_id, template_loop, columns, gridtype, sort_value) {
            var _this = this;
            $('.wcf-pagination a', wrapper).unbind().on('click', function (e) {

                e.preventDefault();

                var page_number = $(this).data('page');
                var link = $(this).attr('href');
                var link_info = document.createElement("a");
                link_info.href = link;

                if (_this.options.ajax_url == 'yes') {
                    window.history.pushState('', document.title, link_info.pathname + link_info.search);
                }
                wrapper.append(_this.options.ajax_loader);

                var pathname = window.location.pathname;
                var search = window.location.search;
                if (search != '') {
                    if (search.substring(0, 1) == "?") {
                        search = search.replace('?', '');
                    }
                }
                if (_this.options.scroll_top == 'yes') {
                    _this.scroll_top(wrapper);
                }
                $.post(wcf_variables.ajax_url, {
                    action: 'wcf_search_ajax',
                    wcf_ajax_nonce: wcf_variables.wcf_ajax_nonce,
                    pathname: pathname,
                    search: search,
                    page_number: page_number,
                    form_id: form_id,
                    loop: template_loop,
                    result_columns: columns,
                    gridtype: gridtype,
                    sort_result: sort_value,
                }, function (response) {
                    _this.response_callback(response, wrapper, form_id, template_loop, columns, gridtype, sort_value);
                });

            });
        },

        scroll_top: function (result_layout) {
            $("html, body").animate({scrollTop: result_layout.offset().top - 100}, 200);
        },
        ini_sorttable: function (wrapper) {
            $('#wcf-table-pager', wrapper).empty();
            $('.wcftablesorter', wrapper).wcftablesorter();
            $('.wcftablepager', wrapper).wcftablepager();
        },

    };


    var namespace = ".wcf.jquery.tabler";

    var WCFTable = function (element, options) {
        this.element = $(element);
        this.origin = this.element.clone();
        this.options = $.extend(true, {}, WCFTable.defaults, this.element.data(), options);

        // todo: implement cache
    };

    $.fn.wcftablepager = function () {

        function the_pager() {
            var table = this;
            var tbody = table.tBodies[0];
            var $pager;
            var count;
            var pagesize;
            var numpages;
            var page;

            /* Get the table rows that are to be paged.
             * The class .wcftablepager-initallyhidden is added to rows during initialization. */
            function rows() {
                var $rows = $(tbody).find('tr:not(.wcftablepager-initiallyhidden)');
                return $rows;
            }

            /* Sets the visibility of rows.
             * Hidden rows will be hidden by adding the clas .wcftablepager-hide. */
            function set_visibility() {
                var $rows = rows();
                var min = (page - 1) * pagesize;
                var max = page * pagesize;
                $rows.each(function (idx) {
                    if (idx >= min && idx < max)
                        $(this).removeClass('wcftablepager-hide');
                    else
                        $(this).addClass('wcftablepager-hide');
                });
            }

            /* Jump to new page. */
            function set_page(newpage) {
                page = newpage;
                if (page < 1) page = 1;
                if (page > numpages) page = numpages;
                $pager.find('.wcftablepager-display').val(page + "/" + numpages);
                set_visibility();
            }

            /* Recalculate number of elements. */
            function set_elements() {
                count = rows().length;
                numpages = Math.ceil(count / pagesize) || 1;
                set_page(page);
                /* Recalculate page number limits and visibility. */
            }

            /* Sets page size. */
            function set_pagesize(newpagesize) {
                var currpos = ((page || 1) - 1) * (pagesize || 1);
                pagesize = newpagesize;
                numpages = Math.ceil(count / pagesize) || 1;
                set_page(Math.floor(currpos / pagesize) + 1);
                /* Try to jump to the page so the same elements are shown. */
            }

            /* Creates pager widget from HTML template, adds event handlers. */
            function create_pager() {
                var pagerhtml =
                    '<div class="wcftablepager-pager">' +
                    '<button type="button" class="wcftablepager-first"></button>' +
                    '<button type="button" class="wcftablepager-prev"></button>' +
                    '<input type="text" class="wcftablepager-display" readonly>' +
                    '<button type="button" class="wcftablepager-next"></button>' +
                    '<button type="button" class="wcftablepager-last"></button>' +
                    '<select class="wcftablepager-pagesize">' +
                    '<option selected value="10">×10</option>' +
                    '<option value="20">×20</option>' +
                    '<option value="50">×50</option>' +
                    '<option value="999999">All</option>' +
                    '</select>' +
                    '</div>';
                var templateid = $(table).attr('data-pager-template-id') || "";
                if (templateid != "")
                    $pager = $($('#' + templateid).html());
                else
                    $pager = $(pagerhtml);
                var containerid = $(table).attr('data-pager-id') || "";
                if (containerid != "")
                    $pager.appendTo($('#' + containerid));
                else
                    $pager.insertBefore(table);

                $pager.find('.wcftablepager-first').on('click', function () {
                    set_page(1);
                });
                $pager.find('.wcftablepager-prev').on('click', function () {
                    set_page(page - 1);
                });
                $pager.find('.wcftablepager-next').on('click', function () {
                    set_page(page + 1);
                });
                $pager.find('.wcftablepager-last').on('click', function () {
                    set_page(numpages);
                });
                $pager.find('.wcftablepager-pagesize').on('change', function () {
                    set_pagesize(+this.value || 10);
                });
                $(table).on('wcftablesorter-sorted', set_visibility);
                $(table).on('wcftablepager-elements', set_elements);
            }

            /* Initializes pager. */
            function initialize_pager() {
                $(tbody).find('tr:not(:visible)').addClass('wcftablepager-initiallyhidden');
                count = rows().length;
                set_pagesize(+$pager.find('.wcftablepager-pagesize').val() || 10);
            }

            /* inicializálás */
            create_pager();
            initialize_pager();
        }

        return this.each(the_pager);
    };

    $.fn.wcftablesorter = function () {
        function the_sorter() {
            var table = this;

            /* Sort all table bodies of the table. */
            function sort(columnidx, colspan, descending) {

                /* Gets a table row, and determines the sort text.
                 * Takes the column index and column span into consideration. */
                function gettext(row) {
                    var text = "";
                    for (var i = columnidx; i < columnidx + colspan; ++i) {
                        var cell = row.cells[i];
                        if (cell)
                            text += cell.textContent;
                    }
                    return text.trim();
                }

                /* Comparator function for two table rows.
                 * Empty strings compare as "small". If both strings contain
                 * numbers, they are sorted as numbers. Otherwise localeCompare is used. */
                function cmp(rowtext1, rowtext2) {
                    var str1 = rowtext1.text, str2 = rowtext2.text;
                    if (str1 == "")
                        return +1;
                    if (str2 == "")
                        return -1;
                    var num1 = +str1, num2 = +str2;
                    var res = isNaN(num1) || isNaN(num2) ? str1.localeCompare(str2) : num1 - num2;
                    return descending ? -res : res;
                }

                /* Sort all table bodies. Determine the text for the table rows only
                 * once before sorting, create a {row, text} object for each row. */
                $.each(table.tBodies, function () {
                    var $tbody = $(this);
                    var rowstexts = $.map($tbody.find('tr'), function (tr) {
                        return {'tr': tr, 'text': gettext(tr)};
                    });
                    rowstexts.sort(cmp);
                    var rows = $.map(rowstexts, function (rowtext) {
                        return rowtext.tr;
                    });
                    $(rows).appendTo($tbody);
                });
            }

            /* Handle click on a table column.
             * Arguments: columidx is the logical column index calculated by activate(),
             * and colspan is its column span to determine the text to be sorted in each
             * table cell. */
            function th_click(columnidx, colspan) {
                var $th = $(this);

                /* If this was the sorted column, with ascending sort, now switch to descending.
                 * All other columns become unsorted, and this one becomes sorted. */
                var descending = $th.hasClass('wcftablesorter-asc');
                $th.siblings('.wcftablesorter-header').removeClass('wcftablesorter-asc wcftablesorter-desc').addClass('wcftablesorter-unsorted');
                $th.removeClass('wcftablesorter-unsorted wcftablesorter-asc wcftablesorter-desc').addClass(descending ? 'wcftablesorter-desc' : 'wcftablesorter-asc');

                /* Wait a while, until the UI uodates, then do the sort. */
                $th.addClass('wcftablesorter-sorting');
                setTimeout(function () {
                    sort(columnidx, colspan, descending);
                    $th.removeClass('wcftablesorter-sorting');
                    $(table).trigger('wcftablesorter-sorted');
                }, 50);
            }

            /* Add click handlers to table headers, which will do the sorting.
             * Also, calculate the column index of the headers, as it might not be equal to the
             * index of the element in the DOM tree. If a header has a colspan greater than 1,
             * it counts as multiple columns. This index is used to determine the column index when the
             * header is clicked, eg.
             *   <thead>
             *     <th>  <th colspan="3">  <th id="this-is-clicked">
             *   <tbody>
             *     <td>  <td> <td> <td>    <td id="this-is-sorted">
             */
            function activate() {
                var columnidx = 0;
                $.each(table.tHead.rows[0].cells, function () {
                    var $th = $(this);
                    var colspan = +$th.attr('colspan') || 1;
                    if ($th.attr('data-wcftablesorter') != 'false') {
                        $th.attr('tabindex', 0);
                        $th.addClass('wcftablesorter-header wcftablesorter-unsorted');
                        $th.on('click', th_click.bind(this, columnidx, colspan));
                    }
                    columnidx += colspan;
                });
                $(table.tBodies).attr('aria-live', 'polite');
            }

            activate();
        }

        return this.each(the_sorter);
    }

    $.fn.WCFilter = function (options) {
        return this.each(function () {

            if (!$.data(this, 'plugin_wcfilter')) {
                $.data(this, 'plugin_wcfilter', new WCF_Form(this, options));
            }
        });
    };


})(jQuery);