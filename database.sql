DROP DATABASE IF EXISTS `app_db`;
CREATE DATABASE `app_db`;

USE `app_db`;

CREATE TABLE `authProviders`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL
);
INSERT INTO `authProviders` (`name`) VALUES ('Email'), ('Google');

CREATE TABLE `roles`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL
);
INSERT INTO `roles` (`name`) VALUES ('Admin'), ('User');

CREATE TABLE `users`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `slug` VARCHAR(255) NOT NULL,

    `email` VARCHAR(255) NOT NULL,
    `passwordHash` VARCHAR(500) DEFAULT NULL,
    `signedUpOnDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `authToken` VARCHAR(500) DEFAULT NULL,
    `fcmToken` VARCHAR(500) DEFAULT NULL,

    `authProviderId` INT NOT NULL,
    CONSTRAINT `fkAuthProviderIdInUsers` FOREIGN KEY (`authProviderId`) REFERENCES `authProviders`(`id`),

    `roleId` INT NOT NULL,
    CONSTRAINT `fkRoleIdInUserRoles` FOREIGN KEY (`roleId`) REFERENCES `roles`(`id`)
);

CREATE TABLE `generatedOtps`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    
    `email` VARCHAR(255) NOT NULL,
    `otp` VARCHAR(6) NOT NULL,
    `generatedOnDateTime`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `isVerified` BOOLEAN DEFAULT FALSE
);

CREATE TABLE `userProfiles`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `mobileNumber` VARCHAR(10) NOT NULL,
    `address` TEXT NOT NULL,
    `pincode` VARCHAR(6) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `state` VARCHAR(255) NOT NULL,
    `profilePictureFileName` VARCHAR(255) DEFAULT NULL,

    `userId` INT NOT NULL,
    CONSTRAINT `fkUserIdinUserProfiles` FOREIGN KEY (`userId`) REFERENCES `users`(`id`)
);

CREATE TABLE `categories`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `slug` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `imageFileName` VARCHAR(255) NOT NULL
);

CREATE TABLE `products`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `slug` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `price` NUMERIC NOT NULL,
    `isOutOfStock` BOOLEAN NOT NULL
);

CREATE TABLE `productImages`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `imageFileName` VARCHAR(255) NOT NULL,
    
    `productId` INT NOT NULL,
    CONSTRAINT `fkProductIdInProductImages` FOREIGN KEY (`productId`) REFERENCES `products`(`id`)
);

CREATE TABLE `productCategories`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    
    `productId` INT NOT NULL,
    CONSTRAINT `fkProductIdInProductCategories` FOREIGN KEY (`productId`) REFERENCES `products`(`id`),

    `categoryId` INT NOT NULL,
    CONSTRAINT `fkCategoryIdInProductCategories` FOREIGN KEY (`categoryId`) REFERENCES `categories`(`id`)
);

CREATE TABLE `orderStatus`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL
);
INSERT INTO `orderStatus` (`name`) VALUES 
    ('In cart'), 
    ('Pending'),
    ('On the way'), 
    ('Delivered'), 
    ('Canceled'), 
    ('Rejected');

CREATE TABLE `orders`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `slug` VARCHAR(255) NOT NULL,

    `orderedOnDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deliveredOnDateTime` DATETIME DEFAULT NULL,
    
    `totalPriceWithoutTax` NUMERIC NOT NULL,
    `cgstPercentage` NUMERIC NOT NULL,
    `cgstAmount` NUMERIC NOT NULL,
    `sgstPercentage` NUMERIC NOT NULL,
    `sgstAmount` NUMERIC NOT NULL,
    `totalPriceWithTax` NUMERIC NOT NULL,

    `orderStatusId` INT NOT NULL,
    CONSTRAINT `fkOrderStatusIdinOrders` FOREIGN KEY (`orderStatusId`) REFERENCES `orderStatus`(`id`),

    `userId` INT NOT NULL,
    CONSTRAINT `fkUserIdinOrders` FOREIGN KEY (`userId`) REFERENCES `users`(`id`)
);

CREATE TABLE `orderProducts`
(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `quantity` INT NOT NULL,

    `orderId` INT NOT NULL,
    CONSTRAINT `fkOrderIdinOrderProducts` FOREIGN KEY (`orderId`) REFERENCES `orders`(`id`),
    
    `productId` INT NOT NULL,
    CONSTRAINT `fkProductIdInOrderProducts` FOREIGN KEY (`productId`) REFERENCES `products`(`id`)
);
