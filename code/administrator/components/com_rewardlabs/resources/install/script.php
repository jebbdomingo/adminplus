<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2016 - 2019 Nucleon + co. (http://www.nucleonplus.com)
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

require_once __DIR__.'/helper.php';

class com_rewardlabsInstallerScript extends JoomlatoolsInstallerHelper
{
    public function afterInstall($type, $installer)
    {
        if ($type !== 'discover_install') {
            $source = $installer->getParent()->getPath('source').'/extensions/ckeditor';
            $map = array(
                $source                     => JPATH_ROOT.'/libraries/joomlatools-components/ckeditor',
                $source.'/resources/assets' => JPATH_ROOT.'/media/koowa/com_ckeditor'
            );

            foreach ($map as $from => $to)
            {
                $temp   = $to.'_tmp';

                if (!JFolder::exists($from)) {
                    continue;
                }

                if (JFolder::exists($temp)) {
                    JFolder::delete($temp);
                }

                JFolder::copy($from, $temp);

                if (JFolder::exists($to)) {
                    JFolder::delete($to);
                }

                JFolder::move($temp, $to);
            }
        }
    }
}
