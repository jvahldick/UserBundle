<?php

namespace JHV\Bundle\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\Event;
use JHV\Bundle\UserBundle\JHVUserEvents;

/**
 * FlashListener
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class FlashListener implements EventSubscriberInterface
{

	protected $session;
	protected $translator;
	protected $translationDomain;

	protected static $successMessages = array(
		JHVUserEvents::CHANGE_PASSWORD_COMPLETED 	=> 'change_password.flash.success',
        JHVUserEvents::GROUP_CREATE_COMPLETED 		=> 'group.flash.created',
        JHVUserEvents::GROUP_DELETE_COMPLETED 		=> 'group.flash.deleted',
        JHVUserEvents::GROUP_EDIT_COMPLETED 		=> 'group.flash.updated',
        JHVUserEvents::PROFILE_EDIT_COMPLETED		=> 'profile.flash.updated',
        JHVUserEvents::REGISTRATION_COMPLETED 		=> 'registration.flash.user_created',
        JHVUserEvents::RESETTING_RESET_COMPLETED 	=> 'resetting.flash.success',
	);

	public function __construct(Session $session, TranslatorInterface $translator, $translationDomain)
	{
		$this->session = $session;
		$this->translator = $translator;
		$this->translationDomain = $translationDomain;
	}

	public static function getSubscribedEvents()
    {
        return array(
            JHVUserEvents::CHANGE_PASSWORD_COMPLETED 	=> 'addSuccessFlash',
            JHVUserEvents::GROUP_CREATE_COMPLETED 		=> 'addSuccessFlash',
            JHVUserEvents::GROUP_DELETE_COMPLETED 		=> 'addSuccessFlash',
            JHVUserEvents::GROUP_EDIT_COMPLETED 		=> 'addSuccessFlash',
            JHVUserEvents::PROFILE_EDIT_COMPLETED		=> 'addSuccessFlash',
            JHVUserEvents::REGISTRATION_COMPLETED 		=> 'addSuccessFlash',
            JHVUserEvents::RESETTING_RESET_COMPLETED 	=> 'addSuccessFlash',
        );
    }

    public function addSuccessFlash(Event $event)
    {
    	if (!isset(self::$successMessages[$event->getName()])) {
            throw new \InvalidArgumentException(sprintf(
                'This event %s does not correspond to a known flash message',
                $event->getName()
            ));
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$event->getName()]));
    }

    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, $this->translationDomain);
    }

}