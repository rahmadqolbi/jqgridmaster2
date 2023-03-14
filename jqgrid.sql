-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2023 at 05:19 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jqgrid`
--

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `no_invoice` varchar(200) NOT NULL,
  `nama_pelanggan` varchar(200) NOT NULL,
  `jenis_kelamin` varchar(200) NOT NULL,
  `saldo` float NOT NULL,
  `tgl_pembelian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `no_invoice`, `nama_pelanggan`, `jenis_kelamin`, `saldo`, `tgl_pembelian`) VALUES
(425, '12121', 'SDA', 'LAKI-LAKI', 12, '2023-02-09'),
(426, 'IKAN', 'D123', 'LAKI-LAKI', 10000, '2023-02-09'),
(427, 'ASEK', 'DS', 'LAKI-LAKI', 2131, '2023-02-15'),
(428, 'SDAD', 'DSA', 'LAKI-LAKI', 2131, '2023-02-08'),
(429, 'SDAD', 'ASASDGRGR', 'LAKI-LAKI', 90000000, '2012-08-15'),
(430, 'ADAWD', 'FGFGEG', 'LAKI-LAKI', 12211200, '2023-02-08'),
(431, 'DADASD12', 'ADA', 'LAKI-LAKI', 1111110, '2023-02-16'),
(432, '1WWDADS', 'EQE', 'LAKI-LAKI', 11344, '2023-02-08'),
(433, '121E', 'R', 'PEREMPUAN', 311, '2023-02-06'),
(435, 'SDSAD', 'SDA', 'PEREMPUAN', 213112, '2023-02-06'),
(436, 'WDASDA', '2112', 'LAKI-LAKI', 123121, '2023-02-16'),
(437, 'DADQWDADRG', 'WE12231331', 'LAKI-LAKI', 1000000, '2023-02-09'),
(438, 'SDADSA', '1231', 'PEREMPUAN', 50000000, '2023-02-15'),
(439, 'AA001', 'QOLBI', 'LAKI-LAKI', 100000000, '2023-08-31'),
(440, 'AAD1', 'RAZZAQ', 'LAKI-LAKI', 40000000, '2024-01-19'),
(441, 'DADAD', '133', 'LAKI-LAKI', 40000000, '2023-02-09'),
(442, 'DSADD', 'DF', 'LAKI-LAKI', 112, '2023-02-01'),
(443, 'A', 'DD', 'LAKI-LAKI', 21, '2023-02-09'),
(444, 'SDAD', '12', 'LAKI-LAKI', 2131, '2023-02-10'),
(445, 'DAD', 'DSA', 'LAKI-LAKI', 211, '2023-02-15'),
(446, '22', '43', 'LAKI-LAKI', 4334, '2023-02-09'),
(447, '56', '54', 'LAKI-LAKI', 6454, '2023-02-09'),
(448, '4545', '654', 'LAKI-LAKI', 454, '2023-02-09'),
(451, 'AA1231', 'RAHMAD', 'LAKI-LAKI', 90000000, '2022-12-12'),
(452, 'AA', 'AA', 'LAKI-LAKI', 21311, '2023-02-07'),
(453, 'AVR', 'SDSD', 'PEREMPUAN', 213121, '2023-02-07'),
(454, 'DASD', 'DSA', 'LAKI-LAKI', 2121, '2023-02-08'),
(455, 'ADDASSD', 'AADD', 'LAKI-LAKI', 998998, '2020-08-15'),
(456, 'ADASDRFE', 'SFSD', 'LAKI-LAKI', 3121, '2023-02-09'),
(457, 'AD', 'GFDG', 'LAKI-LAKI', 1231, '2023-02-09'),
(458, 'FKFD', 'ADEW', 'LAKI-LAKI', 23131200, '2023-02-22'),
(459, 'SD', 'SDA', 'LAKI-LAKI', 1231310, '2023-02-22'),
(460, 'SDAGTGT', 'FG', 'LAKI-LAKI', 332, '2023-02-15'),
(461, 'ADDFWE', 'SA', 'LAKI-LAKI', 122, '2023-02-17'),
(462, 'SDA', 'AD', 'LAKI-LAKI', 122, '2023-02-08'),
(463, 'SD', 'SA', 'LAKI-LAKI', 21, '2023-02-07'),
(464, 'EERA', 'AD', 'LAKI-LAKI', 213121, '2023-02-09'),
(465, 'HG', 'ADAS', 'LAKI-LAKI', 1231, '2023-02-09'),
(466, 'SDADEE', 'FDF', 'LAKI-LAKI', 141312, '2023-02-09'),
(467, 'DSAAS', 'DFDF', 'LAKI-LAKI', 32232, '2023-02-08'),
(468, 'RFREEWEFEW', 'DFD', 'LAKI-LAKI', 12, '2023-02-15'),
(469, 'KJ', 'DF', 'LAKI-LAKI', 44354400, '2023-02-15'),
(470, 'DDD', 'SA', 'LAKI-LAKI', 211, '2023-02-14'),
(471, 'HYR', 'EWW', 'LAKI-LAKI', 3432, '2023-02-13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=472;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
