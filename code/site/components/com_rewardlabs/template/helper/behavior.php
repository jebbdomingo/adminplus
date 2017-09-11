<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsTemplateHelperBehavior extends ComKoowaTemplateHelperBehavior
{
    /**
     * Makes delete button action
     *
     * @param array $config
     * 
     * @return string
     */
    public function deletable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.cartItemDeleteAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();
                    
                    var id = $(this).data('id');

                    $('input[name=\"_action\"]').val('deleteitem');
                    $('input[name=\"item_id\"]').val(id);
                    $('form[name=\"cartForm\"]').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Makes add button action
     *
     * @param array $config
     * 
     * @return string
     */
    public function addable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'form'     => 'k-js-form-controller',
            'action'   => 'add',
            'selector' => '.k-js-cart-form--add',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    if ($('input[name=\"_action\"]').val()) {
                        $('input[name=\"_action\"]').val('{$config->action}');
                    } else {
                        $('form.{$config->form}').append('<input type=\"hidden\" name=\"_action\" value=\"{$config->action}\" />');
                    }

                    $('form.{$config->form}').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Makes update button action
     *
     * @param array $config
     * 
     * @return string
     */
    public function updatable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'form'     => 'k-js-form-controller',
            'action'   => 'updatecart',
            'selector' => '.k-js-cart-form--update',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    if ($('input[name=\"_action\"]').val()) {
                        $('input[name=\"_action\"]').val('{$config->action}');
                    } else {
                        $('form.{$config->form}').append('<input type=\"hidden\" name=\"_action\" value=\"{$config->action}\" />');
                    }

                    $('form.{$config->form}').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
