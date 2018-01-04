<?php

namespace AppBundle\Security;

use AppBundle\Entity\TodoList;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ListVoter extends Voter
{
    const VIEW = 'view';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW))) {
            return false;
        }

        if (!$subject instanceof TodoList) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $list = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($list, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(TodoList $list, User $user)
    {
        return $user === $list->getOwner();
    }
}