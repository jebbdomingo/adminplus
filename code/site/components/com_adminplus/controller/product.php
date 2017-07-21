<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerProduct extends ComKoowaControllerModel
{
    protected function _actionSync(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if (count($entities))
        {
            foreach($entities as $entity) {
                $entity->sync();
            }
        }

        return $entities;
    }
}
