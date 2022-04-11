<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Entity\Mode;
use App\Entity\Platform;
use App\Entity\Tag;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // call to object function
        $tabMode = $this->modeFixture();
        // creation of an array of objects for ulterior use
        $modeList = [];
        
        // hydratation of the object
        foreach($tabMode as $modeName){
            $mode = new Mode();
            $mode->setName($modeName);
            $modeList[] = $mode;
            $manager->persist($mode);
        } 
        
        $tabTag = $this->tagFixture();
        $tagList = [];
        
        // hydratation of the object
        foreach($tabTag as $tagName){
            $tag = new Tag();
            $tag->setName($tagName);
            $tagList[] = $tag;
            $manager->persist($tag);
        } 
        
        $tabPlatform = $this->platformFixture();
        $platformList = [];
        
        foreach($tabPlatform as $platformName){
            // random object of the relationship to hydrate object
            $randomMode = $modeList[mt_rand(0, count($modeList) - 1)];
            $platform = new Platform();
            $platform->setName($platformName)
                    // add random object to relationship
                     ->addMode($randomMode);
            $platformList[] = $platform;
            $manager->persist($platform);
        } 

        $tabItems = $this->itemFixture();
        $itemList = [];
        
        foreach($tabItems as $itemInfos){
            // random object of the relationship to hydrate object
            //$randomMode = $modeList[mt_rand(0, count($modeList) - 1)];
            $item = new Item();
            $item->setName($itemInfos["name"])
                 ->setDescription($itemInfos["description"])
                 ->setReleaseDate($itemInfos["release_date"])
                 ->setProductor($itemInfos["productor"])
                 ->setAutor($itemInfos["autor"])
                 ->setHost($itemInfos["host"])
                 ->setDeveloper($itemInfos["developer"])
                 ->setEditor($itemInfos["editor"])
                 ->setImage($itemInfos["picture"])
                 ->setMode($modeList[$itemInfos["modeIndex"]]);
                 foreach ($itemInfos["platformIndex"] as $platformIndex) {
                    $item->addPlatform($platformList[$platformIndex]);
                 }
                 foreach ($itemInfos["tagIndex"] as $tagIndex) {
                    $item->addTag($tagList[$tagIndex]);
                 }
            $itemList[] = $item;
                 
            $manager->persist($item);
        } 

        $userList = [];

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('admin');
        $userList[] = $admin;
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('user');
    
        $userList[] = $user;
        $manager->persist($user);

        $tabListItem = $this->listItemFixture();
        $listItemList = [];
        
        foreach($tabListItem as $listItemInfos){
            // random object of the relationship to hydrate object
           
            $listItem = new ListItem();
            $listItem
                 ->setItemAddedAt($listItemInfos["item_added_at"])
                 ->setItemStatus($listItemInfos["item_status"])
                 ->setItemComment($listItemInfos["item_comment"])
                 ->setItemRating($listItemInfos["item_rating"])
                 ->setMode($modeList[$listItemInfos["modeIndex"]])
                 ->addItem($itemList[$listItemInfos["itemIndex"]])
                 ->setUser($userList[$listItemInfos["user"]]);
            $listItemList[] = $listItem;
                 
            $manager->persist($listItem);
        } 

        // add to database
        $manager->flush();
    }

    public function modeFixture()
    {
        $tabMode = 
        [
            "podcasts",
            "jeuxvideo",
        ];

        return $tabMode;
    }

    public function tagFixture()
    {
        $tabTag = 
        [
            "Histoire",
            "Action",
            "Aventure",
            "Societe",
            "Gestion"
        ];

        return $tabTag;
    }

    public function platformFixture()
    {
        $tabPlatform = 
        [
            "Playstation 5",
            "PC",
            "Playstation 4",
            "Switch",
            "Apple Podcasts",
            "Spotify"
        ];

        return $tabPlatform;
    }

    public function itemFixture()
    {
        $tabItem = 
        [
            [
                "name" => "Stardew Valley", 
                "description" => "Stardew Valley est un jeu vidéo type RPG dans lequel le joueur doit gérer la ferme de son grand-père décédé.", 
                "release_date" => new DateTime('2016-02-26'), 
                "productor" => null,
                "autor" => null,
                "host" => null,
                "developer" => "ConcernedApe",
                "editor" => "ConcernedApe",
                "picture" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWo6LczbPXgH0M1ukTb3FkEvhqLBiC_a0MmeEKq_vaA8HTtbEqbD07OpgWatrTPSO1res&usqp=CAU",
                "modeIndex" => 1,
                "platformIndex" => [0,1],
                "tagIndex" => [0,1,2]
            ],
            [
                "name" => "Horizon forbidden west", 
                "description" => "orizon Forbidden West est un jeu vidéo d'action-RPG développé par Guerrilla Games et publié par Sony Interactive Entertainment.", 
                "release_date" => new DateTime('2022-02-18'), 
                "productor" => null,
                "autor" => null,
                "host" => null,
                "developer" => "Sony Interactive Entertainment",
                "editor" => "Sony Interactive Entertainment",
                "picture" => "https://image.jeuxvideo.com/medias/163293/1632927582-7714-jaquette-avant.gif",
                "modeIndex" => 1,
                "platformIndex" => [0,1,2],
                "tagIndex" => [1,2]
            ],
            [
                "name" => "Les Couilles sur la table", 
                "description" => "Un podcast destiné aux femmes, aux hommes qui se posent des questions sur eux-mêmes. Un jeudi sur deux.", 
                "release_date" => new DateTime('2017-01-01'), 
                "productor" => "Binge Audio",
                "autor" => "Victoire Tuaillon",
                "host" => "Victoire Tuaillon",
                "developer" => null,
                "editor" => null,
                "picture" => "https://back.bingeaudio.fr/wp-content/uploads/2021/03/visuel-programme-Binge.jpg",
                "modeIndex" => 0,
                "platformIndex" => [4,5],
                "tagIndex" => [3]
            ],
            [
                "name" => "Sur les épaules de Darwin", 
                "description" => "Un voyage avec ses escales dans la recherche, la culture et la vie sociale, accompagné par des textes et voix d’écrivains, de scientifiques et de poètes.", 
                "release_date" => new DateTime('2010-01-01'),
                "productor" => "France Inter",
                "autor" => "Jean Claude Ameisen",
                "host" => "Jean Claude Ameisen",
                "developer" => null,
                "editor" => null,
                "picture" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDR5jrcS-OvSUirhrmPccnzXMY7gvqd4RIBA&usqp=CAU",
                "modeIndex" => 0,
                "platformIndex" => [4,5],
                "tagIndex" => [0,3]
            ],
            [
                "name" => "Ma Petite StartUp", 
                "description" => "Recrutez des développeurs et faites vous un max de moulaga !", 
                "release_date" => new DateTime('2010-01-01'),
                "productor" => null,
                "autor" => null,
                "host" => null,
                "developer" => "Elie, Justine, Abdel, Bryan, Fabien",
                "editor" => "O'Clock",
                "picture" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDR5jrcS-OvSUirhrmPccnzXMY7gvqd4RIBA&usqp=CAU",
                "modeIndex" => 1,
                "platformIndex" => [1],
                "tagIndex" => [4]
            ]
        ];

        return $tabItem;
    }

    public function listItemFixture()
    {
        $tabItemList = 
        [
            [
                "item_added_at" => new DateTimeImmutable('NOW'),
                "item_status" => 1, 
                "item_comment" => "Tip top moumoute", 
                "item_rating" => null,
                "modeIndex" => 1,
                "itemIndex" => 0,
                "user" => 0
            ],
            [
                "item_added_at" => new DateTimeImmutable('NOW'),
                "item_status" => 0, 
                "item_comment" => null, 
                "item_rating" => 9,
                "modeIndex" => 0,
                "itemIndex" => 2,
                "user" => 0
            ],
            [
                "item_added_at" => new DateTimeImmutable('NOW'),
                "item_status" => 2, 
                "item_comment" => "Incroyable !!", 
                "item_rating" => 10,
                "modeIndex" => 1,
                "itemIndex" => 4,
                "user" => 1
            ]
        ];

        return $tabItemList;
    }
}