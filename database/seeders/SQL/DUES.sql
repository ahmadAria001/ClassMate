SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `dues` (`id`, `typeDues`, `description`, `amt_dues`, `amt_fund`, `status`, `rt_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'TrashManagement', 'Iuran Sampah RT-1', 33000.00, 0.00, 0, 1, 1489577018219, NULL, NULL, NULL, NULL, NULL),
(2, 'Security', 'Iuran Keamanan RT-1', 31000.00, 0.00, 1, 1, 1514061598749, NULL, NULL, NULL, NULL, NULL),
(3, 'Event', 'Iuran Kumpul Warga RT-1', 20000.00, 0.00, 1, 1, 1540510261738, NULL, NULL, NULL, NULL, NULL),
(4, 'TrashManagement', 'Iuran Sampah RT-2', 24000.00, 0.00, 1, 2, 1489452547922, NULL, NULL, NULL, NULL, NULL),
(5, 'Security', 'Iuran Keamanan RT-2', 41000.00, 0.00, 0, 2, 1492848530345, NULL, NULL, NULL, NULL, NULL),
(6, 'Event', 'Iuran Kumpul Warga RT-2', 13000.00, 0.00, 0, 2, 1516197397934, NULL, NULL, NULL, NULL, NULL),
(7, 'Event', 'Iuran 17 Agustus', 42000.00, 0.00, 1, 2, 1490119582174, NULL, NULL, NULL, NULL, NULL),
(8, 'TrashManagement', 'Iuran Sampah RT-3', 27000.00, 0.00, 0, 3, 1510857804694, NULL, NULL, NULL, NULL, NULL),
(9, 'Security', 'Iuran Keamanan RT-3', 26000.00, 0.00, 1, 3, 1487957617130, NULL, NULL, NULL, NULL, NULL),
(10, 'Event', 'Iuran Kumpul Warga RT-3', 13000.00, 0.00, 1, 3, 1516956728482, NULL, NULL, NULL, NULL, NULL),
(11, 'Event', 'Iuran 17 Agustus', 50000.00, 0.00, 0, 3, 1532958946661, NULL, NULL, NULL, NULL, NULL),
(12, 'TrashManagement', 'Iuran Sampah RT-4', 39000.00, 0.00, 0, 4, 1513424635619, NULL, NULL, NULL, NULL, NULL),
(13, 'Security', 'Iuran Keamanan RT-4', 20000.00, 0.00, 0, 4, 1486124437066, NULL, NULL, NULL, NULL, NULL),
(14, 'TrashManagement', 'Iuran Sampah RT-5', 36000.00, 0.00, 0, 5, 1511113244513, NULL, NULL, NULL, NULL, NULL),
(15, 'Security', 'Iuran Keamanan RT-5', 31000.00, 0.00, 0, 5, 1501658290499, NULL, NULL, NULL, NULL, NULL),
(16, 'TrashManagement', 'Iuran Sampah RT-6', 25000.00, 0.00, 1, 6, 1513138239699, NULL, NULL, NULL, NULL, NULL),
(17, 'Security', 'Iuran Keamanan RT-6', 45000.00, 0.00, 0, 6, 1501304922211, NULL, NULL, NULL, NULL, NULL),
(18, 'Event', 'Iuran Kumpul Warga RT-6', 20000.00, 0.00, 1, 6, 1505458766376, NULL, NULL, NULL, NULL, NULL),
(19, 'Event', 'Iuran 17 Agustus', 44000.00, 0.00, 0, 6, 1542374691970, NULL, NULL, NULL, NULL, NULL),
(20, 'TrashManagement', 'Iuran Sampah RT-7', 39000.00, 0.00, 0, 7, 1500557303230, NULL, NULL, NULL, NULL, NULL),
(21, 'Security', 'Iuran Keamanan RT-7', 47000.00, 0.00, 0, 7, 1506657587121, NULL, NULL, NULL, NULL, NULL),
(22, 'Event', 'Iuran Kumpul Warga RT-7', 20000.00, 0.00, 0, 7, 1498036934075, NULL, NULL, NULL, NULL, NULL),
(23, 'Event', 'Iuran 17 Agustus', 31000.00, 0.00, 0, 7, 1500337841253, NULL, NULL, NULL, NULL, NULL),
(24, 'TrashManagement', 'Iuran Sampah RT-8', 26000.00, 0.00, 0, 8, 1484879845930, NULL, NULL, NULL, NULL, NULL),
(25, 'Security', 'Iuran Keamanan RT-8', 38000.00, 0.00, 0, 8, 1498086616976, NULL, NULL, NULL, NULL, NULL),
(26, 'Event', 'Iuran Kumpul Warga RT-8', 11000.00, 0.00, 1, 8, 1503446430816, NULL, NULL, NULL, NULL, NULL),
(27, 'Event', 'Iuran 17 Agustus', 27000.00, 0.00, 0, 8, 1495046708459, NULL, NULL, NULL, NULL, NULL),
(28, 'TrashManagement', 'Iuran Sampah RT-9', 15000.00, 0.00, 1, 9, 1491461932058, NULL, NULL, NULL, NULL, NULL),
(29, 'Security', 'Iuran Keamanan RT-9', 35000.00, 0.00, 0, 9, 1500057446002, NULL, NULL, NULL, NULL, NULL),
(30, 'Event', 'Iuran Kumpul Warga RT-9', 27000.00, 0.00, 1, 9, 1485859961333, NULL, NULL, NULL, NULL, NULL),
(31, 'Event', 'Iuran 17 Agustus', 40000.00, 0.00, 0, 9, 1516369034684, NULL, NULL, NULL, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
