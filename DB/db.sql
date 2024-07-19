-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour localement_suisse
CREATE DATABASE IF NOT EXISTS `localement_suisse` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `localement_suisse`;

-- Listage de la structure de table localement_suisse. blogs
CREATE TABLE IF NOT EXISTS `blogs` (
  `blo_id` int NOT NULL AUTO_INCREMENT,
  `blo_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `blo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `blo_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`blo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.blogs : ~1 rows (environ)
INSERT INTO `blogs` (`blo_id`, `blo_date`, `blo_title`, `blo_text`) VALUES
	(1, '2022.10.19', 'Les 5 valeurs de consommer local', 'Quelles sont les choses à savoir en consommer local ?\r\n\r\nPourquoi acheter des produits locaux ?\r\n\r\nDans cet article nous trouverons cinq importances de consommer local.\r\n\r\n![](https://files.catbox.moe/orkbjp.png)\r\n\r\n### Table des matières\r\n  1. [Rien que pour votre santé](#rien-que-pour-votre-santé)\r\n  1. [Faire tourner l\'économie locale](#faire-tourner-léconomie-locale)\r\n  1. [Encourager la relève](#encourager-la-relève)\r\n  1. [Créativité et originalité](#créativité-et-originalité)\r\n  1. [Milieu dynamique](#milieu-dynamique)\r\n\r\n## Rien que pour votre santé\r\nEn achetant des produits locaux, vous aurez la certitude de vous procurer la meilleure qualité.\r\n\r\nEffectivement, les articles sont confectionnés de manière étique, à la main et avec les normes qui sont en vigueur en suisse.\r\n\r\nLe plus important c\'est la passion et le développement dont les auteurs font preuve et qui rajoutent une dose de bienveillance.\r\n\r\n\r\nMais pas seulement, les produits ne contiennent pas de particules nocives et néfastes pour votre santé. Le local est pour la plupart du temps naturel, bio et non traité car les moyens de fabrication sont évidemment moins évolués que dans des grandes industries.\r\n\r\nPour notre plus grand bonheur, en plus de contribuer à votre bien-être, ils vous apporteront satisfaction en terme d\'odeur, de goût et d\'apport de résultat sur votre santé.\r\n## Faire tourner l\'économie locale\r\nAcheter local s’est acheter de manière responsable.\r\n\r\nNous invitons les producteurs, les artisans et les commerçants à se mettre en avant. Se donner les moyens de pouvoir se faire connaître, de vendre leurs produits au public, de pouvoir vous intéresser à la manière, au contrainte, à leur défi, leur sueur et surtout la satisfaction dont ces fabricants font preuve ! Ainsi vous connaîtrez la valeur de ce que vous consommez.\r\n\r\nC\'est qu\'alors que leurs affaires pourront se développer et qu\'un accent en plus sera mis sur la qualité.\r\nDévelopper l\'économie locale, c\'est faire circuler l\'argent dans notre pays pour les collectivités, contrairement au bénéfice engendré par les entreprises étrangères.\r\n## Encourager la relève\r\nLes nouvelles générations ont besoin de s\'inspirer et de leadership.\r\n\r\nQuand ils entendent qu\'une entreprise est en pleine essor, les jeunes s\'intéressent plus à l\'entrepreneuriat.\r\nIls doivent prendre modèle sur nos générations. De plus, ce sont les entreprises de demain qui donneront du travail à vos enfants, vos petits-enfants, etc.\r\n## Créativité et originalité\r\nCe sont des projets de vie qui donnent de l\'énergie, de la sueur et où la plupart (si ce n\'est tout) de leur temps y est consacré, leur entreprise est considérée comme leur bébé.\r\nIl est d\'autant plus admirable de les voir y contribuer avec passion à la création de leurs produits et de pouvoir donner le mérite de les faire continuer dans leur mission.\r\n## Milieu dynamique\r\nLa création des produits artisanaux contribue au maintien et à l\'identité culturelle des villes et des régions. Donne de la valeur, de la personnalité, des couleurs et un endroit où il fait bon vivre.');

-- Listage de la structure de table localement_suisse. carts
CREATE TABLE IF NOT EXISTS `carts` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `cart_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cart_total` int DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `cookie_id` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `status_id` int NOT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `FK_carts_status` (`status_id`),
  CONSTRAINT `FK_carts_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.carts : ~2 rows (environ)
INSERT INTO `carts` (`cart_id`, `cart_date`, `cart_total`, `user_id`, `cookie_id`, `status_id`) VALUES
	(7, '2024-06-28', 5480, NULL, 'e24cb1b1eeee60da0bb5fc11df6638d2', 1),
	(8, '2024-06-28', 12300, 1, '2b4ca24193a30a6136dae7aa1649ab0f', 1);

-- Listage de la structure de table localement_suisse. categories
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `AK_categories` (`cat_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.categories : ~2 rows (environ)
INSERT INTO `categories` (`cat_id`, `cat_name`) VALUES
	(2, 'Nourriture'),
	(1, 'vêtements');

-- Listage de la structure de table localement_suisse. cats_products
CREATE TABLE IF NOT EXISTS `cats_products` (
  `pro_id` int NOT NULL,
  `subcat_id` int NOT NULL,
  PRIMARY KEY (`pro_id`,`subcat_id`),
  KEY `FK_cats_products_subcategories` (`subcat_id`),
  CONSTRAINT `FK_cats_products_products` FOREIGN KEY (`pro_id`) REFERENCES `products` (`pro_id`),
  CONSTRAINT `FK_cats_products_subcategories` FOREIGN KEY (`subcat_id`) REFERENCES `subcategories` (`subcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.cats_products : ~22 rows (environ)
INSERT INTO `cats_products` (`pro_id`, `subcat_id`) VALUES
	(3, 1),
	(19, 1),
	(20, 1),
	(22, 1),
	(23, 1),
	(2, 2),
	(4, 2),
	(9, 2),
	(18, 2),
	(23, 2),
	(1, 3),
	(9, 3),
	(18, 3),
	(19, 3),
	(21, 3),
	(24, 3),
	(1, 4),
	(8, 4),
	(20, 4),
	(21, 4),
	(22, 4),
	(23, 4),
	(24, 4);

-- Listage de la structure de table localement_suisse. faqs
CREATE TABLE IF NOT EXISTS `faqs` (
  `faq_id` int NOT NULL AUTO_INCREMENT,
  `faq_title_fr` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `faq_title_de` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `faq_title_en` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `faq_title_it` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `faq_text_fr` text COLLATE utf8mb4_bin NOT NULL,
  `faq_text_de` text COLLATE utf8mb4_bin NOT NULL,
  `faq_text_en` text COLLATE utf8mb4_bin NOT NULL,
  `faq_text_it` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.faqs : ~17 rows (environ)
INSERT INTO `faqs` (`faq_id`, `faq_title_fr`, `faq_title_de`, `faq_title_en`, `faq_title_it`, `faq_text_fr`, `faq_text_de`, `faq_text_en`, `faq_text_it`) VALUES
	(1, 'Pourquoi vendre mes articles en ligne ?', 'Warum meine Artikel online verkaufen?', 'Why sell my items online ?', 'Perché vendere i miei articoli online?', 'Vendre vos articles en ligne présente de nombreux avantages comme le gain de temps et la possibilité de toucher de nouveaux clients. Vos articles sont disponibles 24h/24 et 7j/7', 'Der Online-Verkauf Ihrer Artikel hat viele Vorteile, z. B. Zeitersparnis und die Möglichkeit, neue Kunden zu erreichen. Ihre Artikel sind rund um die Uhr verfügbar', 'Selling your items online has many advantages such as saving time and allowing you to reach new customers. Your items are available 24/7', 'Vendere i tuoi articoli online presenta molti vantaggi come risparmiare tempo e permetterti di raggiungere nuovi clienti. I tuoi articoli sono disponibili 24 ore su 24, 7 giorni su 7'),
	(2, 'Pourquoi choisir votre plateforme ?', 'Warum sollten Sie Ihre Plattform wählen?', 'Why choose your platform ?', 'Perché scegliere la tua piattaforma?', 'Notre plateforme est innovante, sécurisée et regroupe tous les produits et articles suisses en tout genre (vêtements, cosmétiques, alimentation, etc.). Vous toucherez plus de clients grâce à notre visibilité sur les réseaux sociaux et la publicité. Promouvoir et vendre vos produits est une priorité pour nous. La plateforme est simple d\'utilisation et disponible en 4 langues (français, anglais, allemand et italien).', 'Unsere Plattform ist innovativ, sicher und vereint alle Schweizer Produkte und Artikel aller Art (Kleidung, Kosmetik, Lebensmittel usw.). Dank unserer Sichtbarkeit in sozialen Netzwerken und Werbung erreichen Sie mehr Kunden. Die Förderung und der Verkauf Ihrer Produkte haben für uns Priorität. Die Plattform ist einfach zu bedienen und in 4 Sprachen verfügbar (Französisch, Englisch, Deutsch und Italienisch).', 'Our platform is innovative, secure and brings together all Swiss products and articles of all kinds (clothing, cosmetics, food, etc.) You will reach more customers thanks to our visibility on social networks and advertising. Promoting and selling your products is a priority for us. The platform is easy to use and available in 4 languages (French, English, German and Italian).', 'La nostra piattaforma è innovativa, sicura e riunisce tutti i prodotti e articoli svizzeri di ogni tipo (abbigliamento, cosmetici, cibo, ecc.). Raggiungerai più clienti grazie alla nostra visibilità sui social network e alla pubblicità. Promuovere e vendere i vostri prodotti è per noi una priorità. La piattaforma è facile da usare e disponibile in 4 lingue (francese, inglese, tedesco e italiano).'),
	(3, 'L\'inscription est-elle gratuite ?', 'Ist die Registrierung kostenlos?', 'Is the registration free ?', 'La registrazione è gratuita?', 'Non, l\'accès vendeur à la plateforme coûte 5.-/mois. Et Stripe (la plateforme qui vous permettra de recevoir votre argent) prend 2% de commission.', 'Nein, der Verkäuferzugang zur Plattform kostet 5,-/Monat. Und Stripe (die Plattform, über die Sie Ihr Geld erhalten) nimmt 2 % Provision.', 'No, the seller access of the platform costs 5.-/month. And stripe (the platform that will allow you to receive your money) takes 2% commission.', 'No, l\'accesso venditore alla piattaforma costa 5.-/mese. E stripe (la piattaforma che ti permetterà di ricevere i tuoi soldi) prende una commissione del 2%.'),
	(4, 'Y a-t-il une limite de produits que nous pouvons ajouter ?', 'Gibt es eine Begrenzung der Produkte, die wir hinzufügen können?', 'Is there a limit of products we can add?', 'C\'è un limite di prodotti che possiamo aggiungere?', 'Non, il n\'y a pas de limite.', 'Nein, es gibt keine Begrenzung.', 'No, there is no limit.', 'No, non c\'è limite.'),
	(5, 'Que pouvez-vous vendre sur notre plateforme ?', 'Was können Sie auf unserer Plattform verkaufen?', 'What can you sell on our platform?', 'Cosa puoi vendere sulla nostra piattaforma?', 'Vous pouvez vendre toutes vos créations, articles et produits créés en Suisse. Pour la vente de boissons alcoolisées, vous devez être en possession d\'un brevet. Pour la vente d\'articles pour bébé, vos articles doivent être vérifiés et aux normes.', 'Sie können alle Ihre Kreationen, Artikel und Produkte verkaufen, die in der Schweiz hergestellt wurden. Für den Verkauf von alkoholischen Getränken müssen Sie im Besitz eines Patents sein. Für den Verkauf von Babyartikeln müssen Ihre Artikel geprüft und den Standards entsprechen.', 'You can sell all your creations, articles and products that have been created in Switzerland. For the sale of alcoholic beverages, you must be in possession of a patent. For the sale of baby items, your items must be checked and up to standard.', 'Puoi vendere tutte le tue creazioni, articoli e prodotti che sono stati creati in Svizzera. Per la vendita di bevande alcoliche è necessario essere in possesso di un brevetto. Per la vendita di articoli per bambini, i tuoi articoli devono essere controllati e conformi agli standard.'),
	(6, 'Puis-je vendre mes produits si je ne les ai pas fabriqués en Suisse ?', 'Kann ich meine Produkte verkaufen, wenn ich sie nicht in der Schweiz hergestellt habe?', 'Can I sell my products if I haven’t made them in Switzerland?', 'Posso vendere i miei prodotti se non li ho realizzati in Svizzera?', 'Cette plateforme est conçue exclusivement pour les artisans, designers et détaillants suisses. Vos articles/produits doivent être fabriqués en Suisse.', 'Diese Plattform richtet sich ausschliesslich an Schweizer Handwerker, Designer und Händler. Ihre Artikel/Produkte müssen in der Schweiz hergestellt sein.', 'This platform is designed exclusively for Swiss craftsmen, designers and retailers. Your articles/products must be made in Switzerland.', 'Questa piattaforma è progettata esclusivamente per artigiani, designer e rivenditori svizzeri. I tuoi articoli/prodotti devono essere fabbricati in Svizzera.'),
	(7, 'Puis-je gérer seul mon stock ?', 'Kann ich meinen Bestand alleine verwalten?', 'Can I manage my stock alone ?', 'Posso gestire il mio stock da solo?', 'En ayant une boutique en ligne sur notre plateforme, vous êtes en charge de la gestion de votre inventaire et de l\'envoi de vos commandes.', 'Mit einem Online-Shop auf unserer Plattform sind Sie für die Verwaltung Ihres Lagerbestands und das Versenden Ihrer Bestellungen verantwortlich.', 'By having an online store on our platform, you are in charge of managing your inventory and sending your orders.', 'Avendo un negozio online sulla nostra piattaforma, sei responsabile della gestione del tuo inventario e dell\'invio dei tuoi ordini.'),
	(8, 'Comment se passe le système de rémunération ?', 'Wie sieht das Vergütungssystem aus?', 'How is the remuneration system going ?', 'Come va il sistema retributivo?', 'A chaque vente les fonds vous sont automatiquement versés via Stripe. Vous avez donc besoin d\'un compte Stripe créé. Pas de panique lors de la création de votre boutique sur notre plateforme tout est expliqué. A savoir : stripe prélève 2% de commission sur chaque vente.', 'Bei jedem Verkauf wird Ihnen das Geld automatisch über Stripe ausgezahlt. Sie müssen also ein Stripe-Konto erstellen. Keine Panik, bei der Erstellung Ihres Shops auf unserer Plattform wird alles erklärt. Zu wissen: Stripe erhält 2 % Provision auf jeden Verkauf.', 'At each sale the funds are automatically paid to you via Stripe. So you need a stripe account created. Do not panic when creating your shop on our platform everything is explained. To know: stripe takes 2% commission on each sale.', 'Ad ogni vendita i fondi ti vengono automaticamente pagati tramite Stripe. Quindi è necessario creare un account stripe. Niente panico quando crei il tuo negozio sulla nostra piattaforma è tutto spiegato. Da sapere: stripe prende una commissione del 2% su ogni vendita.'),
	(9, 'Est-ce que je reçois l’argent directement sur mon compte après une vente ?', 'Bekomme ich das Geld nach einem Verkauf direkt auf mein Konto?', 'Do I receive the money directly in my account after a sale?', 'Riceverò il denaro direttamente sul mio conto dopo una vendita?', 'Non, Stripe conserve l\'argent 7 jours avant de l\'envoyer sur votre compte bancaire', 'Nein, Stripe hält das Geld 7 Tage lang zurück, bevor es auf Ihr Bankkonto überwiesen wird.', 'No, Stripe holds the money 7 days before sending it to your bank accoun', 'No, Stripe trattiene il denaro 7 giorni prima di inviarlo al tuo conto bancario'),
	(10, 'Puis-je choisir si j\'envoie mon colis par courrier ou s\'il est à récupérer sur place ?', 'Kann ich wählen, ob ich mein Paket per Post versende oder ob es vor Ort abzuholen ist?', 'Can I choose if I send my package by mail or if it is to pick up on site?', 'Posso scegliere se spedire il pacco tramite posta o se ritirarlo in loco?', 'Non, vous êtes responsable de l\'envoi de chaque colis.', 'Nein, Sie sind für den Versand jedes einzelnen Pakets verantwortlich.', 'No, you are in charge of sending each package.', 'No, sei responsabile della spedizione di ogni pacco.'),
	(11, 'Dois-je afficher l\'adresse de mon magasin sachant que je le fais à mon domicile ?', 'Muss ich die Adresse meines Geschäfts angeben, wenn ich weiß, dass ich dies bei mir zu Hause mache?', 'Do I have to display my store address knowing that I am doing this at my home?', 'Devo mostrare l\'indirizzo del mio negozio sapendo che lo sto facendo a casa mia?', 'Non, lors de la création de votre profil vous pouvez choisir d\'afficher ou non votre adresse.', 'Nein, Sie können bei der Erstellung Ihres Profils wählen, ob Ihre Adresse angezeigt werden soll oder nicht.', 'No, when creating your profile you can choose whether or not to display your address.', 'No, al momento della creazione del tuo profilo puoi scegliere se visualizzare o meno il tuo indirizzo.'),
	(12, 'Puis-je suspendre ma boutique ?', 'Kann ich meinen Shop pausieren?', 'Can I pause my store?', 'Posso mettere in pausa il mio negozio?', 'Oui, pour cela il vous suffit de mettre votre boutique en statut fermé et de couper votre abonnement.', 'Ja, dazu müssen Sie lediglich Ihren Shop auf „Geschlossen“ setzen und Ihr Abonnement kündigen.', 'Yes, for that you just have to put your store in closed status and cut your subscription.', 'Sì, per questo devi solo mettere il tuo negozio in stato chiuso e interrompere l\'abbonamento.'),
	(13, 'Puis-je modifier mon article à tout moment ?', 'Kann ich meinen Artikel jederzeit bearbeiten?', 'Can I edit my item at any time?', 'Posso modificare il mio articolo in qualsiasi momento?', 'Oui sans problème depuis votre tableau de bord vendeur.', 'Ja, ohne Probleme über Ihr Verkäufer-Dashboard.', 'Yes without problems from your seller dashboard.', 'Sì, senza problemi dalla dashboard del venditore.'),
	(14, 'Puis-je ajouter des options à mon produit (taille, couleurs, etc.)', 'Kann ich meinem Produkt Optionen hinzufügen (Größe, Farben usw.)', 'Can I add options to my product (size, colors, etc.)', 'Posso aggiungere opzioni al mio prodotto (dimensioni, colori, ecc.)', 'Oui, vous pouvez ajouter des variantes à votre produit.', 'Ja, Sie können Ihrem Produkt Varianten hinzufügen.', 'Yes, you can add variants to your product.', 'Sì, puoi aggiungere varianti al tuo prodotto.'),
	(15, 'Je vends des boissons alcoolisées, puis-je être sur votre plateforme ?', 'Ich verkaufe alkoholische Getränke, kann ich auf Ihrer Plattform sein?', 'I sell alcoholic beverages, can I be on your platform?', 'Vendo bevande alcoliche, posso essere sulla vostra piattaforma?', 'Oui, à condition d’avoir le permis de vendre de l’alcool. Merci de l\'envoyer à l\'e-mail suivant:  support@localement-suisse.ch', 'Ja, sofern Sie eine Lizenz zum Alkoholverkauf haben. Bitte senden Sie diese an folgende E-Mail:  support@localement-suisse.ch', 'Yes, as long as you have the license to sell alcohol. Please send it to the following e-mail: support@localement-suisse.ch', 'Sì, purché tu abbia la licenza per vendere alcolici. Si prega di inviarlo al seguente indirizzo e-mail:  support@localement-suisse.ch'),
	(16, 'Comment sont gérés les frais de port ? Pouvons-nous livrer à l\'étranger ?', 'Wie werden die Versandkosten geregelt? Können wir ins Ausland liefern?', 'How are shipping costs managed ? Can we deliver abroad ?', 'Come vengono gestite le spese di spedizione? Possiamo consegnare all\'estero?', 'A vous de définir les frais de livraison en créant un tableau de livraison en fonction du poids de vos articles. Depuis notre plateforme vous pouvez livrer dans les pays suivants :\r\n<ul>\r\n<li>Suisse</li>\r\n<li>France</li>\r\n<li>Portugal</li>\r\n<li>Espagne</li>\r\n<li>Italie</li>\r\n<li>Allemagne</li>\r\n<li>Belgique</li>\r\n</ul>', 'Es liegt an Ihnen, die Versandkosten festzulegen, indem Sie eine Liefertabelle entsprechend dem Gewicht Ihrer Artikel erstellen. Von unserer Plattform aus können Sie in die folgenden Länder liefern:\r\n\r\n<ul>\r\n<li>Schweiz</li>\r\n<li>Frankreich</li>\r\n<li>Portugal</li>\r\n<li>Spanien</li>\r\n<li>Italien</li>\r\n<li>Deutschland</li>\r\n<li>Belgien</li>\r\n</ul>', 'It is up to you to define the delivery costs by creating a delivery table according to the weight of your items. From our platform you can deliver to the following countries :\r\n\r\n<ul>\r\n<li>Switzerland</li>\r\n<li>France</li>\r\n<li>Portugal</li>\r\n<li>Spain</li>\r\n<li>Italy</li>\r\n<li>Germany</li>\r\n<li>Belgium</li>\r\n</ul>', 'Sta a te definire i costi di consegna creando una tabella di consegna in base al peso dei tuoi articoli. Dalla nostra piattaforma puoi consegnare nei seguenti paesi:\r\n\r\n<ul>\r\n<li>Svizzera</li>\r\n<li>Francia</li>\r\n<li>Portogallo</li>\r\n<li>Spagna</li>\r\n<li>Italia</li>\r\n<li>Germania</li>\r\n<li>Belgio</li>\r\n</ul>'),
	(17, 'Est-ce que je reçois un email lorsqu\'un client achète un de mes articles pour être averti ?', 'Bekomme ich eine E-Mail, wenn ein Kunde einen meiner Artikel kauft, um benachrichtigt zu werden?', 'Do I receive an email when a customer buys one of my items to be notified?', 'Ricevo un\'e-mail quando un cliente acquista uno dei miei articoli per essere avvisato?', 'Oui, vous êtes averti de chaque vente par email.', 'Ja, Sie werden über jeden Verkauf per E-Mail benachrichtigt.', 'Yes, you are notified of each sale by email.', 'Sì, vieni avvisato di ogni vendita via e-mail.'),
	(18, 'Puis-je mettre des codes promo ou des articles en promotion ?', 'Kann ich Promo-Codes oder Sonderangebote anbieten?', 'Can I put promo codes or articles on promotion?', 'Posso inserire codici promozionali o articoli in promozione?', 'Sur notre plateforme, vous pouvez ajouter des codes promotionnels et réduire vos articles.', 'Auf unserer Plattform können Sie Aktionscodes hinzufügen und Rabatte auf Ihre Artikel gewähren.', 'On our platform, you can add promo codes and discount your items.', 'Sulla nostra piattaforma puoi aggiungere codici promozionali e scontare i tuoi articoli.'),
	(19, 'Qui puis-je contacter si j\'ai des questions ?', 'An wen kann ich mich bei Fragen wenden?', 'Who can I contact if I have any questions?', 'Chi posso contattare se ho domande?', 'Vous pouvez envoyer un e-mail à notre adresse : support@localement-suisse.ch Vous recevrez une réponse dans les 24 heures.', 'Sie können eine E-Mail an unsere Adresse senden: support@localement-suisse.ch. Sie erhalten innerhalb von 24 Stunden eine Antwort.', 'You can send an e-mail to our address: support@localement-suisse.ch You will have a reply within 24 hours.', 'Potete inviare una e-mail al nostro indirizzo: support@localement-suisse.ch Riceverete una risposta entro 24 ore.');

-- Listage de la structure de table localement_suisse. favorites
CREATE TABLE IF NOT EXISTS `favorites` (
  `user_id` int NOT NULL,
  `pro_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`pro_id`),
  KEY `FK_favorites_products` (`pro_id`),
  CONSTRAINT `FK_favorites_products` FOREIGN KEY (`pro_id`) REFERENCES `products` (`pro_id`),
  CONSTRAINT `FK_favorites_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.favorites : ~0 rows (environ)
INSERT INTO `favorites` (`user_id`, `pro_id`) VALUES
	(1, 9);

-- Listage de la structure de table localement_suisse. images
CREATE TABLE IF NOT EXISTS `images` (
  `img_id` int NOT NULL AUTO_INCREMENT,
  `img_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `img_first` tinyint NOT NULL DEFAULT '0',
  `pro_id` int NOT NULL,
  PRIMARY KEY (`img_id`),
  UNIQUE KEY `AK_images` (`img_name`),
  KEY `FK_images_products` (`pro_id`),
  CONSTRAINT `FK_images_products` FOREIGN KEY (`pro_id`) REFERENCES `products` (`pro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.images : ~25 rows (environ)
INSERT INTO `images` (`img_id`, `img_name`, `img_first`, `pro_id`) VALUES
	(1, '6_6661d581b88bc6.30804716', 1, 6),
	(2, '6_6661d581b91272.05292452', 0, 6),
	(3, '6_6661d581b96989.62259143', 0, 6),
	(4, '6_6661d581b9e409.77901769', 0, 6),
	(5, '02_555', 1, 2),
	(6, '7_6669a274c8b038.71425393', 1, 7),
	(8, '9_6669a612dcc536.64295691', 1, 9),
	(9, '10_6669c9e192b019.88522001', 1, 10),
	(10, '10_6669c9e19328b4.16759259', 0, 10),
	(11, '10_6669c9e1937e70.63531779', 0, 10),
	(12, '5_6669d2b07c6873.37935272', 1, 5),
	(16, '8_6669eb0fa82320.97457666', 1, 8),
	(37, '2389e0586346cb903d00b73d9f5cc85b', 1, 18),
	(38, '18_667932ee5d72e5.89448874', 0, 18),
	(39, 'b078d35a692f92db790e2a7c98b8042f', 1, 19),
	(40, '75590932dc8e7e59b1482cf367c5734d', 0, 19),
	(41, 'bf2db38cb4578478c74b391702f63679', 0, 19),
	(42, 'dac50a3b6b908fec9f24e6ff435d1ba7', 1, 20),
	(43, '94b0e6574098df8653fdb8c157131742', 1, 21),
	(44, '88b63dc0b141cce8941679c16d78ec70', 1, 22),
	(45, '923546961707fd01ff96f4c1ea2911f9', 1, 23),
	(46, '9a263c838dbeab86d0fc8ab585c7ee4f', 1, 24),
	(47, '13f8524f5d1de93578ac2fddb0bbb909', 0, 24),
	(48, '1fac255511eaec7c19d8ca4a19e0e13f', 0, 24),
	(49, 'd1f3dc4a80d5d09a47f4c9a5e5a1fe4b', 0, 24),
	(50, '6868315bb9ff84c3dd6915963eec0fa3', 0, 24);

-- Listage de la structure de table localement_suisse. products
CREATE TABLE IF NOT EXISTS `products` (
  `pro_id` int NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `pro_price` int NOT NULL,
  `pro_in_sale` tinyint(1) DEFAULT '0',
  `pro_sale_price` int DEFAULT NULL,
  `pro_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `pro_stock` int NOT NULL DEFAULT '0',
  `pro_is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `pro_date_added` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `seller_id` int NOT NULL,
  PRIMARY KEY (`pro_id`),
  KEY `FK_products_seller` (`seller_id`),
  CONSTRAINT `FK_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.products : ~17 rows (environ)
INSERT INTO `products` (`pro_id`, `pro_name`, `pro_price`, `pro_in_sale`, `pro_sale_price`, `pro_description`, `pro_stock`, `pro_is_closed`, `pro_date_added`, `seller_id`) VALUES
	(1, 'Mon premier produit', 2000, 0, NULL, 'Ceci est probablement un produit', 50, 0, '2024-05-30', 1),
	(2, '2ème produit', 10000, 0, NULL, 'Meilleur produit ever', 15, 0, '2024-03-12', 1),
	(3, 'ce produit est nul', 100, 0, NULL, 'Null', 2, 0, '2024-05-29', 1),
	(4, 'produit du 2ème vendeur', 5000, 0, NULL, 'Incroyable non', 150, 0, '2024-05-30', 2),
	(5, 'Ceci est un test NON', 3475, 0, NULL, 'NOOON, je plaisant.\r\n\r\nCeci est beau.\r\n\r\nlol', 35, 0, '2024-06-06', 3),
	(6, 'Ceci est un test 2', 3470, 0, NULL, 'NOOON, je plaisant.\r\n\r\nCeci est beau.', 7, 0, '2024-06-06', 3),
	(7, 'Une crême', 5570, 0, NULL, 'Ceci est une bonne crème. Faites moi confiance, je suis honnête.\r\n\r\nMon nom est "Honnête".', 32, 0, '2024-06-12', 3),
	(8, 'une fourchette', 12345, 0, NULL, 'UNE FOURCHETTE!', 1, 0, '2024-06-12', 3),
	(9, 'Un humain', 500, 0, NULL, 'lol', 7, 0, '2024-06-12', 3),
	(10, 'LOL', 4500, 0, NULL, 'abc def gef', 4, 0, '2024-06-12', 3),
	(18, 'aaaaa', 43500, 0, NULL, '45345', 55, 0, '2024-06-24', 3),
	(19, 'test mini 3', 5480, 0, NULL, 'BONJOUR LES GENS, lol.', 76, 0, '2024-06-24', 3),
	(20, 'lolololo', 55600, 0, NULL, 'sedfsdfs', 5, 0, '2024-06-28', 3),
	(21, 'kol', 4500, 0, NULL, 'asdf', 444, 0, '2024-06-28', 3),
	(22, 'kawai', 123400, 0, NULL, 'ffff', 65, 0, '2024-06-28', 3),
	(23, '12 corails', 454400, 1, 12300, 'trt', 66, 0, '2024-06-28', 3),
	(24, 'America', 5, 0, NULL, 'America!', 1, 0, '2024-06-28', 3);

-- Listage de la structure de table localement_suisse. products_carts
CREATE TABLE IF NOT EXISTS `products_carts` (
  `pro_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `pro_cart_id` int NOT NULL AUTO_INCREMENT,
  `quantity` int DEFAULT NULL,
  `price` int DEFAULT NULL,
  PRIMARY KEY (`pro_id`,`cart_id`),
  UNIQUE KEY `AK_products_carts` (`pro_cart_id`),
  KEY `FK_products_carts_carts` (`cart_id`),
  CONSTRAINT `FK_products_carts_carts` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`),
  CONSTRAINT `FK_products_carts_products` FOREIGN KEY (`pro_id`) REFERENCES `products` (`pro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.products_carts : ~3 rows (environ)
INSERT INTO `products_carts` (`pro_id`, `cart_id`, `pro_cart_id`, `quantity`, `price`) VALUES
	(19, 6, 9, 1, 5480),
	(19, 7, 10, 1, 5480),
	(22, 5, 8, 1, 123400),
	(23, 8, 17, 1, 12300);

-- Listage de la structure de table localement_suisse. sellers
CREATE TABLE IF NOT EXISTS `sellers` (
  `seller_id` int NOT NULL AUTO_INCREMENT,
  `seller_uniqid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `seller_mail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_name` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `seller_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_tel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `seller_address_street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_address_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_address_canton` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_address_visible` tinyint DEFAULT '1',
  `seller_is_closed` tinyint NOT NULL DEFAULT '0',
  `seller_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_is_activated` tinyint DEFAULT '0',
  `seller_socials` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `seller_date_added` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `seller_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`seller_id`),
  UNIQUE KEY `seller_mail` (`seller_mail`),
  UNIQUE KEY `seller_uniqid` (`seller_uniqid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.sellers : ~3 rows (environ)
INSERT INTO `sellers` (`seller_id`, `seller_uniqid`, `seller_mail`, `seller_name`, `seller_password`, `seller_tel`, `seller_address_street`, `seller_address_city`, `seller_address_canton`, `seller_address_visible`, `seller_is_closed`, `seller_bio`, `seller_is_activated`, `seller_socials`, `seller_date_added`, `seller_img`) VALUES
	(1, '123', 'monmail@hotmail.com', 'Françis', '$argon2id$v=19$m=65536,t=4,p=1$OHAxT3RZTDBLU2w3ekFYYQ$bkp+YFCoukUX2C+zFNYMCDsdOyibno5lp/65uosOMeM', '0774871683', 'Rue de la Ravine 8', 'Gorgier', 'Neuchâtel', 1, 0, 'Yo', 1, 'https://www.google.com https://www.apple.com', NULL, ''),
	(2, '456', 'mail@mail.gmail.com', 'Eric', '$argon2id$v=19$m=65536,t=4,p=1$OHAxT3RZTDBLU2w3ekFYYQ$bkp+YFCoukUX2C+zFNYMCDsdOyibno5lp/65uosOMeM', '0872334455', 'Rue de la mort 5', 'Nulle part', 'Genève', 1, 0, 'NOOOO', 1, 'https://www.google.com https://www.apple.com', NULL, ''),
	(3, '789', 'pintokevin2002@hotmail.com', 'Kevin', '$argon2id$v=19$m=65536,t=4,p=1$OHAxT3RZTDBLU2w3ekFYYQ$bkp+YFCoukUX2C+zFNYMCDsdOyibno5lp/65uosOMeM', NULL, 'Rue de la Ravine 8', 'La Grande Béroche', 'Neuchâtel', 1, 0, 'Yo, je suis Dieux.\r\n\r\nRespecte moi.', 1, 'https://www.google.com https://www.apple.com', NULL, '');

-- Listage de la structure de table localement_suisse. status
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `AK_status` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.status : ~3 rows (environ)
INSERT INTO `status` (`status_id`, `status_name`) VALUES
	(3, 'Abandonnée'),
	(1, 'En cours'),
	(2, 'Terminé');

-- Listage de la structure de table localement_suisse. subcategories
CREATE TABLE IF NOT EXISTS `subcategories` (
  `subcat_id` int NOT NULL AUTO_INCREMENT,
  `subcat_name` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cat_id` int NOT NULL,
  PRIMARY KEY (`subcat_id`),
  UNIQUE KEY `AK_subcategories` (`subcat_name`),
  KEY `FK_subcategories_categories` (`cat_id`),
  CONSTRAINT `FK_subcategories_categories` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.subcategories : ~4 rows (environ)
INSERT INTO `subcategories` (`subcat_id`, `subcat_name`, `cat_id`) VALUES
	(1, 'T-shirt', 1),
	(2, 'Pull', 1),
	(3, 'Fit', 2),
	(4, 'Pizza', 2);

-- Listage de la structure de table localement_suisse. types
CREATE TABLE IF NOT EXISTS `types` (
  `typ_id` int NOT NULL AUTO_INCREMENT,
  `typ_name` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`typ_id`),
  UNIQUE KEY `AK_types` (`typ_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.types : ~3 rows (environ)
INSERT INTO `types` (`typ_id`, `typ_name`) VALUES
	(2, 'admin'),
	(1, 'client'),
	(3, 'modérateur');

-- Listage de la structure de table localement_suisse. users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_mail` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `user_uniqid` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `user_pseudo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `user_tel` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_address_street` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_address_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_address_canton` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `typ_id` int NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `AK_users` (`user_mail`),
  UNIQUE KEY `user_uniqid` (`user_uniqid`),
  KEY `FK_users_types` (`typ_id`),
  CONSTRAINT `FK_users_types` FOREIGN KEY (`typ_id`) REFERENCES `types` (`typ_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table localement_suisse.users : ~2 rows (environ)
INSERT INTO `users` (`user_id`, `user_mail`, `user_uniqid`, `user_pseudo`, `user_password`, `user_tel`, `user_address_street`, `user_address_city`, `user_address_canton`, `typ_id`) VALUES
	(1, 'pintokevin2002@hotmail.com', '12345', 'Kevin', '$argon2id$v=19$m=65536,t=4,p=1$WXdKMDhaUjU4a2RXWm80bA$hQmnnQXBPIiW8k3nFMbtNG45Pg1yeda2DZwhxJdfTPo', '0774871683', 'Rue de la Ravine 8', 'Gorgier', 'Neuchâtel', 2),
	(2, 'kp.mail.pin@gmail.com', '67890', 'Eric', '$argon2id$v=19$m=65536,t=4,p=1$WXdKMDhaUjU4a2RXWm80bA$hQmnnQXBPIiW8k3nFMbtNG45Pg1yeda2DZwhxJdfTPo', NULL, 'Rue de la Ravine 8', 'La Grande Béroche', 'Neuchâtel', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
