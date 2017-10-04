<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsTemplateHelperString extends ComKoowaTemplateHelperBehavior
{
    /**
     * Pluralize string
     *
     * @param  array  $config [count=>1,string="string"]
     * @return string
     */
    public function pluralize($config = array())
    {
        $config = new KObjectConfigJson($config);

        $count = (int) $config->count;

        if ($count > 1) {
            $result = KStringInflector::pluralize($config->string);
        } else {
            $result = KStringInflector::singularize($config->string);
        }

        return "{$count} {$result}";
    }

    /**
     * State label
     *
     * @param  array  $config [entity=>$article]
     * @return string
     */
    public function state($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity' => null
        ));

        return $this->getObject('translator')->translate(ComTextmanModelEntityArticle::$state_messages[$config->entity->state]);
    }
}
