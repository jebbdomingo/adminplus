<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Processable Controller Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 */
class ComRewardlabsControllerBehaviorProcessable extends KControllerBehaviorAbstract
{
    protected function _beforeProcessing(KControllerContextInterface $context)
    {
        // JFactory::getUser()->id is needed to support Joomla login/authentication programatically
        // Nooku's user object needs to reinitialize after calling JFactory::getApplication('site')->login() hence the need for JFactory::getUser()->id
        $userId = (int) $this->getObject('user')->getId();
        if (!$userId) {
            $userId = (int) JFactory::getUser()->id;
        }

        $context->request->data->processed_by = $userId;
        $context->request->data->processed_on = gmdate('Y-m-d H:i:s');
    }
}
