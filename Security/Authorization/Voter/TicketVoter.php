<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30.11.15
 * Time: 17:55
 */

namespace Dreamlex\TicketBundle\Security\Authorization\Voter;

use Dreamlex\TicketBundle\Entity\Ticket;
use SellMMO\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class TicketVoter
 * @package Dreamlex\Bundle\TicketBundle\Security\Authorization\Voter
 */
class TicketVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    private $decisionManager;

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Ticket) {
            return false;
        }

        return true;
    }

    /**
     * TicketVoter constructor.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->decisionManager = new AccessDecisionManager();
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     * @throws \LogicException
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();


        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Ticket $ticket */
        $ticket = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($ticket, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Ticket $ticket
     * @param User   $user
     *
     * @return bool
     */
    private function canEdit(Ticket $ticket, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $ticket->getUser();
    }
}
