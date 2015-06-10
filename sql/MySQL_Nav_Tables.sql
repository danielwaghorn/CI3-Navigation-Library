SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `CI-Nav-InMenu`
-- ----------------------------
DROP TABLE IF EXISTS `CI-Nav-InMenu`;
CREATE TABLE `CI-Nav-InMenu` (
  `MenuID` int(11) DEFAULT NULL,
  `ItemID` int(11) DEFAULT NULL,
  KEY `MenuID` (`MenuID`),
  KEY `ItemID` (`ItemID`),
  CONSTRAINT `ItemIDConst` FOREIGN KEY (`ItemID`) REFERENCES `CI-Nav-Items` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `MenuIDConst` FOREIGN KEY (`MenuID`) REFERENCES `CI-Nav-Menus` (`MenuID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `CI-Nav-Items`
-- ----------------------------
DROP TABLE IF EXISTS `CI-Nav-Items`;
CREATE TABLE `CI-Nav-Items` (
  `ItemID` int(11) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `ItemHumanName` varchar(255) DEFAULT NULL,
  `ItemLink` varchar(255) DEFAULT NULL,
  `ParentItem` int(255) DEFAULT NULL,
  PRIMARY KEY (`ItemID`),
  KEY `fkParentMenu` (`ParentItem`),
  CONSTRAINT `ParentRef` FOREIGN KEY (`ParentItem`) REFERENCES `CI-Nav-Items` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `CI-Nav-Menus`
-- ----------------------------
DROP TABLE IF EXISTS `CI-Nav-Menus`;
CREATE TABLE `CI-Nav-Menus` (
  `MenuID` int(11) NOT NULL,
  `MenuName` varchar(255) NOT NULL,
  PRIMARY KEY (`MenuID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
