<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

abstract class ComRewardlabsControllerIntegrationAbstract extends ComKoowaControllerModel
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_debug');
        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.add', '_mapColumns');
        $this->addCommandCallback('before.edit', '_debug');
        $this->addCommandCallback('before.edit', '_validate');
        $this->addCommandCallback('before.edit', '_mapColumns');
        $this->addCommandCallback('before.delete', '_debug');
        $this->addCommandCallback('before.delete', '_validate');
    }

    protected function _validate(KControllerContextInterface $context)
    {
        return true;
    }

    protected function _debug(KControllerContextInterface $context)
    {
        $data = array(
            'request_data' => $context->request->toString()
        );

        $log = $this->getObject('com://site/rewardlabs.model.httplogs')->create($data);
        $log->save();
    }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        return array();
    }
}
