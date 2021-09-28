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
$current_url = admin_url('/edit.php?') . $_SERVER['QUERY_STRING'];
?>

<div class="wrap wcf-import-page">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php esc_html_e('Import Search Form', 'wcf') ?></h2>
    <?php
    if (isset($_GET['wcf_action'])) { ?>
        <div class="updated">
            <p><?php echo esc_html__('Import form successfully', 'wcf');?></p>
        </div>
        <p><?php echo esc_html__('Done', 'wcf')?> <a href="<?php echo admin_url('/edit.php?post_type=zf-wcf');?>"><?php echo esc_html__('View Forms', 'wcf');?></a></p>
        <?php
        return;
    }
    ?>
    <p><?php echo esc_html__('Upload a WCF file to import the form into the plugin. Choose a .wcf file to upload, then click "Import".', 'wcf')?></p>

    <form method="post" action="<?php echo esc_url($current_url) . '&wcf_action=import';?>" enctype="multipart/form-data">

        <?php wp_nonce_field( 'wcf_import', 'wcf_import_nonce' ); ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="import_file"><?php echo esc_html__( 'Import', 'wcf' );?>: </label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php echo esc_html__( 'Import', 'wcf' );?></span></legend>
                            <input type="file" name="import_file" id="import_file" />
                            <br/>
                            <span class="description"><?php echo esc_html__('Upload a file to import search form')?></span>
                        </fieldset>
                    </td>
                </tr>


            </tbody>
        </table>
        <br>
        <p>
            <?php submit_button(_x( 'Import', 'button', 'wcf' ) ); ?>
        </p>
    </form>

</div>
