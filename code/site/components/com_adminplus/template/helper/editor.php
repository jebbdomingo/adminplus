<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusTemplateHelperEditor extends ComKoowaTemplateHelperEditor
{
    public function display($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name' => 'description',
            'options' => [
                'readOnly' => false,
                'removePlugins' => false,
                'uploadUrl' => (string)$this->getTemplate()->route('option=com_textman&view=file&format=json&routed=1&plupload=1&container=textman-files', false, false),
                'imageUploadUrl' => (string)$this->getTemplate()->route('option=com_textman&view=file&format=json&routed=1&plupload=1&container=textman-images', false, false)
            ]
        ))->append([
            'buttons' => !$config->options->readOnly
        ]);

        // Editor
        $html = $this->getObject('com:ckeditor.controller.editor')->render($config);

        $html .= $this->_setupDragAndDrop($config);

        // Buttons
        if ($config->buttons) {
            $html .= $this->buttons();
        }

        return $html;
    }

    protected function _setupDragAndDrop($config)
    {
        return '<script>
            kQuery(document).ready(function($) {
                
                CKEDITOR.on("instanceReady", function(e) {
                    var editor = e.editor;

                    editor.on("fileUploadRequest", function(evt) {
                        var fileLoader = evt.data.fileLoader,
                            formData = new FormData(),
                            xhr = fileLoader.xhr;


                        formData.append("file", fileLoader.file, fileLoader.fileName);
                        formData.append("csrf_token", "'.$this->getObject('user')->getSession()->getToken().'");
                        formData.append("overwrite", "1");

                        xhr.send(formData);

                        // Prevent the default behavior.
                        evt.cancel();
                    });

                    editor.on( "fileUploadResponse", function( evt ) {
                        // Get XHR and response.
                        var data = evt.data,
                            xhr = data.fileLoader.xhr,
                            response = JSON.parse(xhr.response);

                        if ( !response.uploaded ) {
                            // An error occurred during upload.
                            data.message = response.error;
                        }
                    } );
                });
            });
        </script>';
    }

    public function buttons()
    {
        $buttons  = $this->_getButtons('introtext');
        $excluded = $this->_excludedButtons();

        $buttons = array_filter($buttons, function($button) use($excluded) {
            if (in_array($button->text, $excluded)) {
                return false;
            } else {
                return $button;
            }
        });

        // Add editor styles and scripts in JDocument to page when rendering
        $this->getIdentifier('com:koowa.view.page.html')->getConfig()->append(['template_filters' => ['document']]);

        return JLayoutHelper::render('joomla.editors.buttons', $buttons);
    }

    protected function _getButtons($editor, $buttons = true)
    {
        $result = array();

        if (is_bool($buttons) && !$buttons)
        {
            return $result;
        }

        $dispatcher = JEventDispatcher::getInstance();
        $editor_plugin = new PlgEditorJoomlatools($dispatcher, new JRegistry);

        // Get plugins
        $plugins = JPluginHelper::getPlugin('editors-xtd');

        foreach ($plugins as $plugin)
        {
            if (is_array($buttons) && in_array($plugin->name, $buttons))
            {
                continue;
            }

            JPluginHelper::importPlugin('editors-xtd', $plugin->name, false);
            $className = 'PlgEditorsXtd' . $plugin->name;

            if (!class_exists($className))
            {
                $className = 'PlgButton' . $plugin->name;
            }

            if (class_exists($className))
            {
                $plugin = new $className($editor_plugin, (array) $plugin);
            }

            // Try to authenticate
            if (!method_exists($plugin, 'onDisplay'))
            {
                continue;
            }

            $button = $plugin->onDisplay($editor, null, null);

            if (empty($button))
            {
                continue;
            }

            if (is_array($button))
            {
                $result = array_merge($result, $button);
                continue;
            }

            $result[] = $button;
        }

        return $result;
    }

    protected function _excludedButtons()
    {
        $excluded = array('Module');

        // Remove the Article editor button if logman and its linker button plugin is installed and enabled
        $isLogmanInstalled = JComponentHelper::isInstalled('com_logman');
        $isLogmanEnabled   = JComponentHelper::isEnabled('com_logman');
        $isLinkerEnabled   = JPluginHelper::isEnabled('editors-xtd', 'logmanlinker');

        if (($isLogmanInstalled && $isLogmanEnabled) && $isLinkerEnabled) {
            $excluded[] = 'Article';
        }

        // Remove the Image editor button if fileman is installed and its button plugin is enabled
        $isFilemanInstalled     = JComponentHelper::isInstalled('com_fileman');
        $isFilemanEnabled       = JComponentHelper::isEnabled('com_fileman');
        $isFilemanButtonEnabled = JPluginHelper::isEnabled('editors-xtd', 'filelink');
        
        if (($isFilemanInstalled && $isFilemanEnabled) && $isFilemanButtonEnabled) {
            $excluded[] = 'Image';
        }

        return $excluded;
    }
}

class PlgEditorJoomlatools extends JPlugin
{
    public function __call($method, $args)
    {
        $event = 'on'.ucfirst($method);

        if (method_exists($this, $event))
        {
            return call_user_func_array(array($this, $event), $args);
        } else {
            return null;
        }
    }

    public function onGetContent($id)
    {
        return 'CKEDITOR.instances[' . json_encode($id) . '].getData();';
    }

    public function onSetContent($id, $html)
    {
        return 'CKEDITOR.instances[' . json_encode($id) . '].setData(' . json_encode($html) . ');';
    }
}

