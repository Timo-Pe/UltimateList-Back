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
            $mode->setName($modeName["name"]);
            $mode->setSlug($modeName["slug"]);
            $mode->setColor($modeName["color"]);
            $modeList[] = $mode;
            $manager->persist($mode);
        } 
        
        $tabTag = $this->tagFixture();
        $tagList = [];
        
        // hydratation of the object
        foreach($tabTag as $tagName){
            $tag = new Tag();
            $tag->setName($tagName["name"]);
            $tag->setColor($tagName["color"]);
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
        $admin->setPassword('$2y$13$KsCO7uSoma1KkQCetaN78.tg9HLQ9TGPdlusc3K9OoIgxioPOVSUy');
        $userList[] = $admin;
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('$2y$13$hAEC.bJQdfPcJbQ6jl24Pub5QYibpM4kvTzgIqp9Me0i4QtYfGOPa');
    
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
            [
                "name" => "Podcasts",
                "slug" => "podcasts",
                "color" => "#FFA47A"
            ],
            [
                "name" => "Jeux Vidéo",
                "slug" => "jeuxvideo",
                "color" => "#7068F4"
            ],
        ];

        return $tabMode;
    }

    public function tagFixture()
    {
        $tabTag = 
        [
            [
                "name" => "Histoire",
                "color" => "#4A412A"
            ],
            [
                "name" => "Action",
                "color" => "#C92FAA"
            ],
            [
                "name" => "Aventure",
                "color" => "#FCFF00"
            ],
            [
                "name" => "Societe",
                "color" => "#00FF18"
            ],
            [
                "name" => "Gestion",
                "color" => "#FF9C00"
            ]
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
            "Spotify",
            "Playstation 2"
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
                "picture" => "https://i.gyazo.com/afc1f79710f05003cb5ddb9b1d6f8c5c.png",
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
                "picture" => "https://i.gyazo.com/1be94abd64e834fae9e74a9f4ba5edb0.png",
                "modeIndex" => 1,
                "platformIndex" => [1],
                "tagIndex" => [4]
            ],
            [
                "name" => "Grand Theft Auto V", 
                "description" => "Jeu d'action-aventure en monde ouvert, Grand Theft Auto (GTA) V sur PlayStation 4 vous place dans la peau de trois personnages inédits : Michael, Trevor et Franklin. Ces derniers ont élu domicile à Los Santos, ville de la région de San Andreas. Braquages et missions font partie du quotidien du joueur qui pourra également cohabiter avec 29 autres utilisateurs dans cet univers persistant. Cette version permet en outre de jouer en vue FPS tout au long de l'aventure.", 
                "release_date" => new DateTime('2013-09-17'),
                "productor" => null,
                "autor" => null,
                "host" => null,
                "developer" => "Rockstar North",
                "editor" => "Rockstar Games",
                "picture" => "https://image.jeuxvideo.com/medias-sm/163129/1631287693-8700-jaquette-avant.jpg",
                "modeIndex" => 1,
                "platformIndex" => [0,1,2],
                "tagIndex" => [0,1,2,4]
            ],
            [
                "name" => "Kingdom Hearts", 
                "description" => "Kingdom Hearts débute sur l'Île du Destin où Sora, Riku et Kairi habitent. Les trois amis veulent quitter l'île pour explorer de nouveaux mondes et ont préparé un radeau à cet effet. Un soir, l'île est attaquée par d'obscures créatures, les Sans-cœur.", 
                "release_date" => new DateTime('2002-01-01'),
                "productor" => null,
                "autor" => null,
                "host" => null,
                "developer" => "Tetsuya Nomura",
                "editor" => "SQUARE ENIX",
                "picture" => "https://i.gyazo.com/0a2026070ee028e317dd283e080db8c2.png",
                "modeIndex" => 1,
                "platformIndex" => [6],
                "tagIndex" => [1, 2]
            ],
            [
                "name" => "Oyez Oh Yeah", 
                "description" => "Oyez Oh Yeah est un podcast d’Histoire comme on n’en avait jamais entendu ! Entre apprentissage et humour, Manon Bril et Alex Ramirès revisitent la discipline en recevant à chaque épisode un ou une spécialiste qui répond à toutes leurs questions.", 
                "release_date" => new DateTime('2021-01-01'),
                "productor" => "Anaïs Carayon",
                "autor" => "Vincent Girard",
                "host" => "Manon Bril et Alex Ramires",
                "developer" => null,
                "editor" => null,
                "picture" => "https://i.gyazo.com/7605d9648d22094499f0f5ece24ffecd.png",
                "modeIndex" => 0,
                "platformIndex" => [5],
                "tagIndex" => [0, 3]
            ],
            [
                "name" => "FloodCast", 
                "description" => "Floodcast, c'est le podcast présenté par Florent Bernard avec la complicité d'Adrien Ménielle dans lequel ils invitent des copains pour discuter de pleins de choses. Ce qui se passe dans chaque émission n'engage que nous et le président des Etats-Unis.", 
                "release_date" => new DateTime('2015-01-01'),
                "productor" => "Florent Bernard",
                "autor" => "Florent Bernard",
                "host" => "Florent Bernard et Adrien Ménielle",
                "developer" => null,
                "editor" => null,
                "picture" => "https://i.gyazo.com/8eb3c1e367f71e9a9a728c3050758cdb.png",
                "modeIndex" => 0,
                "platformIndex" => [4, 5],
                "tagIndex" => [3]
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
            ],
            [
                "item_added_at" => new DateTimeImmutable('NOW'),
                "item_status" => 0, 
                "item_comment" => null, 
                "item_rating" => null,
                "modeIndex" => 1,
                "itemIndex" => 6,
                "user" => 1
            ]
        ];

        return $tabItemList;
    }
}