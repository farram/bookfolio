<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationDatabaseCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-database';

    public const CURRENT_DB = 'dev';
    public const SOURCE_DB = 'bookfolio_dev';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->em->getConnection();

        $conn->exec("
        
        INSERT IGNORE INTO `sort_list_follow` (`id`, `title`, `description`, `sort`)
        VALUES
            (1, 'createdAt', 'Date de création', '1'),
            (2, 'lastname', 'Nom', '2'),
            (3, 'firstname', 'Prénom', '3')");

        $conn->exec("
        
            INSERT IGNORE INTO `events` (`id`, `type`, `text`)
            VALUES
                (1, 'like', 'a aimé votre photo'),
                (2, 'comment', 'a commenté votre photo'),
                (3, 'follow', 'a commencé à vous suivre')");

        $conn->exec("
        
            INSERT IGNORE INTO `plan` (`id`, `plan_name`, `plan_price`, `is_highlight`, `icon`, `publication`, `total`, `id_price_api`, `position`, `slug`)
            VALUES
                (1, 'Awesome', '500', '0', 'NULL', '60', '5', 'price_1INSeaFekYzBq4jwckgIVjXJ', '2','awesome'),
                (2, 'Formule Pro', '1200', '1', 'NULL', '100', '12', 'price_1IhxtUFekYzBq4jwRD57c3u6', '3', 'pro'),
                (3, 'Starter', '0', '0', 'NULL', '10', '0', '', '1','starter')");

        $conn->exec("
        
            INSERT IGNORE INTO `plan_feature` (`id`, `title`, `description`)
            VALUES
                (1, 'Photos par mois', 'Photos par mois'),
                (2, 'Galeries', 'Galeries'),
                (3, 'Vidéos par mois', 'Vidéos par mois'),
                (4, 'Annonces par mois', 'Annonces par mois'),
                (5, 'Statistiques de visites', 'Statistiques de visites'),
                (6, 'Designs premium', 'Templates premium'),
                (7, 'Nom de domaine (.fr)', 'Nom de domaine (.fr)'),
                (8, 'Galeries privées', 'Galeries privées'),
                (9, 'Messagerie interne', 'Messagerie interne');
            ");

        $conn->exec("
        
            INSERT IGNORE INTO `plan_details` (`id`, `plan_id`, `feature_id`, `value`)
            VALUES
                (1, 1, 1, '60'),
                (2, 1, 2, '10'),
                (3, 1, 3, '15'),
                (4, 1, 4, '10'),
                (5, 1, 5, 'true'),
                (6, 1, 6, 'true'),
                (7, 1, 7, 'true'),
                (8, 1, 8, 'true'),
                (9, 1, 9, 'true'),
                (10, 2, 1, '100'),
                (11, 2, 2, '100'),
                (12, 2, 3, '100'),
                (14, 2, 4, '100'),
                (15, 2, 5, 'true'),
                (16, 2, 6, 'true'),
                (17, 2, 7, 'true'),
                (18, 2, 8, 'true'),
                (19, 2, 9, 'true'),
                (20, 3, 1, '10'),
                (21, 3, 2, '5'),
                (22, 3, 3, '5'),
                (23, 3, 4, '2'),
                (24, 3, 5, 'false'),
                (25, 3, 6, 'false'),
                (26, 3, 7, 'false'),
                (27, 3, 8, 'false'),
                (28, 3, 9, 'false');");

        $conn->exec("
        INSERT INTO `design` (`id`, `title`, `description`, `image`, `is_active`, `is_custom`, `slug`, `position`, `is_new`, `is_default`)
        VALUES
            (1, 'Tile Light', 'Tile Light', '', 1, 0, 'tile-light', 1, 0, 1),
            (2, 'Tile Dark', 'Tile Dark', '', 1, 0, 'tile-dark', 2, 0, 0),
            (3, 'Tile Full Light', 'Tile Full Light', '', 1, 0, 'tile-full-light', 3, 0, 0),
            (4, 'Tile Full Dark', 'Tile Full Dark', '', 1, 0, 'tile-full-dark', 4, 0, 0),
            (5, 'Tile Wide Dark', 'Tile Wide Dark', '', 1, 0, 'wide-dark', 5, 0, 0),
            (6, 'Tile Wide Light', 'Tile Wide Light', '', 1, 0, 'wide-light', 6, 0, 0),
            (7, 'Alba Light', 'Alba Light', '', 1, 0, 'alba-light', 7, 0, 0),
            (8, 'Alba Dark', 'Alba Dark', '', 1, 0, 'alba-dark', 8, 0, 0),
            (9, 'Illo Dark', 'Illo Dark', '', 1, 0, 'illo-dark', 9, 0, 0),
            (10, 'Illo Light', 'Illo Light', '', 1, 0, 'illo-light', 10, 0, 0),
            (11, 'Mosaic Light', 'Mosaic Light', '', 1, 0, 'mosaic-light', 11, 0, 0),
            (12, 'Mosaic Dark', 'Mosaic Dark', '', 1, 0, 'mosaic-dark', 12, 0, 0),
            (13, 'Folio Light', 'Folio Light', '', 1, 0, 'folio-light', 13, 0, 0),
            (14, 'Folio Dark', 'Folio Dark', '', 1, 0, 'folio-dark', 14, 0, 0),
            (15, 'Kool Light', 'Kool Light', '', 1, 0, 'kool-light', 15, 0, 0),
            (16, 'Kool Dark', 'Kool Dark', '', 1, 0, 'kool-dark', 16, 0, 0),
            (17, 'Big Gap Light', 'Big Gap Light', '', 1, 0, 'big-gap-light', 17, 1, 0),
            (18, 'Big Gap Dark', 'Big Gap Dark', '', 1, 0, 'big-gap-dark', 18, 1, 0)");

        $conn->exec("/*!40000 ALTER TABLE `avantages` DISABLE KEYS */; 
        INSERT IGNORE INTO `avantages` (`id`, `title`, `description`, `icon`, `is_active`)
        VALUES
            (32, 'Création de galeries illimitées', '<p>Sur Bookfolio, cr&eacute;ez autant de galeries que vous le souhaitez, et am&eacute;liorez la visibilit&eacute; de votre book.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/folder.png', 1),
            (33, 'Affichage de vos photos Instagram sur votre book', '<p>Affichez de mani&egrave;re on ne peut plus simple vos images provenant d&#39;Instagram sur votre book. Vous pouvez trier les photos selon votre choix (populaires, r&eacute;centes, plus comment&eacute;es etc.)</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/like.png', 1),
            (34, 'Annuaire complet', '<p>D&eacute;veloppez votre r&eacute;seau gr&acirc;ce &agrave; notre annuaire complet. Trouvez d&rsquo;autres artistes pour partager votre travail ou lancer une collaboration.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/inbox.png', 1),
            (35, 'Plus de 15 templates disponibles', '<p>Choisissez un book &agrave; votre image parmi plus de 15 templates disponibles con&ccedil;us par nos designers.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/layers.png', 1),
            (36, 'Recevez des avis', '<p>Donnez la parole &agrave; vos visiteurs en affichant leur avis pour d&eacute;velopper booster votre visibilit&eacute; !</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/favorites.png', 1),
            (37, 'Galeries protégées', '<p>Prot&eacute;gez vos galeries avec un mot de passe.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/padlock.png', 1),
            (38, 'Gestion de contacts', '<p>Cette fonctionnalit&eacute; vous permet de vous cr&eacute;er votre propre r&eacute;seau. En proc&eacute;dant ainsi, vous aurez la possibilit&eacute; d&#39;&eacute;changer avec vos contacts gr&acirc;ce au Messenger Bookfolio.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/users.png', 1),
            (39, 'Publication des annonces', '<p>Boostez vos chances de faire conna&icirc;tre votre travail et de d&eacute;buter de nouveaux projets.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/speaker.png', 1),
            (40, 'Création de pages illimitées', '<p>Vous allez pouvoir cr&eacute;er un nombre illimit&eacute; de pages sur votre book en toute simplicit&eacute;. Vous pourrez ainsi y ajouter facilement du contenu.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/document.png', 1),
            (41, 'Images protégées', '<p>Prot&eacute;gez vos images avec un filigrane.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/padlock.png', 1),
            (42, 'Templates optimisés pour tous les appareils', '<p>Votre site s&#39;ajuste automatiquement aux &eacute;crans mobiles.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/smartphone.png', 1),
            (43, 'Sans publicité', '<p>Votre confort est le plus important pour nous. Aucune publicit&eacute; ne viendra vous d&eacute;ranger sur le site.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/heart.png', 1),
            (44, 'SEO intégré', '<p>Bookfolio est naturellement r&eacute;f&eacute;renc&eacute;. Vous pouvez le d&eacute;sactiver mais nous vous le d&eacute;conseillons si vous voulez rester visible sur Google.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/line-chart.png', 1),
            (45, 'Messagerie interne', '<p>Discutez avec les autres membres de Bookfolio pour d&eacute;buter de nouvelles collaborations.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/chat.png', 1),
            (46, 'Qualité des images', '<p>Vos photos sont publi&eacute;es en haute d&eacute;finition et conservent toute leur qualit&eacute;.</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/favorites.png', 1),
            (47, 'Statistiques de visites de votre book', '<p>Statistiques en d&eacute;tail les visites d&rsquo;aujourd&rsquo;hui, d&rsquo;hier, du mois en cours, du mois dernier, de cette ann&eacute;e et de l&rsquo;ann&eacute;e derni&egrave;re de votre book !</p>\r\n', 'http://blog.bookfolio.fr/wp-content/uploads/2020/04/bar-chart.png', 1);");

        $conn->exec("/*!40000 ALTER TABLE `profession` DISABLE KEYS */;  
        INSERT IGNORE INTO `profession` (`id`, `title`, `description`, `slug`, `is_active`, `position`, `is_advanced_search`)
        VALUES
            (1, 'Modèle', 'Modèles', 'modeles', 1, 1, 1),
            (2, 'Photographe', 'Photographes', 'photographes', 1, 2, 0),
            (3, 'Maquilleur / Maquilleuse', 'Maquilleurs / Maquilleuses', 'maquilleurs', 1, 3, 0),
            (4, 'Comédien / Comédienne', 'Comédiens', 'comediens', 1, 4, 1),
            (5, 'Danseur / Danseuse', 'Danseurs', 'danseurs', 1, 5, 0),
            (6, 'Musicien / Musicienne', 'Musiciens', 'musiciens', 1, 6, 0),
            (7, 'Vidéaste', 'Vidéastes', 'videastes', 1, 7, 0),
            (8, 'Styliste', 'Stylistes', 'stylistes', 1, 8, 0),
            (9, 'Designer graphique', 'Graphistes', 'graphistes', 1, 9, 0),
            (10, 'Auteur', 'Auteurs', 'auteurs', 1, 10, 0),
            (11, 'Artisan', 'Artisans', 'artisans', 1, 11, 0),
            (12, 'Agence', 'Agences', 'agences', 1, 12, 0),
            (13, 'Chanteur / Chanteuse', 'Chanteurs', 'chanteurs', 1, 13, 0),
            (14, 'Coiffeur / Coiffeuse', 'Coiffeurs', 'coiffeurs', 1, 14, 0),
            (15, 'Chargé(e) de communication', 'Chargés de communication', 'charges_communication', 1, 15, 0),
            (16, 'Artiste peintre', 'Artistes peintres', 'peintres', 1, 16, 0),
            (17, 'Artiste sculpteur', 'Artistes sculpteurs', 'sculpteurs', 1, 17, 0),
            (18, 'Maître dart', 'Maîtres dart', 'maitres_art', 1, 18, 0),
            (19, 'Danseuse et modèle', 'Danseuses et modèles', 'danseuse_modele', 1, 19, 1),
            (20, 'Danseur et modèle', 'Danseurs et modèles', 'danseur_modele', 1, 20, 1),
            (21, 'Architecte', 'Architectes', 'architecte', 1, 21, 0);
        ");

        $conn->exec("/*!40000 ALTER TABLE `experience` DISABLE KEYS */; 
        INSERT IGNORE INTO `experience` (`id`, `profession_id`, `title`, `is_active`)
        VALUES
            (1, 2, 'Première expérience', 1),
            (2, 2, 'Photographe amateur', 1),
            (3, 2, 'Confirmé', 1),
            (4, 2, 'Freelance', 1),
            (5, 2, 'Professionnel', 1),
            (6, 1, 'Première expérience', 1),
            (7, 1, 'Amateur', 1),
            (8, 1, 'Amateur confirmé', 1),
            (9, 1, 'Freelance', 1),
            (10, 1, 'Modèle professionnel', 1),
            (11, 1, 'Mannequin professionnel', 1),
            (12, 3, 'Quelques expériences', 1),
            (13, 3, 'Expérimenté(e)', 1),
            (14, 3, 'Grande expérience', 1),
            (15, 4, 'Quelques expériences', 1),
            (16, 4, 'Expérimenté(e)', 1),
            (17, 4, 'Grande expérience', 1),
            (18, 5, 'Quelques expériences', 1),
            (19, 5, 'Expérimenté(e)', 1),
            (20, 5, 'Grande expérience', 1),
            (21, 6, 'Quelques expériences', 1),
            (22, 6, 'Expérimenté(e)', 1),
            (23, 6, 'Grande expérience', 1),
            (24, 7, 'Quelques expériences', 1),
            (25, 7, 'Expérimenté(e)', 1),
            (26, 7, 'Grande expérience', 1),
            (27, 8, 'Quelques expériences', 1),
            (28, 8, 'Expérimenté(e)', 1),
            (29, 8, 'Grande expérience', 1),
            (30, 9, 'Quelques expériences', 1),
            (31, 9, 'Expérimenté(e)', 1),
            (32, 9, 'Grande expérience', 1),
            (33, 10, 'Quelques expériences', 1),
            (34, 10, 'Expérimenté(e)', 1),
            (35, 10, 'Grande expérience', 1),
            (36, 11, 'Quelques expériences', 1),
            (37, 11, 'Expérimenté(e)', 1),
            (38, 11, 'Grande expérience', 1),
            (39, 12, 'Quelques expériences', 1),
            (40, 12, 'Expérimenté(e)', 1),
            (41, 12, 'Grande expérience', 1),
            (42, 13, 'Quelques expériences', 1),
            (43, 13, 'Expérimenté(e)', 1),
            (44, 13, 'Grande expérience', 1),
            (45, 14, 'Quelques expériences', 1),
            (46, 14, 'Expérimenté(e)', 1),
            (47, 14, 'Grande expérience', 1),
            (48, 15, 'Quelques expériences', 1),
            (49, 15, 'Expérimenté(e)', 1),
            (50, 15, 'Grande expérience', 1),
            (51, 16, 'Grande expérience', 1),
            (52, 16, 'Expérimenté(e)', 1),
            (53, 16, 'Quelques expériences', 1),
            (54, 17, 'Grande expérience', 1),
            (55, 17, 'Expérimenté(e)', 1),
            (56, 17, 'Quelques expériences', 1),
            (57, 18, 'Grande expérience', 1),
            (58, 18, 'Expérimenté(e)', 1),
            (59, 18, 'Quelques expériences', 1),
            (60, 19, 'Grande expérience', 1),
            (61, 19, 'Expérimenté(e)', 1),
            (62, 19, 'Quelques expériences', 1),
            (63, 19, 'Amateur', 1),
            (64, 20, 'Grande expérience', 1),
            (65, 20, 'Expérimenté', 1),
            (66, 20, 'Quelques expériences', 1),
            (67, 20, 'Amateur', 1),
            (68, 21, 'Quelques expériences', 1),
            (69, 21, 'Expérimenté(e)', 1),
            (70, 21, 'Grande expérience', 1);");

        $conn->exec("/*!40000 ALTER TABLE `sort_list_annuaire` DISABLE KEYS */; 
        REPLACE INTO `sort_list_annuaire` (`id`, `title`, `description`, `sort`)
        VALUES
            (1, 'lastname', 'Nom', 1),
            (2, 'firstname', 'Prénom', 2),
            (3, 'createdAt', 'Date de création', 3),
            (4, 'updatedAt', 'Date de mise à jour', 4);
        ");

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . ".`page` (
                id,
                user_id,
                title,
                content,
                is_active,
                created_at,
                updated_at,
                slug
                )
            SELECT 
                id,
                user_id,
                title,
                '',
                status,
                created_date,
                created_date,
                ''  
            FROM " . self::SOURCE_DB . '.pages_users_book
            WHERE NOT EXISTS (SELECT * FROM page)');

        $conn->exec('
            UPDATE IGNORE ' . self::CURRENT_DB . '.`page` SET
        title = 
        (SELECT 
            title
            FROM ' . self::SOURCE_DB . '.pages_users_book_children
            WHERE page_id = ' . self::CURRENT_DB . '.`page`.id)
        , 
        content = 
        (SELECT 
            content
            FROM ' . self::SOURCE_DB . '.pages_users_book_children
            WHERE page_id = ' . self::CURRENT_DB . '.`page`.id)');

        $conn->exec('
            UPDATE IGNORE ' . self::CURRENT_DB . ".`page` 
        SET slug = replace(trim(lower(title)), ' ', '-')");

        $conn->exec('
        REPLACE INTO ' . self::CURRENT_DB . ".`user` (
            id,
            profession_id, 
            experience_id, 
            stripe_customer_id,
            email,
            roles,
            password,
            is_verified,
            access_token,
            created_at,
            updated_at,
            uuid,
            is_active,
            username,
            lastname,
            firstname,
            thumbnail,
            about,
            is_demo
            )
        SELECT  
            id,
            type_compte,
            experience,
            NULL,
            email,
            '[\"ROLE_USER\"]',
            password,
            1, 
            NULL, 
            created_date, 
            updated_at,
            UUID(),
            1,
            username,
            firstname,
            lastname,
            '',
            about,
            0
        FROM `bookfolio_dev`.`user` WHERE status = '1' AND username IS NOT NULL AND experience IS NOT NULL
        AND NOT EXISTS (SELECT * FROM user)
        ");

        $conn->exec('SET foreign_key_checks = 0;');

        $conn->exec('
        DELETE t1 
        FROM user AS t1
        WHERE t1.username IS NULL');

        $conn->exec('
        DELETE t1 
        FROM user AS t1
        JOIN (SELECT MAX(id) AS t2, id, username FROM user) t2 
        WHERE t1.id > t2.id
        AND t1.username = t2.username');

        $conn->exec('        
        INSERT INTO ' . self::CURRENT_DB . ".`book` (
            user_id,
            design_id,
            name,
            title,
            allow_seo,
            description,
            keywords,
            code_analytics,
            show_contact,
            allow_comments,
            show_visitor_counter,
            style_photos
            )
        SELECT
            id,
            1,
            username,
            NULL,
            1,
            NULL,
            NULL,
            '',
            1,
            1,
            1,
            style_photos
        FROM " . self::SOURCE_DB . '.user
        WHERE NOT EXISTS (SELECT * FROM book)');

        $conn->exec("
        UPDATE `user` SET `roles` = '[\"ROLE_ADMIN\"]' WHERE `email` = 'infobookfolio@gmail.com'
        ");

        $conn->exec("
        UPDATE `user` SET `is_demo` = '1' WHERE `email` = 'infobookfolio@gmail.com'
        ");

        $conn->exec("
        UPDATE `user` SET `roles` = '[\"ROLE_ADMIN\"]' WHERE `email` = 'farramm@me.com'
        ");

        $conn->exec("
        UPDATE `avis` SET `is_active` = '0' WHERE `user_id` = '605'
        ");

        $conn->exec("
        UPDATE `avis` SET `is_active` = '0' WHERE `user_id` = '538'
        ");

        $conn->exec("
        INSERT INTO `ethnicity` (`id`, `title`, `is_active`)
        VALUES
            (1, 'Européen(e)',1),
            (2, 'Asiatique',1),
            (3, 'Latino-américain',1),
            (4, 'Arabe/Moyen-Orient',1),
            (5, 'Indien',1),
            (6, 'Métissé',1),
            (7, 'Africaine',1),
            (8, 'Autres',1)
        ");

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`gallery` (
                id,
                user_id,
                cover_image_id,
                name,
                description,
                created_at,
                updated_at,
                is_active,
                position,
                is_protect,
                password_hash,
                created_at_password,
                slug
                )
            SELECT 
                id,
                user_id,
                NULL,
                title,
                description,
                created_date,
                updated_at,
                statut,
                position,
                iagree,
                password,
                date_update_password,
                slug
            FROM ' . self::SOURCE_DB . '.media_folder_pro');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`images` (
                id,
                user_id,
                gallery_id,
                image_name,
                title,
                created_at,
                updated_at,
                is_visible,
                position,
                description,
                copyright,
                keywords,
                is_nsfw,
                is_home,
                is_gallery,
                allow_favorites,
                allow_likes,
                allow_comments,
                type,
                size,
                count_view
                )
            SELECT 
                id,
                user_id,
                folder_id,
                file_name,
                title,
                created_date,
                updated,
                status,
                position,
                NULL,
                NULL,
                NULL,
                epublic,
                visibleHome,
                0,
                1,
                1,
                1,
                NULL,
                NULL,
                0
            FROM ' . self::SOURCE_DB . '.media_pro
            ');

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . ".`page` (
            id,
            user_id,
            title,
            content,
            is_active,
            created_at,
            updated_at,
            slug
            )
        SELECT 
            id,
            user_id,
            title,
            '',
            status,
            created_date,
            created_date,
            ''
        FROM " . self::SOURCE_DB . '.pages_users_book');

        $conn->exec('
        UPDATE IGNORE ' . self::CURRENT_DB . '.`page` SET content = 
        (SELECT 
        content
        FROM ' . self::SOURCE_DB . '.pages_users_book_children
        WHERE page_id = ' . self::CURRENT_DB . '.`page`.id
        )');

        $conn->exec('
            UPDATE ' . self::CURRENT_DB . ".`page` 
            SET slug = replace(trim(lower(title)), ' ', '-')");

        $conn->exec("
        UPDATE `images` SET `is_visible` = '0' WHERE `is_visible` = '1'
        ");
        $conn->exec("
        UPDATE `images` SET `is_visible` = '1' WHERE `is_visible` = '2'
        ");

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`image_view` (
                image_id,
                ip_address,
                created_at
                )
            SELECT 
                id_media,
                ip,
                created_at
            FROM ' . self::SOURCE_DB . '.media_pro_views
            WHERE NOT EXISTS (SELECT * FROM image_view)');

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . '.`video` (
            user_id,
            url,
            title,
            preview,
            created_at
            )
        SELECT 
            user_id,
            url,
            title, 
            image, 
            created_date
        FROM ' . self::SOURCE_DB . '.videos_user
        WHERE NOT EXISTS (SELECT * FROM video)');

        /*$conn->exec("
        UPDATE IGNORE ".self::CURRENT_DB.".`images` SET count_view =
        (SELECT COUNT(image_id) AS count
        FROM image_view
        WHERE `images`.id = `image_view`.image_id
        GROUP BY `image_view`.image_id)");*/

        $conn->exec('
        UPDATE IGNORE ' . self::CURRENT_DB . '.`user` SET thumbnail = 
        (SELECT 
        file_name
        FROM ' . self::SOURCE_DB . '.media
        WHERE user_id = ' . self::CURRENT_DB . '.`user`.id
        ORDER BY ' . self::SOURCE_DB . '.media.id DESC LIMIT 1)');

        $conn->exec('
        REPLACE INTO ' . self::CURRENT_DB . '.`eyes_color` (id, title,is_active)
        SELECT id, title, 1
        FROM ' . self::SOURCE_DB . '.physical_eyes_color
        WHERE NOT EXISTS (SELECT * FROM eyes_color)
        ');

        $conn->exec('
        REPLACE INTO ' . self::CURRENT_DB . '.`hair_color` (id, title, is_active)
        SELECT id, title,1 
        FROM ' . self::SOURCE_DB . '.physical_hair
        WHERE NOT EXISTS (SELECT * FROM hair_color)
        ');

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . '.`physical` (
            user_id,
            gender_id,
            ethnicity_id,
            hair_color_id,
            eyes_color_id,
            size,
            sexe,
            birthday,
            weight,
            hip,
            chest,
            confection,
            pointure,
            chest_size,
            waist_size,
            chest_height,
            height_bust,
            back_height,
            shoulder_width,
            arm_length,
            arms_turn,
            round_neck,
            apn_camera,
            apn_lenses
            )
        SELECT 
            user_id,
            gender,
            origin_id,
            hair_id, 
            eyes_color_id, 
            size, 
            gender,
            NULL,
            weight,
            hip,
            chest,
            confection,
            pointure,
            chest_size,
            waist_size,
            chest_height,
            height_bust,
            back_height,
            shoulder_width,
            arm_length,
            arms_turn,
            round_neck,
            boitier,
            objectifs
        FROM ' . self::SOURCE_DB . '.user_physical
        WHERE NOT EXISTS (SELECT * FROM physical)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`release_notes` (
                user_id,
                title,
                content,
                is_active,
                created_at
                )
            SELECT 
                user_id,
                title,
                content,
                status,
                created_date
            FROM ' . self::SOURCE_DB . '.release_notes
            WHERE NOT EXISTS (SELECT * FROM release_notes)');

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . '.`address` (
            user_id,
            full_address,
            route,
            locality,
            adminstrative_area,
            country,
            postal_code
            )
        SELECT 
            id,
            autocomplete,
            NULL,
            locality_area, 
            region_area, 
            country_area, 
            postal_code_area
        FROM ' . self::SOURCE_DB . '.user
        WHERE NOT EXISTS (SELECT * FROM address)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`option` (
                user_id,
                contact_publish_medias,
                contact_share_message,
                contact_send_private_message,
                comment_image,
                follow
                )
            SELECT 
                user_id,
                bookContact,
                1,
                1, 
                1, 
                1
            FROM ' . self::SOURCE_DB . '.user_options
            WHERE NOT EXISTS (SELECT * FROM option)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`social` (
                user_id,
                facebook,
                instagram,
                tumblr,
                twitter,
                pinterest,
                linkedin,
                skype,
                website
                )
            SELECT 
                id,
                facebook,
                instagram,
                tumblr,
                twitter,
                pinterest,
                NULL,
                NULL,
                website
            FROM ' . self::SOURCE_DB . '.user
            WHERE NOT EXISTS (SELECT * FROM social)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`statistic` (
                user_id,
                created_at,
                ip_address
                )
            SELECT 
                user_id,
                created_date,
                ip_adress
            FROM ' . self::SOURCE_DB . '.user_visits_off
            WHERE NOT EXISTS (SELECT * FROM statistic)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`guestbook` (
                user_id,
                email,
                content,
                created_at,
                is_active,
                ip_address,
                author,
                location,
                website
                )
            SELECT 
                user_id,
                email,
                message,
                created_date,
                status,
                ip_adress,
                name,
                cities,
                website    
            FROM ' . self::SOURCE_DB . '.comment_profile_artists
            WHERE NOT EXISTS (SELECT * FROM guestbook)');

        $conn->exec("
        INSERT IGNORE INTO `gender_list` (`id`, `title`)
        VALUES
            (1, 'Homme'),
            (2, 'Femme'),
            (3, 'Sans aucune importance')
        ");

        $conn->exec("
        INSERT INTO `design_plan` (`design_id`, `plan_id`)
        VALUES
            (18, 2),
            (18, 1),
            (17, 2),
            (17, 1),
            (16, 2),
            (16, 1),
            (15, 2),
            (15, 1),
            (14, 3),
            (14, 2),
            (14, 1),
            (13, 3),
            (13, 2),
            (13, 1),
            (12, 3),
            (12, 2),
            (12, 1),
            (11, 3),
            (11, 2),
            (11, 1),
            (10, 3),
            (10, 2),
            (10, 1),
            (9, 3),
            (9, 2),
            (9, 1),
            (8, 3),
            (8, 2),
            (8, 1),
            (7, 3),
            (7, 2),
            (7, 1),
            (6, 3),
            (6, 2),
            (6, 1),
            (5, 3),
            (5, 2),
            (5, 1),
            (4, 3),
            (4, 2),
            (4, 1),
            (3, 3),
            (3, 2),
            (3, 1),
            (2, 3),
            (2, 2),
            (2, 1),
            (1, 3),
            (1, 2),
            (1, 1);
        ");

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . ".`annonces` (
                id,
                user_id,
                profession_id,
                gender_id,
                type,
                title,
                description,
                location,
                slug,
                created_at,
                updated_at,
                is_active
                )
            SELECT 
                id,
                user_id,
                profile_id,
                sexe_id,
                type_id,
                title,
                description,
                autocomplete,
                '',
                created_date,
                update_date,
                status
            FROM " . self::SOURCE_DB . '.annonces
            WHERE NOT EXISTS (SELECT * FROM annonces)');

        $conn->exec('
            UPDATE IGNORE ' . self::CURRENT_DB . ".`annonces` 
            SET slug = replace(trim(lower(title)), ' ', '-')");

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`annonces_view` (
                annonce_id,
                ip_address,
                created_at
                )
            SELECT 
                annonce_id,
                adresse_ip,
                created_date
            FROM ' . self::SOURCE_DB . '.annonces_views
            WHERE NOT EXISTS (SELECT * FROM annonces_view)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`annonces_comment` (
                annonce_id,
                user_id,
                comment,
                created_at,
                is_active,
                updated_at
                )
            SELECT 
                id_annonce,
                user_id,
                answer,
                created_date,
                status,
                created_date
            FROM ' . self::SOURCE_DB . '.annonces_comment_post
            WHERE NOT EXISTS (SELECT * FROM annonces_comment)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`style_photos` (
                id,
                title,
                is_active
                )
            SELECT 
                id,
                title,
                status
            FROM ' . self::SOURCE_DB . '.list_style_photos
            WHERE NOT EXISTS (SELECT * FROM style_photos)');

        $conn->exec('
            INSERT IGNORE INTO ' . self::CURRENT_DB . '.`avis` (
                user_id,
                message,
                is_active,
                is_selected,
                created_at,
                updated_at
                )
            SELECT 
                user_id,
                message,
                status,
                selected,
                created_date,
                created_date
            FROM ' . self::SOURCE_DB . '.avis_list
            WHERE NOT EXISTS (SELECT * FROM avis)');

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . ".`image_heartstroke` (
            image_id,
            created_at,
            position
            )
        SELECT 
            media_id,
            '2021-07-26 16:00:00',
            position
        FROM " . self::SOURCE_DB . '.media_pro_selected
        WHERE NOT EXISTS (SELECT * FROM image_heartstroke)');

        $conn->exec("
            INSERT INTO `image_cover` (`id`, `image_id`, `created_at`, `is_active`)
            VALUES
                (1, 68308, '2013-05-01 00:00:00', 1)");

        $conn->exec('
        INSERT IGNORE INTO ' . self::CURRENT_DB . ".`follow` (
            user_id,
            friend_id,
            notifications_interface,
            created_at
            )
        SELECT 
            id_suivi,
            id_suiveur,
            '0',
            created_date
        FROM " . self::SOURCE_DB . '.follow_users
        WHERE NOT EXISTS (SELECT * FROM follow)');

        return Command::SUCCESS;
    }
}
