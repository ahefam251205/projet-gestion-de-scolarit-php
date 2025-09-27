-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 15 Mars 2010 à 13:02
-- Version du serveur: 5.1.36
-- Version de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `gestion`
--

-- --------------------------------------------------------

--
-- Structure de la table `bulletin`
--

CREATE TABLE IF NOT EXISTS `bulletin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numsem` int(11) NOT NULL,
  `numel` int(11) NOT NULL,
  `codemat` int(11) NOT NULL,
  `notefinal` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `bulletin`
--

INSERT INTO `bulletin` (`id`, `numsem`, `numel`, `codemat`, `notefinal`) VALUES
(1, 1, 8, 17, 15.12),
(5, 3, 3, 10, 14.25),
(3, 4, 3, 5, 14.785),
(6, 3, 4, 10, 14.5);

-- --------------------------------------------------------

--
-- Structure de la table `classe`
--

CREATE TABLE IF NOT EXISTS `classe` (
  `codecl` int(11) NOT NULL AUTO_INCREMENT,
  `nom` text NOT NULL,
  `numprofcoord` int(11) NOT NULL,
  `promotion` int(11) NOT NULL,
  PRIMARY KEY (`codecl`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `classe`
--

INSERT INTO `classe` (`codecl`, `nom`, `numprofcoord`, `promotion`) VALUES
(1, 'GI', 2, 2008),
(2, 'TM', 1, 2008),
(3, 'GRH', 3, 2008),
(4, 'GI', 1, 2009),
(5, 'TM', 2, 2009),
(6, 'GRH', 4, 2009);

-- --------------------------------------------------------

--
-- Structure de la table `conseil`
--

CREATE TABLE IF NOT EXISTS `conseil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numsem` int(11) NOT NULL,
  `codecl` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `conseil`
--

INSERT INTO `conseil` (`id`, `numsem`, `codecl`) VALUES
(1, 1, 4),
(6, 3, 1),
(4, 2, 5);

-- --------------------------------------------------------

--
-- Structure de la table `devoir`
--

CREATE TABLE IF NOT EXISTS `devoir` (
  `numdev` int(11) NOT NULL AUTO_INCREMENT,
  `date_dev` text NOT NULL,
  `coeficient` int(11) NOT NULL,
  `codemat` int(11) NOT NULL,
  `codecl` int(11) NOT NULL,
  `numsem` int(11) NOT NULL,
  `n_devoir` int(1) NOT NULL,
  PRIMARY KEY (`numdev`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `devoir`
--

INSERT INTO `devoir` (`numdev`, `date_dev`, `coeficient`, `codemat`, `codecl`, `numsem`, `n_devoir`) VALUES
(1, '12/01/2010', 4, 3, 2, 3, 1),
(2, '01/03/2010', 3, 1, 4, 2, 1),
(3, '02/05/2010', 4, 10, 1, 3, 1),
(4, '20/01/2010', 5, 3, 3, 3, 1),
(5, '02/04/2011', 3, 5, 1, 4, 1),
(6, '04/03/2010', 4, 4, 5, 2, 1),
(7, '10/02/2010', 3, 1, 4, 2, 2),
(8, '10/10/2010', 3, 3, 2, 4, 1),
(9, '23/32/2010', 4, 10, 1, 3, 2),
(11, '10/10/2010', 3, 5, 1, 4, 2),
(14, '10/02/2010', 5, 8, 4, 2, 1),
(19, '12/02/2010', 4, 4, 5, 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `diplome`
--

CREATE TABLE IF NOT EXISTS `diplome` (
  `numdip` int(11) NOT NULL AUTO_INCREMENT,
  `titre_dip` text NOT NULL,
  PRIMARY KEY (`numdip`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `diplome`
--

INSERT INTO `diplome` (`numdip`, `titre_dip`) VALUES
(1, 'DUT_GI'),
(2, 'DUT_TM'),
(6, 'DUT_GRH');

-- --------------------------------------------------------

--
-- Structure de la table `eleve`
--

CREATE TABLE IF NOT EXISTS `eleve` (
  `numel` int(11) NOT NULL AUTO_INCREMENT,
  `nomel` text NOT NULL,
  `prenomel` text NOT NULL,
  `date_naissance` text NOT NULL,
  `adresse` text NOT NULL,
  `telephone` text NOT NULL,
  `codecl` int(11) NOT NULL,
  PRIMARY KEY (`numel`),
  KEY `codecl2` (`codecl`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Contenu de la table `eleve`
--

INSERT INTO `eleve` (`numel`, `nomel`, `prenomel`, `date_naissance`, `adresse`, `telephone`, `codecl`) VALUES
(3, 'LAGMAR', 'Ayoub', '02/19/1999', 'Casablanca', '029329452', 1),
(4, 'Nohad', 'Imad', '21/01/1990', 'Casablanca', '029329452', 1),
(5, 'OUBARKA', 'Samir', '19/11/1990', 'barnoussin<br />\r\nCasablanca', '029329452', 2),
(6, 'Kadir', 'Younes', '19/19/1999', 'Ouazzan', '029329452', 3),
(7, 'Zemzami', 'Mehdi', '20/07/1991', 'maarif<br />\r\nCasablanca', '029355552', 5),
(8, 'Achraf', 'Achraf', '19/19/1989', 'Casablanca', '029329452', 4),
(9, 'Fadil', 'Hamada', '19/00/1999', 'Casablanca', '029329452', 6),
(10, 'Chaimaa', 'Chaimma', '19/19/1989', 'Casablanca', '029329452', 3),
(11, 'Alamai', 'Karim', '19/19/1988', 'Settat', '029329452', 6),
(13, 'Alami', 'Meriem', '21/03/1990', 'Berrechid', '097217342', 5),
(14, 'Alami', 'Meriem', '21/03/1990', 'Berrechid', '097217342', 5);

-- --------------------------------------------------------

--
-- Structure de la table `eleve_diplome`
--

CREATE TABLE IF NOT EXISTS `eleve_diplome` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numdip` int(11) NOT NULL,
  `numel` int(11) NOT NULL,
  `note` float NOT NULL,
  `commentaire` text NOT NULL,
  `etablissement` text NOT NULL,
  `lieu` text NOT NULL,
  `annee_obtention` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `eleve_diplome`
--

INSERT INTO `eleve_diplome` (`id`, `numdip`, `numel`, `note`, `commentaire`, `etablissement`, `lieu`, `annee_obtention`) VALUES
(1, 1, 3, 14.54, 'Bien', 'ESTB', 'ESTB', 2010),
(2, 2, 5, 13, 'Assez bien', 'ESTB', 'ESTB', 2010),
(3, 3, 5, 12, 'SZDQS', 'SDFS', 'SD', 1212);

-- --------------------------------------------------------

--
-- Structure de la table `enseignement`
--

CREATE TABLE IF NOT EXISTS `enseignement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codecl` int(11) NOT NULL,
  `codemat` int(11) NOT NULL,
  `numprof` int(11) NOT NULL,
  `numsem` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `enseignement`
--

INSERT INTO `enseignement` (`id`, `codecl`, `codemat`, `numprof`, `numsem`) VALUES
(1, 1, 5, 3, 4),
(2, 2, 9, 6, 4),
(3, 5, 4, 6, 2),
(4, 2, 3, 6, 3),
(5, 1, 10, 4, 3),
(6, 4, 1, 2, 2),
(7, 3, 3, 6, 3),
(8, 6, 11, 6, 2),
(9, 1, 12, 1, 4),
(10, 4, 8, 5, 2),
(13, 3, 7, 6, 3),
(14, 1, 18, 4, 3),
(15, 5, 5, 5, 1),
(16, 5, 17, 5, 1),
(17, 4, 2, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE IF NOT EXISTS `evaluation` (
  `numeval` int(11) NOT NULL AUTO_INCREMENT,
  `numdev` int(11) NOT NULL,
  `numel` int(11) NOT NULL,
  `note` float NOT NULL,
  PRIMARY KEY (`numeval`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `evaluation`
--

INSERT INTO `evaluation` (`numeval`, `numdev`, `numel`, `note`) VALUES
(1, 1, 5, 12.18),
(2, 5, 4, 14),
(3, 6, 7, 15.25),
(4, 7, 8, 17),
(5, 5, 3, 13.57),
(6, 3, 3, 15),
(7, 4, 10, 18),
(8, 14, 8, 15),
(9, 2, 8, 13.75),
(11, 3, 4, 14.75),
(17, 19, 7, 16.25),
(13, 11, 3, 16),
(14, 1, 4, 13),
(15, 9, 3, 13.5),
(16, 9, 4, 14.25);

-- --------------------------------------------------------

--
-- Structure de la table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Num` int(11) NOT NULL,
  `pseudo` text NOT NULL,
  `passe` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `login`
--

INSERT INTO `login` (`id`, `Num`, `pseudo`, `passe`, `type`) VALUES
(1, 1, 'SNOUNI', 'leila', 'prof'),
(3, 100, 'admin', 'admin', 'admin'),
(4, 3, 'LAGMAR', 'Ayoub', 'etudiant');

-- --------------------------------------------------------

--
-- Structure de la table `matiere`
--

CREATE TABLE IF NOT EXISTS `matiere` (
  `codemat` int(11) NOT NULL AUTO_INCREMENT,
  `nommat` text NOT NULL,
  `codecl` int(11) NOT NULL,
  PRIMARY KEY (`codemat`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Contenu de la table `matiere`
--

INSERT INTO `matiere` (`codemat`, `nommat`, `codecl`) VALUES
(1, 'mathematique', 4),
(2, 'Algorithme', 4),
(3, 'Comptabilite', 2),
(4, 'Marketing', 5),
(5, 'Programmation', 1),
(6, 'psychiatrie', 6),
(7, 'Gestion des ressource', 3),
(8, 'Communication_gi', 4),
(9, 'Qualité', 2),
(10, 'Reseau', 1),
(11, 'Gestion de projet', 6),
(12, 'Multimedia', 1),
(14, 'Anglais', 5),
(16, 'Droit social', 6),
(17, 'Economie generale', 5);

-- --------------------------------------------------------

--
-- Structure de la table `prof`
--

CREATE TABLE IF NOT EXISTS `prof` (
  `numprof` int(11) NOT NULL AUTO_INCREMENT,
  `nom` text NOT NULL,
  `prenom` text NOT NULL,
  `adresse` text NOT NULL,
  `telephone` text NOT NULL,
  PRIMARY KEY (`numprof`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `prof`
--

INSERT INTO `prof` (`numprof`, `nom`, `prenom`, `adresse`, `telephone`) VALUES
(1, 'SNOUNI', 'Leila', 'Salé', '023294532'),
(2, 'NAFIDI', 'Ahmed', 'casablanca', '0293287425'),
(3, 'Naimi', 'Mohamad', 'Rabat', '34328724'),
(4, 'Nasserdin', 'Bouchaib', 'SETTAT', '02932842342'),
(5, 'Laghrissi', 'Nadia', 'Settat', '0293248235'),
(6, 'CHROKI', 'RAZAN', 'SETTAT', '029328472'),
(8, 'LAKIR', 'Mohamed', 'Casablanca''', '0900223');

-- --------------------------------------------------------

--
-- Structure de la table `semestre`
--

CREATE TABLE IF NOT EXISTS `semestre` (
  `numsem` int(11) NOT NULL AUTO_INCREMENT,
  `date_debut` text NOT NULL,
  `date_fin` text NOT NULL,
  PRIMARY KEY (`numsem`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `semestre`
--

INSERT INTO `semestre` (`numsem`, `date_debut`, `date_fin`) VALUES
(1, '10/11/2010', '01/04/2011'),
(2, '10/04/2010', '10/08/2010'),
(3, '02/03/2010', '02/06/2010'),
(4, '20/06/2010', '20/09/2010');

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE IF NOT EXISTS `stage` (
  `numstage` int(11) NOT NULL AUTO_INCREMENT,
  `lieu_stage` text NOT NULL,
  `date_debut` text NOT NULL,
  `date_fin` text NOT NULL,
  `numel` int(11) NOT NULL,
  PRIMARY KEY (`numstage`),
  KEY `numel1` (`numel`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `stage`
--

INSERT INTO `stage` (`numstage`, `lieu_stage`, `date_debut`, `date_fin`, `numel`) VALUES
(1, 'dell', '04/03/2010', '11/03/2010', 3),
(2, 'COREMI', '01/07/2009', '01/08/2009', 5),
(3, 'OCP', '01/07/2009', '01/08/2009', 4),
(8, 'Microsoft', '01/07/2010', '01/08/2010', 8);
