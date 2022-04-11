<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';

    private $security;

    public function __construct(Security $security){
        
        $this->security = $security;
        
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::VIEW])) {
            return false; 
        }
        if (!$subject instanceof User) {
            return false;
        }
        return true;

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                if ($user->getUserIdentifier() !== $subject->getUserIdentifier() && !$this->security->isGranted("ROLE_ADMIN")){
                    return false;
                }
                return true ;
                break;
            case self::VIEW:
                return true ;
                break;
        }

        return false;
    }

}
