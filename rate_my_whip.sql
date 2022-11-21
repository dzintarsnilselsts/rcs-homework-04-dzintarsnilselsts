-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2022 at 05:55 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rate_my_whip`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_list`
--

CREATE TABLE `email_list` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_list`
--

INSERT INTO `email_list` (`id`, `email`, `username`) VALUES
(1, 'test@test.com', NULL),
(2, 'dzintarsnilselsts@gmail.com', NULL),
(3, 'epasts@test.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `user_id` int(255) NOT NULL,
  `publish_date` datetime NOT NULL,
  `image` mediumtext NOT NULL,
  `gallery` mediumtext NOT NULL DEFAULT '[]',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `rating` mediumtext NOT NULL DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `excerpt`, `text`, `user_id`, `publish_date`, `image`, `gallery`, `is_deleted`, `rating`) VALUES
(1, 'adsfasdf', 'asdfasdf', 'adsfasdf         asdfasdfasdfasdf', 2, '2022-11-08 23:11:26', 'AD89.tmp.png', '[\'AD8A.tmp.png\']', 0, '[]'),
(2, 'asdasd', 'asdasda', 'sdasdasd', 2, '2022-11-09 00:26:21', '436A.tmp.png', '[\'436B.tmp.png\']', 0, '[]'),
(3, 'audi 80 quattro', 'a shitbox', '94\' a80 quattro -> rwd', 2, '2022-11-09 22:04:10', '73E4.tmp.png', '[\'73E5.tmp.png\',\'73E6.tmp.png\']', 0, '[]'),
(4, 'Some random red car', 'red', 'This sticker adds atleast 30hp to your whip', 4, '2022-11-12 19:39:02', 'A67A.tmp.png', '[\'A6AA.tmp.png\']', 1, '[]'),
(5, 'a dickbutt', '', 'here\'s some dickbutt for you', 4, '2022-11-12 22:23:55', '9B0C.tmp.png', '[\'9B1D.tmp.png\']', 1, '[]'),
(6, 'Sickbutt', '', 'sickbutt on a car', 4, '2022-11-14 01:40:28', 'EA12.tmp.png', '[\'EA13.tmp.png\']', 0, '[]'),
(7, 'audi 80 rwd', '', 'tests', 4, '2022-11-14 19:44:16', '294C.tmp.png', '[\'294D.tmp.png\']', 1, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `following` longtext NOT NULL DEFAULT '[]',
  `followers` longtext NOT NULL DEFAULT '[]',
  `blocked` longtext NOT NULL DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `following`, `followers`, `blocked`) VALUES
(1, 'test', 'test@testytest.com', '$2y$10$TnAveP/IWHitFAGV5UG1ue.XkHwABWx.6uNJzcVhztZBD9hV5LHCS', '[]', '[]', '[]'),
(2, 'testy', 'test@testy.com', '$2y$10$97GAyUaM/OCDW3yh6D8roec9Ux0Wuvz7z9unzKJC5bchPrG87KUpm', '[\'4\']', '[\'4\']', '[]'),
(3, 'test1', 'testy@testy.com', '$2y$10$.wnzHez4B5/IelgGNoSUIuEsC4JsJ9TmDqKArtUoOwFmcSDjYwKXS', '[]', '[]', '[]'),
(4, 'DzintarsNils', 'dzintarsnilselsts@gmail.com', '$2y$10$pxFthZCW9vCOBzbqSc6w9.fW9K95czl6dJIfLhPI9D.lgL.kjYzoa', '[\'2\']', '[\'2\']', '[]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_list`
--
ALTER TABLE `email_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_list`
--
ALTER TABLE `email_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
