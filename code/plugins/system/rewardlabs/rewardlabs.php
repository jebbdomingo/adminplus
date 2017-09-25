<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class PlgSystemRewardlabs extends JPlugin
{
    /**
     * Hook into onAfterRoute sytem event
     *
     * @return void
     */
    public function onAfterRoute()
    {
        // Render the sticky toolbar on joomlatools-framework components
        $this->getObject('event.publisher')->addListener('onBeforeKoowaHtmlViewRender', function($context) {
            if ($context->layout === 'koowa') {
                $context->getTarget()->setContent($this->_renderToolbar().$context->getTarget()->getContent());
            }
        }, KEvent::PRIORITY_LOW);

        $this->_logHttpRequests();
    }

    /**
     * This event handler does not run when the page is rendered by the framework
     */
    public function onAfterDispatch()
    {
        // Renders the sticky toolbar in Joomla context
        JFactory::getDocument()->addCustomTag($this->_renderToolbar());

        $request = $this->getObject('request');
        $input   = JFactory::getApplication()->input;

        // Remember sponsor id from referral link
        $this->_persistSponsorId();

        // Social meta tags for product page
        $view = $input->get('view', null);
        $id   = $input->get('id', null);

        if ($view == 'product' && $id) {
            $this->_generateSocialMetatags($id);
        }
    }

    /**
     * Persist sponsor id
     * 
     * @return void
     */
    protected function _persistSponsorId()
    {
        $input      = JFactory::getApplication()->input;
        $sponsor_id = $input->get('sponsor_id', null);

        if ($sponsor_id)
        {
            $session = $this->getObject('lib:user.session');
            $session->set('sponsor_id', $sponsor_id);
        }
    }

    /**
     * Generate social meta tags from item data
     * 
     * @param  integer $product_id
     * 
     * @return void
     */
    protected function _generateSocialMetatags($product_id)
    {
        $item = $this->getObject('com://admin/qbsync.model.items')->id($product_id)->fetch();

        $image = JURI::base() . "images/{$item->image}";

        $document = JFactory::getDocument();
        $document->setMetaData('twitter:card', 'summary');
        $document->setMetaData('twitter:title', $item->Name);
        $document->setMetaData('twitter:description', $item->Description);
        $document->setMetaData('twitter:image:alt', $item->Name);
        $document->setMetaData('twitter:image', $image);
        $document->setMetaData('og:type', 'item');
        $document->setMetaData('og:title', $item->Name);
        $document->setMetaData('og:description', $item->Description);
        $document->setMetaData('og:url', JURI::current());
        $document->setMetaData('og:image', $image);
    }

    /**
     * Renders the sticky toolbar that provides access to the dashboard.
     *
     * @return void
     */
    protected function _renderToolbar()
    {
        $request = $this->getObject('request')->getQuery();
        $input   = JFactory::getApplication()->input;
        $option  = $input->get('option', 'cmd');
        $view    = $input->get('view', 'cmd');
        $layout  = $input->get('layout', null);
        $id      = $input->get('id', null);

        // Permissions
        $user        = $this->getObject('user');
        $isAuthentic = $user->isAuthentic();

        // Only show the edit bar with the specified specifications above
        $baseUrl = JURI::root();
        $token   = JSession::getFormToken();
        $return  = urlencode(base64_encode($baseUrl));

        if (in_array(2, $user->getGroups())) {
            $dashboard_url = $baseUrl . 'index.php?option=com_nucleonplus&view=account';
        } else {
            $dashboard_url = $baseUrl . 'index.php?option=com_rewardlabs&view=accounts';
        }

        $config = new KObjectConfigJson();
        $config->append(array(
            'options' => array(
                'id'          => $id,
                'isAuthentic' => $isAuthentic,
                'url'         => array(
                    'homeUrl'      => JRoute::_($baseUrl),
                    'dashboardUrl' => JRoute::_($dashboard_url),
                    'loginUrl'     => JRoute::_('index.php?option=com_users&view=login'),
                    'logoutUrl'    => JRoute::_("index.php?option=com_users&task=user.logout&{$token}=1&return={$return}")
                ),
            )
        ));

        $html = $this->getObject('template.default')
            ->addFilter('lib:template.filter.style')
            ->addFilter('lib:template.filter.script')
            ->addFilter('com:koowa.template.filter.asset')
            ->loadString('
                <ktml:style src="media://com_rewardlabs/css/toolbar.css" />
                <ktml:script src="media://com_rewardlabs/js/rewardlabs.toolbar.js" />
                <ktml:script src="media://com_rewardlabs/js/toolbar.js" />
                <script>Rewardlabs.ToolBar.init('.$config->options.');</script>', 'php')
            ->render();

        return $html;
    }

    protected function _logHttpRequests()
    {
        $request  = $this->getObject('request');
        $data     = json_encode($request->query->toArray());
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        $log = $this->getObject('com://site/rewardlabs.model.httplogs')->create(array(
            'referrer' => $referrer,
            'request_data' => $data
        ));

        $log->save();
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array                    $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        return KObjectManager::getInstance()->getObject($identifier, $config);
    }

    /**
     * Only run this when:
     * - Request method is GET
     * - Document type is HTML
     * - We are on site app
     * 
     * @return bool
     */
    protected function _canRun()
    {
        return (JFactory::getApplication()->input->getMethod() === 'GET'
            && JFactory::getDocument()->getType() === 'html'
            && JFactory::getApplication()->isSite());
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa') && class_exists('KObjectManager') && (bool) JComponentHelper::getComponent('com_rewardlabs', true)->enabled && $this->_canRun())
        {
            try
            {
                $return = parent::update($args);
            }
            catch (Exception $e)
            {
                if (JDEBUG) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        return $return;
    }
}
