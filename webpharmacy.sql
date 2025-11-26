-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 02:58 PM
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
-- Database: `webpharmacy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` varchar(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `email`, `phone`) VALUES
('ADM001', 'sarah', 'sarahabc', NULL, '082467895467'),
('ADM002', 'neyza', 'neyzaabc', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `medicine_id` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` varchar(20) NOT NULL,
  `supplier_id` varchar(10) DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `category` enum('wellness','medicine') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `dosage` text DEFAULT NULL,
  `warnings` text DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `tag` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `supplier_id`, `medicine_name`, `category`, `price`, `expired_date`, `image_path`, `description`, `benefits`, `dosage`, `warnings`, `rating`, `tag`, `stock`, `created_at`, `updated_at`, `admin_id`) VALUES
('MED-001', 'SUP001', 'Bodrex Tablet', 'medicine', 18000.00, '2028-10-20', '../../assets/bodrex.png', 'Popular pain reliever and fever reducer with fast-acting formula that effectively treats headaches and muscle pain.', 'Relieves headaches, Reduces fever, Eases muscle pain', 'Adults: 1-2 tablets every 4-6 hours. Max 8 tablets/day', 'Do not use if you have stomach ulcer', 5.0, 'best seller', 200, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('MED-002', 'SUP003', 'OBH Combi Syrup', 'medicine', 35000.00, '2028-10-20', '../../assets/ObhCombi.png', 'Effective cough syrup that soothes throat irritation and relieves both dry and wet cough symptoms quickly.', 'Relieves dry cough, Soothes throat, Fast acting', 'Adults: 3 teaspoons 3-4 times daily', 'Not for children under 6 years', 4.5, 'best seller', 100, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('MED-003', 'SUP003', 'Panadol Cold & Flu', 'medicine', 28000.00, '2028-10-20', '../../assets/panadol.jpg', 'Trusted all-in-one cold and flu medicine that relieves fever, headache, runny nose, and body aches effectively.', 'Relieves cold symptoms, Reduces fever, Eases body aches', 'Adults: 2 tablets every 6 hours', 'Consult doctor if symptoms persist', 5.0, 'new arrival', 150, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-004', 'SUP004', 'Balsem Geliga', 'medicine', 24000.00, '2028-10-20', '../../assets/balsem.webp', 'Powerful topical pain relief balm that provides warming sensation to ease muscle aches and joint stiffness.', 'Relieves muscle pain, Warming sensation, Eases stiffness', 'Apply to affected area 2-3 times daily', 'For external use only', 4.5, 'best seller', 180, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-005', 'SUP001', 'Alpara Antacid', 'medicine', 28000.00, '2028-10-20', '../../assets/AlparaObat.jpeg', 'Effective antacid that relieves heartburn, acid indigestion, and upset stomach with a soothing mint flavor.', 'Relieves heartburn, Neutralizes acid, Soothes stomach', 'Chew 1-2 tablets as needed', 'Do not exceed 12 tablets in 24 hours', 5.0, 'new arrival', 130, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-006', 'SUP002', 'Diapet Anti-Diarrheal', 'medicine', 22000.00, '2028-10-20', '../../assets/DiapetObat.webp', 'Fast-acting anti-diarrheal medication that provides quick relief from stomach discomfort and digestive issues.', 'Stops diarrhea, Relieves stomach discomfort, Fast acting', 'Adults: 2 tablets initially, then 1 after each loose stool', 'Consult doctor if diarrhea persists', 4.5, 'best seller', 160, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('MED-007', 'SUP003', 'Metformin 500mg', 'medicine', 85000.00, '2028-10-20', '../../assets/MetforminObat.png', 'Prescription diabetes medication that helps control blood sugar levels and improves insulin sensitivity.', 'Controls blood sugar, Improves insulin sensitivity, Long-term diabetes management', 'As prescribed by doctor', 'Prescription required. Regular monitoring needed', 5.0, 'prescription required', 90, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('MED-008', 'SUP004', 'Mixagrip Flu Relief', 'medicine', 32000.00, '2028-10-20', '../../assets/MixagripObat.webp', 'Complete flu and cold remedy that relieves fever, headache, runny nose, and body aches in one tablet.', 'Relieves flu symptoms, Reduces fever, All-in-one relief', 'Adults: 1 tablet 3 times daily', 'Do not use with other paracetamol products', 4.5, 'best seller', 140, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-009', 'SUP001', 'Promag Liquid Antacid', 'medicine', 30000.00, '2028-10-20', '../../assets/Promag.webp', 'Trusted antacid liquid that quickly neutralizes stomach acid and provides long-lasting relief from heartburn.', 'Fast relief, Neutralizes acid, Long-lasting effect', 'Adults: 1-2 tablespoons as needed', 'Shake well before use', 5.0, 'best seller', 110, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('MED-010', 'SUP002', 'Rhinos Decongestant', 'medicine', 38000.00, '2028-10-20', '../../assets/RhinosObat.jpg', 'Powerful nasal decongestant that provides fast relief from stuffy nose and sinus pressure caused by allergies.', 'Clears nasal congestion, Relieves sinus pressure, Fast acting', 'Adults: 1 tablet every 12 hours', 'Do not use for more than 7 days', 4.5, 'new arrival', 95, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-011', 'SUP003', 'Sanmol Kids Syrup', 'wellness', 27000.00, '2028-10-20', '../../assets/SanmolObat.jpg', 'Gentle paracetamol syrup specially formulated for children to reduce fever and relieve minor aches safely.', 'Safe for children, Reduces fever, Pleasant taste', 'Children 6-12 years: 1-2 teaspoons every 4-6 hours', 'Consult doctor for children under 6', 5.0, 'best seller', 170, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED-012', 'SUP004', 'Tempra Fever Syrup', 'wellness', 29000.00, '2028-10-20', '../../assets/TempraObat.jpeg', 'Trusted children\'s fever reducer with a pleasant grape flavor that works fast to bring down high temperatures.', 'Fast fever relief, Grape flavor, Gentle formula', 'Follow age-based dosing on package', 'Store at room temperature', 4.5, 'new arrival', 155, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('MED1310', 'SUP010', 'FreshCare', 'medicine', 15000.00, '2030-02-20', 'uploads/medicines/MED_1760942341_68f5d905e1433.png', 'example', 'example', 'example', 'example', 0.0, NULL, 150, '2025-10-20 06:39:01', '2025-10-20 06:39:01', 'ADM001'),
('MED4850', 'SUP010', 'f', 'medicine', 1000.00, '2030-10-10', 'uploads/medicines/MED_1760905733_68f54a0597656.png', 'nNana', 'nanna', 'na', 'na', 0.0, NULL, 100, '2025-10-19 20:28:53', '2025-10-19 20:28:53', 'ADM001'),
('WEL-001', 'SUP001', 'Multivitamin Plus', 'wellness', 95000.00, '2028-10-20', '../../assets/multivitamin.jpg', 'Essential multivitamin supplement that provides complete daily nutrition to boost energy and support overall health.', 'Boosts energy levels, Supports immune system, Improves overall health', 'Take 1 tablet daily with food', 'Consult doctor if pregnant or nursing', 4.5, 'best seller', 100, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('WEL-002', 'SUP001', 'Omega-3 Fish Oil', 'wellness', 125000.00, '2028-10-20', '../../assets/OmegaFish.webp', 'Natural omega-3 fish oil that supports heart health, brain function, and reduces inflammation in the body.', 'upports heart health,Improves brain function, Reduces inflammation', 'Take 2 capsules daily with meals', 'Not suitable for those allergic to fish', 5.0, 'new arrival', 80, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002'),
('WEL-003', 'SUP002', 'Vitamin D3 1000 IU', 'wellness', 78000.00, '2028-10-20', '../../assets/vitD3.webp', 'High-potency vitamin D3 supplement that strengthens bones, supports immune function, and improves calcium absorption.', 'Strengthens bones, Supports immune system, Improves calcium absorption', 'Take 1 tablet daily', 'Do not exceed recommended dose', 4.5, 'best seller', 120, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM001'),
('WEL-004', 'SUP002', 'Eucalyptus Oil 30ml', 'wellness', 42000.00, '2028-10-20', '../../assets/eucalypOil.webp', 'Refreshing aromatherapy eucalyptus oil that helps clear respiratory passages and relieves cold symptoms naturally.', 'Clears respiratory passages, Relieves cold symptom, Natural aromatherapy', 'Apply topically or use in diffuser', 'For external use only. Keep away from children', 5.0, 'new arrival', 150, '2025-10-18 13:37:22', '2025-10-19 19:54:03', 'ADM002');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_address` text DEFAULT NULL,
  `order_notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','transfer','ewallet') NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `user_name`, `user_email`, `user_phone`, `user_address`, `order_notes`, `total_amount`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
('O-0001', 'CUS001', 'John Doe', 'john.doe@email.com', '0812-3456-7890', 'Jl. Sudirman No. 123, Jakarta Pusat', 'Resep dari Dr. Andi', 235000.00, '', 'completed', '2025-10-10 02:30:00', '2025-10-19 16:04:05'),
('O-0002', 'CUS006', 'Jane Smith', 'jane.smith@email.com', '0856-7890-1234', 'Jl. Thamrin No. 45, Jakarta Selatan', 'Obat batuk untuk anak', 145000.00, '', 'completed', '2025-10-11 07:20:00', '2025-10-19 16:04:24'),
('O-0003', 'CUS007', 'Ahmad Wijaya', 'ahmad.w@email.com', '0821-5555-6666', 'Jl. Gatot Subroto No. 88, Jakarta Barat', '', 290000.00, 'transfer', 'completed', '2025-10-12 03:15:00', '2025-10-19 16:05:19'),
('O-0004', 'CUS008', 'Siti Nurhaliza', 'siti.n@email.com', '0877-2222-3333', 'Jl. MH Thamrin No. 10, Jakarta Pusat', 'Perlu segera', 180000.00, '', 'completed', '2025-10-13 09:45:00', '2025-10-19 16:05:32'),
('O-0005', 'CUS009', 'Budi Santoso', 'budi.s@email.com', '0813-9999-8888', 'Jl. Kuningan No. 55, Jakarta Selatan', 'Menunggu konfirmasi pembayaran', 420000.00, 'transfer', 'pending', '2025-10-15 01:20:00', '2025-10-19 16:05:42'),
('O-0006', 'CUS010', 'Dewi Lestari', 'dewi.l@email.com', '0898-7777-6666', 'Jl. Rasuna Said No. 12, Jakarta Selatan', 'Untuk persediaan keluarga', 385000.00, '', 'completed', '2025-10-16 04:30:00', '2025-10-19 16:05:51'),
('O-0007', 'CUS011', 'Rina Susanti', 'rina.s@email.com', '0822-4444-5555', 'Jl. Senopati No. 77, Jakarta Selatan', 'Obat sakit kepala', 95000.00, '', 'completed', '2025-10-17 06:50:00', '2025-10-19 16:06:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders_detail`
--

CREATE TABLE `orders_detail` (
  `orderdetail_id` varchar(10) NOT NULL,
  `order_id` varchar(10) NOT NULL,
  `medicine_id` varchar(20) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_detail`
--

INSERT INTO `orders_detail` (`orderdetail_id`, `order_id`, `medicine_id`, `medicine_name`, `quantity`, `price`, `subtotal`) VALUES
('OD-00001', 'O-0001', 'MED-001', 'Bodrex Tablet', 3, 18000.00, 54000.00),
('OD-00002', 'O-0001', 'MED-002', 'OBH Combi Syrup', 2, 35000.00, 70000.00),
('OD-00003', 'O-0001', 'MED-003', 'Panadol Cold & Flu', 4, 28000.00, 112000.00),
('OD-00004', 'O-0002', 'MED-004', 'Balsem Geliga', 5, 24000.00, 120000.00),
('OD-00005', 'O-0002', 'MED-005', 'Alpara Antacid', 1, 28000.00, 28000.00),
('OD-00006', 'O-0003', 'MED-001', 'Bodrex Tablet', 5, 18000.00, 90000.00),
('OD-00007', 'O-0003', 'MED-003', 'Panadol Cold & Flu', 5, 28000.00, 140000.00),
('OD-00008', 'O-0003', 'MED-005', 'Alpara Antacid', 3, 28000.00, 84000.00),
('OD-00009', 'O-0004', 'MED-002', 'OBH Combi Syrup', 6, 35000.00, 210000.00),
('OD-00010', 'O-0004', 'MED-004', 'Balsem Geliga', 3, 24000.00, 72000.00),
('OD-00011', 'O-0004', 'MED-005', 'Alpara Antacid', 1, 28000.00, 28000.00),
('OD-00012', 'O-0005', 'MED-001', 'Bodrex Tablet', 10, 18000.00, 180000.00),
('OD-00013', 'O-0005', 'MED-003', 'Panadol Cold & Flu', 6, 28000.00, 168000.00),
('OD-00014', 'O-0005', 'MED-004', 'Balsem Geliga', 4, 24000.00, 96000.00),
('OD-00015', 'O-0006', 'MED-005', 'Alpara Antacid', 5, 28000.00, 140000.00),
('OD-00016', 'O-0006', 'MED-001', 'Bodrex Tablet', 7, 18000.00, 126000.00),
('OD-00017', 'O-0006', 'MED-002', 'OBH Combi Syrup', 4, 35000.00, 140000.00),
('OD-00018', 'O-0007', 'MED-001', 'Bodrex Tablet', 4, 18000.00, 72000.00),
('OD-00019', 'O-0007', 'MED-004', 'Balsem Geliga', 3, 24000.00, 72000.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` varchar(10) NOT NULL,
  `supplier_id` varchar(10) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_phone` varchar(20) DEFAULT NULL,
  `supplier_address` text DEFAULT NULL,
  `purchase_notes` text DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','received','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `supplier_id`, `supplier_name`, `supplier_phone`, `supplier_address`, `purchase_notes`, `total_amount`, `payment_method`, `status`, `created_at`, `updated_at`, `admin_id`) VALUES
('PUR-0001', 'SUP001', 'PT Sumber Makmur Jaya', '021-5551234', 'Jl. Sudirman No. 123, Jakarta Pusat', 'Pembelian rutin obat-obatan dasar', 13350000.00, 'transfer_bank', 'received', '2025-01-05 02:30:00', '2025-10-19 17:14:32', 'ADM001'),
('PUR-0002', 'SUP002', 'CV Mitra Sejahtera', '022-7654321', 'Jl. Asia Afrika No. 45, Bandung', 'Order obat herbal dan suplemen', 18100000.00, 'transfer_bank', 'received', '2025-01-10 03:15:00', '2025-10-19 17:17:54', 'ADM002'),
('PUR-0003', 'SUP003', 'PT Global Trading Indonesia', '031-8889999', 'Jl. Tunjungan Plaza No. 78, Surabaya', 'Restock vitamin dan paracetamol', 13000000.00, 'transfer_bank', 'received', '2025-01-15 07:20:00', '2025-10-19 17:18:07', 'ADM002'),
('PUR-0004', 'SUP004', 'UD Berkah Supplier', '024-3334444', 'Jl. Pemuda No. 56, Semarang', 'Pembelian antibiotik dan balsem', 14700000.00, 'transfer_bank', 'received', '2025-01-20 04:00:00', '2025-10-19 17:14:32', 'ADM001'),
('PUR-0005', 'SUP005', 'PT Anugrah Distribusi', '0274-5556666', 'Jl. Malioboro No. 89, Yogyakarta', 'Stock obat untuk bulan depan', 10700000.00, 'transfer_bank', 'pending', '2025-01-25 06:45:00', '2025-10-19 17:14:32', 'ADM001');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `purchasedetail_id` varchar(10) NOT NULL,
  `purchase_id` varchar(10) NOT NULL,
  `medicine_id` varchar(20) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_details`
--

INSERT INTO `purchase_details` (`purchasedetail_id`, `purchase_id`, `medicine_id`, `medicine_name`, `quantity`, `price`, `subtotal`) VALUES
('PD-001', 'PUR-0001', 'MED-001', 'Bodrex Tablet', 200, 1500.00, 300000.00),
('PD-002', 'PUR-0001', 'MED-002', 'OBH Combi Syrup', 150, 2500.00, 375000.00),
('PD-003', 'PUR-0001', 'MED-003', 'Panadol Cold & Flu', 100, 5000.00, 500000.00),
('PD-004', 'PUR-0002', 'MED-004', 'Balsem Geliga', 120, 2000.00, 240000.00),
('PD-005', 'PUR-0002', 'MED-005', 'Alpara Antacid', 80, 12000.00, 960000.00),
('PD-006', 'PUR-0002', 'MED-006', 'Diapet Anti-Diarrheal', 200, 1500.00, 300000.00),
('PD-007', 'PUR-0003', 'MED-007', 'Metformin 500mg', 180, 1800.00, 324000.00),
('PD-008', 'PUR-0003', 'MED-008', 'Mixagrip Flu Relief', 90, 10000.00, 900000.00),
('PD-009', 'PUR-0003', 'MED-009', 'Promag Liquid Antacid', 60, 15000.00, 900000.00),
('PD-010', 'PUR-0004', 'MED-010', 'Rhinos Decongestant', 100, 7000.00, 700000.00),
('PD-011', 'PUR-0004', 'MED-011', 'Sanmol Kids Syrup', 60, 8000.00, 480000.00),
('PD-012', 'PUR-0004', 'MED-012', 'Tempra Fever Syrup', 50, 9000.00, 450000.00);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` varchar(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `company_name`, `phone_number`, `address`, `created_at`, `updated_at`) VALUES
('SUP001', 'PT Sumber Makmur Jaya', '021-5551234', 'Jl. Sudirman No. 123, Jakarta Pusat', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP002', 'CV Mitra Sejahtera', '022-7654321', 'Jl. Asia Afrika No. 45, Bandung', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP003', 'PT Global Trading Indonesia', '031-8889999', 'Jl. Tunjungan Plaza No. 78, Surabaya', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP004', 'UD Berkah Supplier', '024-3334444', 'Jl. Pemuda No. 56, Semarang', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP005', 'PT Anugrah Distribusi', '0274-555666', 'Jl. Malioboro No. 89, Yogyakarta', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP006', 'CV Jaya Abadi Sentosa', '061-7778888', 'Jl. Gatot Subroto No. 234, Medan', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP007', 'PT Cahaya Medika Utama', '0511-4445555', 'Jl. Ahmad Yani No. 67, Banjarmasin', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP008', 'UD Surya Pharma Indo', '0361-6667777', 'Jl. Sunset Road No. 45, Denpasar', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP009', 'PT Nusantara Health Supply', '0411-2223333', 'Jl. Ratulangi No. 123, Makassar', '2025-10-18 10:01:15', '2025-10-18 10:01:15'),
('SUP010', 'CV Mandiri Farma Group', '0341-9998888', 'Jl. Ijen Boulevard No. 78, Malang', '2025-10-18 10:01:15', '2025-10-18 10:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `phone_number`, `birth_date`, `gender`, `city`, `province`, `address`, `created_at`, `updated_at`) VALUES
('CUS001', 'johndoe', 'john123', 'John Doe', 'john.doe@gmail.com', '081234567890', '1995-05-15', 'Laki-laki', 'Jakarta Selatan', 'DKI Jakarta', 'Jl. Sudirman No. 123, Kebayoran Baru', '2025-10-18 09:01:41', '2025-10-18 11:00:39'),
('CUS002', 'putribunga', 'putri123', 'putri bunga lestari', 'putri.bunga@gmail.com', '089898989', '2000-01-01', 'Perempuan', 'Bandung', 'Jawa Barat', 'Jl. Braga', '2025-10-18 09:01:41', '2025-10-19 23:20:18'),
('CUS003', 'ahmadrifai', 'ahmad123', 'Ahmad Rifai', 'ahmad.rifai@gmail.com', '082145678901', '1992-03-10', 'Laki-laki', 'Surabaya', 'Jawa Timur', 'Jl. Diponegoro No. 78, Gubeng', '2025-10-18 09:01:41', '2025-10-18 11:01:46'),
('CUS004', 'sitinurhayati', 'siti123', 'Siti Nurhayati', 'siti.nurh@gmail.com', '085678901234', '1999-12-25', 'Perempuan', 'Yogyakarta', 'DI Yogyakarta', 'Jl. Malioboro No. 234, Gedongtengen', '2025-10-18 09:01:41', '2025-10-18 11:01:58'),
('CUS005', 'budiwijaya', 'budi123', 'Budi Wijaya', 'budi.wijaya88@gmail.com', '087712345678', '1988-07-08', 'Laki-laki', 'Semarang', 'Jawa Tengah', 'Jl. Pandanaran No. 156, Semarang Tengah', '2025-10-18 09:01:41', '2025-10-18 11:02:09'),
('CUS006', 'janesmith', 'jane123\r\n', 'Jane Smith', 'jane.smith@email.com', '0856-7890-1234', '1992-03-20', 'Perempuan', 'Jakarta Selatan', 'DKI Jakarta', 'Jl. Thamrin No. 45, Jakarta Selatan', '2025-10-19 13:55:03', '2025-10-19 13:55:36'),
('CUS007', 'ahmadwijaya', 'ahmad123\r\n', 'Ahmad Wijaya', 'ahmad.w@email.com', '0821-5555-6666', '1988-07-10', 'Laki-laki', 'Jakarta Barat', 'DKI Jakarta', 'Jl. Gatot Subroto No. 88, Jakarta Barat', '2025-10-19 13:55:03', '2025-10-19 13:56:02'),
('CUS008', 'sitinurhaliza', 'liza123\r\n\r\n', 'Siti Nurhaliza', 'siti.n@email.com', '0877-2222-3333', '1995-05-25', 'Perempuan', 'Jakarta Pusat', 'DKI Jakarta', 'Jl. MH Thamrin No. 10, Jakarta Pusat', '2025-10-19 13:55:03', '2025-10-19 14:05:40'),
('CUS009', 'budisantoso', 'santoso123', 'Budi Santoso', 'budi.s@email.com', '0813-9999-8888', '1985-11-30', 'Laki-laki', 'Jakarta Selatan', 'DKI Jakarta', 'Jl. Kuningan No. 55, Jakarta Selatan', '2025-10-19 13:55:03', '2025-10-19 13:57:13'),
('CUS010', 'dewilestari', 'lestari123\r\n', 'Dewi Lestari', 'dewi.l@email.com', '0898-7777-6666', '1993-09-12', 'Perempuan', 'Jakarta Selatan', 'DKI Jakarta', 'Jl. Rasuna Said No. 12, Jakarta Selatan', '2025-10-19 13:55:03', '2025-10-19 13:57:30'),
('CUS011', 'rinasusanti', 'rina123\r\n', 'Rina Susanti', 'rina.s@email.com', '0822-4444-5555', '1991-02-18', 'Perempuan', 'Jakarta Selatan', 'DKI Jakarta', 'Jl. Senopati No. 77, Jakarta Selatan', '2025-10-19 13:55:03', '2025-10-19 13:57:46');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_all_transactions`
-- (See below for the actual view)
--
CREATE TABLE `vw_all_transactions` (
`transaction_id` varchar(10)
,`transaction_number` varchar(10)
,`transaction_type` varchar(3)
,`transaction_date` timestamp
,`partner_id` varchar(10)
,`partner_name` varchar(255)
,`partner_phone` varchar(20)
,`partner_email` varchar(255)
,`partner_address` mediumtext
,`notes` mediumtext
,`total_amount` decimal(12,2)
,`payment_method` varchar(50)
,`status` varchar(10)
,`admin_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transaction_details`
-- (See below for the actual view)
--
CREATE TABLE `vw_transaction_details` (
`detail_id` varchar(10)
,`transaction_id` varchar(10)
,`transaction_type` varchar(3)
,`medicine_id` varchar(20)
,`medicine_name` varchar(255)
,`quantity` int(11)
,`price` decimal(12,2)
,`subtotal` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transaction_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_transaction_summary` (
`transaction_type` varchar(3)
,`status` varchar(10)
,`total_transactions` bigint(21)
,`total_amount` decimal(34,2)
,`month_year` varchar(7)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_all_transactions`
--
DROP TABLE IF EXISTS `vw_all_transactions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_all_transactions`  AS SELECT `o`.`order_id` AS `transaction_id`, `o`.`order_id` AS `transaction_number`, 'OUT' AS `transaction_type`, `o`.`created_at` AS `transaction_date`, `o`.`user_id` AS `partner_id`, `o`.`user_name` AS `partner_name`, `o`.`user_phone` AS `partner_phone`, `o`.`user_email` AS `partner_email`, `o`.`user_address` AS `partner_address`, `o`.`order_notes` AS `notes`, `o`.`total_amount` AS `total_amount`, `o`.`payment_method` AS `payment_method`, `o`.`status` AS `status`, NULL AS `admin_name` FROM `orders` AS `o`union all select `p`.`purchase_id` AS `transaction_id`,`p`.`purchase_id` AS `transaction_number`,'IN' AS `transaction_type`,`p`.`created_at` AS `transaction_date`,`p`.`supplier_id` AS `partner_id`,`p`.`supplier_name` AS `partner_name`,`p`.`supplier_phone` AS `partner_phone`,NULL AS `partner_email`,`p`.`supplier_address` AS `partner_address`,`p`.`purchase_notes` AS `notes`,`p`.`total_amount` AS `total_amount`,`p`.`payment_method` AS `payment_method`,`p`.`status` AS `status`,`a`.`username` AS `admin_name` from (`purchases` `p` left join `admins` `a` on(`a`.`admin_id` = `p`.`admin_id`))  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transaction_details`
--
DROP TABLE IF EXISTS `vw_transaction_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transaction_details`  AS SELECT `od`.`orderdetail_id` AS `detail_id`, `od`.`order_id` AS `transaction_id`, 'OUT' AS `transaction_type`, `od`.`medicine_id` AS `medicine_id`, `od`.`medicine_name` AS `medicine_name`, `od`.`quantity` AS `quantity`, `od`.`price` AS `price`, `od`.`subtotal` AS `subtotal` FROM `orders_detail` AS `od`union all select `pd`.`purchasedetail_id` AS `detail_id`,`pd`.`purchase_id` AS `transaction_id`,'IN' AS `transaction_type`,`pd`.`medicine_id` AS `medicine_id`,`pd`.`medicine_name` AS `medicine_name`,`pd`.`quantity` AS `quantity`,`pd`.`price` AS `price`,`pd`.`subtotal` AS `subtotal` from `purchase_details` `pd`  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transaction_summary`
--
DROP TABLE IF EXISTS `vw_transaction_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transaction_summary`  AS SELECT `vw_all_transactions`.`transaction_type` AS `transaction_type`, `vw_all_transactions`.`status` AS `status`, count(0) AS `total_transactions`, sum(`vw_all_transactions`.`total_amount`) AS `total_amount`, date_format(`vw_all_transactions`.`transaction_date`,'%Y-%m') AS `month_year` FROM `vw_all_transactions` GROUP BY `vw_all_transactions`.`transaction_type`, `vw_all_transactions`.`status`, date_format(`vw_all_transactions`.`transaction_date`,'%Y-%m') ORDER BY date_format(`vw_all_transactions`.`transaction_date`,'%Y-%m') DESC, `vw_all_transactions`.`transaction_type` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `medicines_ibfk_1` (`supplier_id`),
  ADD KEY `fk_medicines_admin` (`admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD PRIMARY KEY (`orderdetail_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_medicine` (`medicine_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `fk_supplier` (`supplier_id`),
  ADD KEY `fk_purchases_admin` (`admin_id`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`purchasedetail_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `fk_medicines_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD CONSTRAINT `orders_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_detail_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_purchases_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `fk_pd_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_purchase_details_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
