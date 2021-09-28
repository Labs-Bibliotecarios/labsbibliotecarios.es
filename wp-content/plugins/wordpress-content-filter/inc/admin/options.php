<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}
$options = wcf_get_options();

?>

<div class="wrap wcf-admin-options">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php esc_html_e('General Settings', 'wcf') ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields('wcf_settings_fields'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[color_scheme]"><?php echo esc_html__( 'Color Scheme', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Color Scheme', 'wcf' );?></span></legend>
                            <?php
                            $colors = wcf_get_colors();
                            $colors = array('' => esc_html__('None')) + $colors;

                            $colors_options = array(
                                'type' => 'select',
                                'name' => 'wcf_settings_options[color_scheme]',
                                'value' => $options['color_scheme'],
                                'options' => $colors,
//                                'label' => esc_html__( 'Color Scheme', 'wcf' ),
                                'class' => '',
                                'desc' => esc_html__('Choose skin for the plugin (default is dark)', 'wcf'),
                            );

                            wcf_forms_field($colors_options);
                            ?>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[scroll_top]"><?php echo esc_html__( 'Scroll Top', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Scroll Top', 'wcf' );?></span></legend>
                            <?php

                            $scroll_options = array(
                                'type' => 'select',
                                'name' => 'wcf_settings_options[scroll_top]',
                                'value' => $options['scroll_top'],
                                'options' => array(
                                  'yes' => esc_html__('Yes', 'wcf'),
                                  'no' => esc_html__('No', 'wcf'),
                                ),
                                'class' => '',
                                'desc' => esc_html__('Scroll to top when the ajax done', 'wcf'),
                            );

                            wcf_forms_field($scroll_options);
                            ?>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[tooltip_appear]"><?php echo esc_html__( 'Show Tooltip', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Show Tooltip', 'wcf' );?></span></legend>
                            <?php

                            $scroll_options = array(
                                'type' => 'select',
                                'name' => 'wcf_settings_options[tooltip]',
                                'value' => $options['tooltip'],
                                'options' => array(
                                  'yes' => esc_html__('Yes', 'wcf'),
                                  'no' => esc_html__('No', 'wcf'),
                                ),
                                'class' => '',
                                'desc' => esc_html__('Show/Hide tooltip .', 'wcf'),
                            );

                            wcf_forms_field($scroll_options);
                            ?>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[ajax_loader]"><?php echo esc_html__( 'Ajax Loader', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Ajax Loader', 'wcf' );?></span></legend>

                            <div class="wcf-image-url-wrapper">
                                <input type="text" class="wcf-image-url" name="wcf_settings_options[ajax_loader]" id="wcf_settings_options_ajax_loader" value="<?php echo $options['ajax_loader'];?>"/>
                                <a href="#" class="button wcf-upload-image" data-field="wcf_settings_options_ajax_loader" title="<?php echo esc_html__('Add Media', 'wcf');?>"><span class="wp-media-buttons-icon"></span> <?php echo esc_html__('Upload image', 'wcf');?></a>
                            </div>
                            <span class="description"><?php echo esc_html__('Custom ajax loading image (.gif), leave blank for default', 'wcf');?></span>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[ajax_url]"><?php echo esc_html__( 'Ajax Url', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Ajax Url', 'wcf' );?></span></legend>
                            <?php

                            $ajax_url_options = array(
                                'type' => 'select',
                                'name' => 'wcf_settings_options[ajax_url]',
                                'value' => isset($options['ajax_url']) && $options['ajax_url'] != '' ? $options['ajax_url'] : 'yes',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'wcf'),
                                    'no' => esc_html__('No', 'wcf'),
                                ),
                                'class' => '',
                                'desc' => esc_html__('Generate the router url when using Ajax Filter', 'wcf'),
                            );

                            wcf_forms_field($ajax_url_options);
                            ?>

                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wcf_settings_options[custom_css]"><?php echo esc_html__( 'Custom CSS', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Custom CSS', 'wcf' );?></span></legend>

                            <?php

                            $scroll_options = array(
                                'type' => 'textarea',
                                'name' => 'wcf_settings_options[custom_css]',
                                'value' => esc_html($options['custom_css']),
                                'class' => '',
                                'desc' => esc_html__('Custom style for frontend', 'wcf'),
                            );

                            wcf_forms_field($scroll_options);
                            ?>

                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <p>
            <?php submit_button(); ?>
        </p>
    </form>

</div>
