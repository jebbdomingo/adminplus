<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComAdminplusViewCartHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $context->data->account = $this->getObject('com://admin/nucleonplus.model.accounts')
            ->id($this->getModel()->getState()->customer)
            ->fetch()
        ;
    }
}