<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\Options as Options;
use WeTransfer\Core\Shortcode as Shortcode;
use WPCF7_TagGenerator;

/**
 * Contact Form 7
 */
class CF7 {
    /**
     * Test if plugin is actuve
     * @return boolean
     */
    public static function isActive() {
        if ((!is_plugin_active('contact-form-7/wp-contact-form-7.php') && !file_exists(WPMU_PLUGIN_DIR . '/' . 'contact-form-7/wp-contact-form-7.php'))) {
            return false;
        }

        return true;
    }

    /**
     * Register form tag to display WeTransfer uploader
     */
    public static function register() {
        add_action('wpcf7_init', function() {
            self::addFormTags();
            self::validateFormTags();
            self::registerFormTags();
        });
    }

    public static function defineLocalizedVariables() {
        add_action('wp_enqueue_scripts', function() {
            // Define available javascript variables
            $variables = [
                'transferCompleteMessage' => Options::getCf7TransferComplete(),
                'transferCompleteShowUrl' => Options::getCf7ShowUrl()
            ];
            wp_localize_script(Plugin::getSlug() . '-js', Plugin::getShortname() . '_cf7', $variables);
        });
    }

    /**
     * Get Form Tags
     */
    public static function getFormTags() {
        return [
            'wetransfer',
            'wetransfer*'
        ];
    }

    /**
     * Register Form Tags
     */
    public static function addFormTags() {
        wpcf7_add_form_tag(
            self::getFormTags(),
            function($tag) {
                // CF7
                if (empty($tag->name)) {
                    return '';
                }

                $validation_error = wpcf7_get_validation_error($tag->name);

                $class = wpcf7_form_controls_class($tag->type);

                if ($validation_error) {
                    $class .= ' wpcf7-not-valid';
                }

                $atts = [];

                $atts['class'] = $tag->get_class_option($class);
                $atts['id'] = $tag->get_id_option();
                $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

                if ($tag->is_required()) {
                    $atts['aria-required'] = 'true';
                }

                $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

                $value = (string) reset($tag->values);

                $value = $tag->get_default_option($value);

                $value = wpcf7_get_hangover($tag->name, $value);

                $atts['value'] = $value;
                $atts['type'] = 'hidden';
                $atts['name'] = $tag->name;
                $atts['id'] = $tag->name;

                $atts = wpcf7_format_atts($atts);

                $html = sprintf(
                    '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
                    sanitize_html_class($tag->name), $atts, $validation_error
                );

                // WeTransfer
                Shortcode::registerFooterScript();

                $wetransfer_html = file_get_contents(dirname(__DIR__) . '/Templates/Master.php');
                $wetransfer_html = str_replace('<div class="indi-wetransfer" type="wordpress">', '<div class="indi-wetransfer" type="cf7" for="'.$tag->name.'">', $wetransfer_html);

                return $wetransfer_html . $html;
            },
            [
                'name-attr' => true,
                'success-message-attr' => true,
                'display-hidden' => true
            ]
        );
    }

    /**
     * Validate Form Tags
     */
    public static function validateFormTags() {
        foreach (self::getFormTags() as $formTag) {
            add_filter('wpcf7_validate_' . $formTag, function($result, $tag) {
                $name = $tag->name;

                $value = isset( $_POST[$name] )
                    ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
                    : '';

                if ('wetransfer' == $tag->basetype) {
                    if ($tag->is_required() && '' == $value) {
                        $result->invalidate($tag, 'Please transfer your files before submitting this form');
                    }
                }

                return $result;
            }, 10, 2);
        }
    }

    /**
     * Register Form Tags In CF7 Admin UI
     */
    public static function registerFormTags() {
        add_action('wpcf7_admin_init', function() {
            $tag_generator = WPCF7_TagGenerator::get_instance();

            $tag_generator->add('wetransfer', __('wetransfer', 'contact-form-7'), function($contact_form, $args = '') {

                $args = wp_parse_args($args, []);
                $type = $args['id'];
                $description = __('Generate a form-tag for a single WeTransfer upload field.');

                ?>

                <div class="control-box">
                    <fieldset>
                        <legend><?php echo sprintf(esc_html($description)); ?></legend>

                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></legend>
                                            <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'contact-form-7')); ?></label>
                                        </fieldset>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'contact-form-7')); ?></label></th>
                                    <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </div>

                <div class="insert-box">
                    <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
                    <div class="submitbox">
                        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
                    </div>

                    <br class="clear" />
                    <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf(esc_html(__('To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.', 'contact-form-7' )), '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" /></label></p>
                </div>

                <?php
            });
        }, 15);
    }
}
