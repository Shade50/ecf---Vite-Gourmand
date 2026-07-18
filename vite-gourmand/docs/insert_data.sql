INSERT INTO
    `allergene` (`id`, `label`, `description`)
VALUES (
        1,
        'Lait',
        'Présence de lait ou de produits dérivés (crème, beurre, fromage, lait en poudre, lactosérum, etc.). Peut provoquer une réaction chez les personnes allergiques aux protéines du lait.'
    ),
    (
        2,
        'Gluten',
        'Présence de céréales contenant du gluten (blé, seigle, orge, avoine...).'
    ),
    (
        3,
        'Œufs',
        'Présence d\'œufs ou de produits dérivés.'
    ),
    (
        4,
        'Arachides',
        'Présence d\'arachides ou de produits à base d\'arachides.'
    ),
    (
        5,
        'Fruits à coque',
        'Présence d\'amandes, noisettes, noix, pistaches, noix de cajou, etc.'
    ),
    (
        6,
        'Soja',
        'Présence de soja ou de produits dérivés.'
    ),
    (
        7,
        'Céleri',
        'Présence de céleri sous toutes ses formes.'
    ),
    (
        8,
        'Moutarde',
        'Présence de moutarde ou de graines de moutarde.'
    ),
    (
        9,
        'Sésame',
        'Présence de graines de sésame ou d\'huile de sésame.'
    ),
    (
        10,
        'Poissons',
        'Présence de poisson ou de produits dérivés.'
    ),
    (
        11,
        'Crustacés',
        'Présence de crustacés (crevette, crabe, homard...).'
    ),
    (
        12,
        'Mollusques',
        'Présence de moules, huîtres, calamars, etc.'
    ),
    (
        13,
        'Sulfites',
        'Présence de sulfites, souvent utilisés comme conservateurs.'
    ),
    (
        14,
        'Lupin',
        'Présence de farine ou de graines de lupin.'
    );

INSERT INTO
    `menu` (
        `id`,
        `title`,
        `description`,
        `minimum_person`,
        `price`,
        `conditions`,
        `stock`,
        `image`,
        `theme_id`
    )
VALUES (
        1,
        'Menu Tradition',
        'Un menu convivial inspiré de la cuisine française traditionnelle, élaboré à partir de produits frais et de saison. Idéal pour les repas de famille, anniversaires et événements associatifs.',
        10,
        32.90,
        'Commande à partir de 10 personnes. Réservation minimum 72 heures à l\'avance. Livraison disponible selon la zone géographique.',
        49,
        'menu-tradition.jpg',
        1
    ),
    (
        2,
        'Menu Prestige',
        'Une formule gastronomique élaborée à partir de produits nobles et de saison. Idéale pour les mariages, réceptions, repas d\'entreprise et événements haut de gamme.',
        20,
        49.90,
        'Commande à partir de 20 personnes. Réservation minimum 7 jours à l\'avance. Livraison et installation possibles selon la localisation de l\'événement.',
        30,
        'menu-prestige.jpg',
        2
    ),
    (
        3,
        'Menu Cocktail',
        'Une sélection de pièces salées et sucrées à déguster debout, idéale pour les cocktails, inaugurations, séminaires, portes ouvertes et événements professionnels.',
        15,
        24.90,
        'Commande à partir de 15 personnes. Réservation minimum 5 jours à l\'avance. Livraison disponible avec installation sur demande.',
        40,
        'menu-cocktail.jpg',
        3
    ),
    (
        4,
        'Menu Végétarien',
        'Une sélection de plats végétariens savoureux élaborés avec des légumes de saison, des céréales et des produits frais. Une formule gourmande adaptée à tous les convives.',
        10,
        29.90,
        'Commande à partir de 10 personnes. Réservation minimum 72 heures à l\'avance. Livraison disponible selon la zone géographique.',
        35,
        'menu-vegetarien.jpg',
        4
    ),
    (
        5,
        'Menu Festif',
        'Un menu d\'exception conçu pour les fêtes, mariages et grandes réceptions. Des produits raffinés et des saveurs gourmandes pour faire de chaque événement un moment inoubliable.',
        20,
        59.90,
        'Commande à partir de 20 personnes. Réservation minimum 10 jours à l\'avance. Livraison, installation et service possibles selon la prestation choisie.',
        25,
        'menu-festif.jpg',
        6
    );

INSERT INTO
    `menu_plat` (`menu_id`, `plat_id`)
VALUES (1, 1),
    (1, 5),
    (1, 6),
    (2, 1),
    (2, 3),
    (2, 4),
    (3, 1),
    (3, 4),
    (3, 5),
    (4, 5),
    (5, 1),
    (5, 3),
    (5, 4);

INSERT INTO
    `order` (
        `id`,
        `status`,
        `create_at`,
        `total_price`,
        `delivery_date`,
        `number_of_people`,
        `delivery_adresse`,
        `user_id`,
        `menu_id`
    )
VALUES (
        1,
        'En attente',
        '2026-07-17 16:52:54',
        38.92,
        '2026-07-17 18:51:00',
        10,
        '12 Cours de l\'Intendance 33000 Bordeaux',
        1,
        1
    );

INSERT INTO
    `plat` (
        `id`,
        `title`,
        `description`,
        `type`,
        `regime`
    )
VALUES (
        1,
        'Suprême de volaille fermière, crème forestière',
        'Suprême de volaille fermière rôti, accompagné d\'une sauce crémeuse aux champignons de saison. Servi avec un gratin dauphinois et une poêlée de légumes frais.',
        'Plat principal',
        'Sans porc'
    ),
    (
        2,
        'Suprême de volaille fermière, crème forestière',
        'Suprême de volaille fermière rôti, accompagné d\'une sauce crémeuse aux champignons de saison. Servi avec un gratin dauphinois et une poêlée de légumes frais.',
        'Plat principal',
        'Sans porc'
    ),
    (
        3,
        'Filet de bœuf, sauce au poivre',
        'Filet de bœuf français grillé, nappé d\'une sauce au poivre concassé, accompagné d\'un gratin dauphinois et de légumes de saison.',
        'Plat principal',
        'Standard'
    ),
    (
        4,
        'Dos de saumon rôti, beurre citronné',
        'Dos de saumon rôti au four, servi avec un beurre citronné, un riz parfumé et une julienne de légumes croquants.',
        'Plat principal',
        'Pescétarien'
    ),
    (
        5,
        'Risotto crémeux aux légumes du soleil',
        'Risotto crémeux au parmesan accompagné de légumes de saison rôtis et de copeaux de parmesan.',
        'Plat principal',
        'Végétarien'
    ),
    (
        6,
        'Lasagnes maison à la bolognaise',
        'Lasagnes préparées avec une sauce bolognaise mijotée, une béchamel maison et un mélange de fromages gratinés.',
        'Plat principal',
        'Standard'
    );

INSERT INTO
    `plat_allergene` (`plat_id`, `allergene_id`)
VALUES (2, 1),
    (2, 2),
    (3, 1),
    (4, 1),
    (4, 10),
    (5, 1),
    (6, 1),
    (6, 2),
    (6, 3);

INSERT INTO
    `role` (`id`, `libelle`)
VALUES (1, 'ROLE_USER'),
    (2, 'ROLE_EMPLOYEE'),
    (3, 'ROLE_ADMIN');

INSERT INTO
    `site_settings` (
        `id`,
        `site_name`,
        `email`,
        `phone`,
        `address`,
        `postal_code`,
        `city`,
        `opening_hours_week`,
        `opening_hours_saturday`,
        `opening_hours_sunday`
    )
VALUES (
        1,
        'Vite & Gourmand',
        'contact@vite-gourmand.fr',
        '0612345678',
        'Place de la Bourse',
        '33000',
        'Bordeaux',
        '08:00 - 12:00 et 13:00 - 18:00',
        '08:00 - 13:00',
        'Fermé'
    );

INSERT INTO
    `theme` (`id`, `label`, `description`)
VALUES (
        1,
        'Menu Tradition',
        'Un menu authentique mettant à l\'honneur les recettes traditionnelles françaises, élaboré à partir de produits de saison et de qualité. Idéal pour les repas de famille, anniversaires et événements conviviaux.'
    ),
    (
        2,
        'Menu Prestige',
        'Une sélection raffinée de mets gastronomiques préparés avec des produits d\'exception pour sublimer vos réceptions et événements haut de gamme.'
    ),
    (
        3,
        'Menu Cocktail',
        'Une formule composée de pièces salées et sucrées, parfaite pour les cocktails, inaugurations, séminaires et événements professionnels.'
    ),
    (
        4,
        'Menu Végétarien',
        'Un menu équilibré et savoureux composé exclusivement de plats végétariens, mettant en valeur les légumes et produits de saison.'
    ),
    (
        5,
        'Menu Enfant',
        'Une formule adaptée aux plus jeunes, composée de plats simples, gourmands et équilibrés.'
    ),
    (
        6,
        'Menu Festif',
        'Une sélection de plats spécialement imaginés pour les fêtes de fin d\'année, mariages et grandes réceptions.'
    );

INSERT INTO
    `user` (
        `id`,
        `email`,
        `password`,
        `nom`,
        `prenom`,
        `gsm`,
        `adresse_postale`,
        `role_id`,
        `is_active`
    )
VALUES (
        1,
        'admin@vite-gourmand.fr',
        '$2y$13$BaHUfYsXutnN1Nk0ck.gbeoNmoZa./hENtWkodXVNBWWfRMQKsgem',
        'Administrateur',
        'Alex',
        '0600000000',
        '123 rue de la Paix, 33000 Bordeaux',
        3,
        1
    ),
    (
        2,
        'user@vite-gourmand.fr',
        '$2y$13$t3lUnC2vGByD2rFLCWJX.OHRLu7s0GCIdfXbQNw2NRFhsU.NjZkt2',
        'space',
        'Robert',
        '0612345678',
        '60 place central 33000 Bordeaux',
        1,
        1
    );