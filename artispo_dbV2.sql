-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 03:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `artispo`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `_id` int(11) NOT NULL,
  `category_id` varchar(36) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`_id`, `category_id`, `category_name`) VALUES
(1, 'fc66b722-b4cc-4c7c-b6d6-92fe52880152', 'Uncategorized');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `_id` int(11) NOT NULL,
  `comment_id` varchar(36) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `post_id_fk` int(11) NOT NULL,
  `date_posted` datetime DEFAULT current_timestamp(),
  `comment_content` varchar(1000) NOT NULL,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
  `_id` int(11) NOT NULL,
  `hashtag_id` varchar(36) NOT NULL,
  `hashtag_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `liked_posts`
--

CREATE TABLE `liked_posts` (
  `user_id_fk` int(11) NOT NULL,
  `post_id_fk` int(11) NOT NULL,
  `date_liked` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `_id` int(11) NOT NULL,
  `post_id` varchar(36) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `category_id_fk` int(11) NOT NULL,
  `date_posted` datetime DEFAULT current_timestamp(),
  `title` varchar(120) NOT NULL,
  `image` varchar(2048) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`_id`, `post_id`, `user_id_fk`, `category_id_fk`, `date_posted`, `title`, `image`, `description`) VALUES
(1, 'e785b69b-5782-4ebd-a82a-69e41cbabd99', 2, 1, '2026-03-20 16:18:09', 'Post_upload_69bd02c172afb5.86521389_1773994689', 'upload_69bd02c172afb5.86521389.jpg', 'Bag of bagasse'),
(2, 'c8f3d665-56f5-4c92-9370-647b71600989', 3, 1, '2026-03-26 18:21:14', 'Post_upload_69c5089a533f39.69118940_1774520474', 'upload_69c5089a533f39.69118940.png', 'GOD OF WAR IS PEAK');

-- --------------------------------------------------------

--
-- Table structure for table `posts_hashtags`
--

CREATE TABLE `posts_hashtags` (
  `post_id` varchar(36) NOT NULL,
  `hashtag_id` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_posts`
--

CREATE TABLE `saved_posts` (
  `user_id_fk` int(11) NOT NULL,
  `post_id_fk` int(11) NOT NULL,
  `date_saved` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `_id` int(11) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`_id`, `user_id`, `username`, `email`, `password`, `profile_picture`, `bio`) VALUES
(1, '4cb09499-44ad-46eb-b09a-2a1965f84e43', 'novus', 'novus@email.com', '$argon2id$v=19$m=65536,t=4,p=1$Vm5HL3ppQXUubkd4RDNYVA$Gx0YQ8PY5TGd3AyLD4AzGwdNDVVyCC3fFO9joJ0iXZo', NULL, NULL),
(2, '45d816e4-ce1e-4eef-9fca-9b802ee16c17', 'hello', 'hello@email.com', '$argon2id$v=19$m=65536,t=4,p=1$SmJvTmlJYklNbXZpUnFTag$MKk/bqG4g0NLXPT/nRLH4kYgUXQrn/xe0sc+XGHil08', 'hello.jpg', 'hi this is my bio!'),
(3, '5550dd25-4134-408b-97b2-fed2fa3c7590', 'hallu', 'hallu@email.com', '$argon2id$v=19$m=65536,t=4,p=1$UWZVMXN1Y0xGU1dFU255Sg$p7bIFIp7I7W3qVrA5G8Xpncs68LFmf5bcO2531bC1KU', 'hallu.jpg', 'GUN MAN');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`_id`,`category_id`),
  ADD UNIQUE KEY `category_id` (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`_id`,`comment_id`),
  ADD UNIQUE KEY `comment_id` (`comment_id`),
  ADD KEY `fk_comments_users` (`user_id_fk`),
  ADD KEY `fk_comments_posts` (`post_id_fk`);

--
-- Indexes for table `hashtags`
--
ALTER TABLE `hashtags`
  ADD PRIMARY KEY (`_id`,`hashtag_id`),
  ADD UNIQUE KEY `hashtag_id` (`hashtag_id`),
  ADD UNIQUE KEY `hashtag_name` (`hashtag_name`);

--
-- Indexes for table `liked_posts`
--
ALTER TABLE `liked_posts`
  ADD PRIMARY KEY (`user_id_fk`,`post_id_fk`),
  ADD KEY `fk_likes_posts` (`post_id_fk`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`_id`,`post_id`),
  ADD UNIQUE KEY `post_id` (`post_id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `fk_posts_users` (`user_id_fk`),
  ADD KEY `fk_posts_categories` (`category_id_fk`);

--
-- Indexes for table `posts_hashtags`
--
ALTER TABLE `posts_hashtags`
  ADD PRIMARY KEY (`post_id`,`hashtag_id`),
  ADD KEY `fk_ph_hashtags` (`hashtag_id`);

--
-- Indexes for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD PRIMARY KEY (`user_id_fk`,`post_id_fk`),
  ADD KEY `fk_saves_posts` (`post_id_fk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`_id`,`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hashtags`
--
ALTER TABLE `hashtags`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_posts` FOREIGN KEY (`post_id_fk`) REFERENCES `posts` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_users` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `liked_posts`
--
ALTER TABLE `liked_posts`
  ADD CONSTRAINT `fk_likes_posts` FOREIGN KEY (`post_id_fk`) REFERENCES `posts` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_likes_users` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_categories` FOREIGN KEY (`category_id_fk`) REFERENCES `categories` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_posts_users` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts_hashtags`
--
ALTER TABLE `posts_hashtags`
  ADD CONSTRAINT `fk_ph_hashtags` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`hashtag_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ph_posts` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD CONSTRAINT `fk_saves_posts` FOREIGN KEY (`post_id_fk`) REFERENCES `posts` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saves_users` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
