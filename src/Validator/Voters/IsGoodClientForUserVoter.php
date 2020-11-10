<?php

namespace App\Validator\Voters;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IsGoodClientForUserVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::DELETE, self::EDIT, self::SHOW])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $client = $token->getUser();

        if (!$client instanceof Client) {
            return false;
        }

        /**
         * @var User $user
         */
        $user = $subject;

        if ($user->getClient()->getId() !== $client->getId()) {
            return false;
        }

        return true;
    }
}
