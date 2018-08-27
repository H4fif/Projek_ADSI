-- MySQL dump 10.16  Distrib 10.1.34-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: projek_adsi
-- ------------------------------------------------------
-- Server version	10.1.34-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tb_akun`
--

DROP TABLE IF EXISTS `tb_akun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_akun` (
  `kode_akun` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) NOT NULL,
  `kata_sandi` varchar(40) NOT NULL,
  `akses` enum('administrator','gudang','kasir','manager') NOT NULL,
  `kode_pegawai` int(11) NOT NULL,
  PRIMARY KEY (`kode_akun`),
  UNIQUE KEY `nama_pengguna` (`email`),
  UNIQUE KEY `email` (`email`),
  KEY `kode_pegawai` (`kode_pegawai`),
  KEY `login` (`email`,`kata_sandi`,`akses`),
  CONSTRAINT `tb_akun_ibfk_1` FOREIGN KEY (`kode_pegawai`) REFERENCES `tb_pegawai` (`kode_pegawai`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_akun`
--

LOCK TABLES `tb_akun` WRITE;
/*!40000 ALTER TABLE `tb_akun` DISABLE KEYS */;
INSERT INTO `tb_akun` VALUES (1,'john@beatles.com','2a50435b0f512f60988db719106a258fb7e338ff','administrator',1),(2,'paul@beatles.com','6ae16792c502a5b47da180ce8456e5ae7d65e262','manager',2),(3,'george@beatles.com','1af17e73721dbe0c40011b82ed4bb1a7dbe3ce29','gudang',3),(4,'ringo@beatles.com','520f73691bcf89d508d923a2dbc8e6fa58efb522','kasir',4),(6,'kasir2@email.com','08dfc5f04f9704943a423ea5732b98d3567cbd49','kasir',7),(7,'gudang1@email.com','69c790d6e836dbbe1d6417e7d2300f6570e81125','gudang',9),(8,'manager1@email.com','a5c297c15e40ac3881db51277613aea3731b673a','manager',10),(9,'admin2@email.com','315f166c5aca63a157f7d41007675cb44a948b33','administrator',6),(10,'kasir3@email.comasdf','dd4fab4a0925326b97aeb5435b0016b1f4ad9863','kasir',12),(12,'gudang2@email.com','c9708f9a1980cd4a606a39388cd5f7297034d2fb','gudang',13),(13,'manager2@email.com','d2c9a46b3870e03e3c45c6a6ba0d7a574f50c698','manager',14),(14,'admin3@email.com','33aab3c7f01620cade108f488cfd285c0e62c1ec','administrator',11),(15,'kasir1@email.com','874c0ac75f323057fe3b7fb3f5a8a41df2b94b1d','kasir',8),(16,'adasdf@afdasdf.adf','1161e6ffd3637b302a5cd74076283a7bd1fc20d3','kasir',31);
/*!40000 ALTER TABLE `tb_akun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_barang`
--

DROP TABLE IF EXISTS `tb_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_barang` (
  `kode_barang` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `jumlah_stok` smallint(6) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `kode_supplier` int(11) NOT NULL,
  PRIMARY KEY (`kode_barang`),
  UNIQUE KEY `nama_barang` (`nama_barang`),
  KEY `kode_supplier` (`kode_supplier`),
  KEY `cari` (`nama_barang`,`harga`,`kategori`,`jumlah_stok`,`deskripsi`),
  CONSTRAINT `tb_barang_ibfk_1` FOREIGN KEY (`kode_supplier`) REFERENCES `tb_supplier` (`kode_supplier`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_barang`
--

LOCK TABLES `tb_barang` WRITE;
/*!40000 ALTER TABLE `tb_barang` DISABLE KEYS */;
INSERT INTO `tb_barang` VALUES (1,'Aqua Botol 600 ml',3000,'Minuman',10,NULL,1),(2,'Indomie Goreng 90 gr',2500,'Makanan',10,NULL,2),(3,'Saus Sambal ABC 60 ml',6000,'Makanan',10,NULL,3),(4,'Chitato Rasa Ayam Panggang 90 gr',3000,'Makanan',10,NULL,2);
/*!40000 ALTER TABLE `tb_barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_detail_pembelian`
--

DROP TABLE IF EXISTS `tb_detail_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_detail_pembelian` (
  `kode_detail_faktur` int(11) NOT NULL AUTO_INCREMENT,
  `qty` tinyint(4) NOT NULL,
  `kode_faktur` int(11) NOT NULL,
  `kode_barang` int(11) NOT NULL,
  PRIMARY KEY (`kode_detail_faktur`),
  KEY `kode_faktur` (`kode_faktur`),
  KEY `kode_barang` (`kode_barang`),
  KEY `cari` (`qty`),
  CONSTRAINT `tb_detail_pembelian_ibfk_1` FOREIGN KEY (`kode_faktur`) REFERENCES `tb_pembelian` (`kode_faktur`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tb_detail_pembelian_ibfk_2` FOREIGN KEY (`kode_barang`) REFERENCES `tb_barang` (`kode_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_detail_pembelian`
--

LOCK TABLES `tb_detail_pembelian` WRITE;
/*!40000 ALTER TABLE `tb_detail_pembelian` DISABLE KEYS */;
INSERT INTO `tb_detail_pembelian` VALUES (1,10,1,1),(2,10,2,2),(3,10,3,3),(4,10,4,4);
/*!40000 ALTER TABLE `tb_detail_pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_detail_penjualan`
--

DROP TABLE IF EXISTS `tb_detail_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_detail_penjualan` (
  `kode_detail_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `qty` tinyint(4) NOT NULL,
  `kode_transaksi` int(11) NOT NULL,
  `kode_barang` int(11) NOT NULL,
  PRIMARY KEY (`kode_detail_transaksi`),
  KEY `kode_transaksi` (`kode_transaksi`),
  KEY `kode_barang` (`kode_barang`),
  KEY `cari` (`qty`),
  CONSTRAINT `tb_detail_penjualan_ibfk_1` FOREIGN KEY (`kode_transaksi`) REFERENCES `tb_penjualan` (`kode_transaksi`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tb_detail_penjualan_ibfk_2` FOREIGN KEY (`kode_barang`) REFERENCES `tb_barang` (`kode_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_detail_penjualan`
--

LOCK TABLES `tb_detail_penjualan` WRITE;
/*!40000 ALTER TABLE `tb_detail_penjualan` DISABLE KEYS */;
INSERT INTO `tb_detail_penjualan` VALUES (1,1,1,1),(2,2,2,2),(3,3,3,3),(4,4,4,4);
/*!40000 ALTER TABLE `tb_detail_penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pegawai`
--

DROP TABLE IF EXISTS `tb_pegawai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_pegawai` (
  `kode_pegawai` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `tgl_lahir` date NOT NULL,
  PRIMARY KEY (`kode_pegawai`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pegawai`
--

LOCK TABLES `tb_pegawai` WRITE;
/*!40000 ALTER TABLE `tb_pegawai` DISABLE KEYS */;
INSERT INTO `tb_pegawai` VALUES (1,'John Lennon','L','123456789','New York','1993-03-26'),(2,'Paul McCartney','L','123456788','California','1993-03-26'),(3,'George Harrison','L','123456787','Nevada','1993-03-26'),(4,'Ringo Starr','L','123456786','Los Angeles','1993-03-26'),(5,'admin1','L','12345','jl. abc','1993-03-26'),(6,'admin2','P','12345','Jl. admin2','1993-03-26'),(7,'kasir2','P','12345','Jl. Kasir No. 2','1993-03-26'),(8,'kasir1','L','123456789','New York','1993-03-26'),(9,'gudang1','L','123456788','California','1993-03-26'),(10,'manager1','L','123456787','Nevada','1993-03-26'),(11,'admin3','L','123456786','Los Angeles','1982-06-26'),(12,'kasir3','P','123456789','New York','1982-06-26'),(13,'gudang2','P','123456788','California','1982-06-26'),(14,'manager2','P','123456787','Nevada','1982-06-26'),(15,'kasir4','P','123456786','Los Angeles','1982-06-26'),(16,'Hafif_Imammuddyn','L','123412341234','alert(\'xcc attack!\');','1982-06-26'),(17,'John\'s','L','1234134','london, us.','1982-06-26'),(18,'kim\'s','L','12345321','north korea','1982-06-26'),(19,'minsjong','L','87483294710','south korea','1982-06-26'),(21,'yuda ha\'san','L','342347978','bandung, jawa barat, indonesia','1989-08-12'),(22,'qwery\'','P','123412414','qwery st. 123','1989-08-12'),(23,'abc de\'f','P','792345','jl. abc no. 123 kota depok qweoriuqwerpiouqwepioruqweporiuqewpioruqweoriu','1989-08-12'),(24,'a Nasution','L','234124','Jl. A.H. Nasution','1989-08-12'),(25,'a. badf','L','12342144','afsfafff','1989-08-12'),(26,'H. Ma\'ruf','L','3432134243','adfafdfa 1234134134','1989-08-12'),(28,'aldkskfajf','L','34141412','dafdfdaff','1989-08-12'),(29,'H. amir s.k.','L','34141234124','afadfadfa','1989-08-12'),(30,'.adf.\'adff','L','134141234','xzvzxvzxcvzx','1989-08-12'),(31,'H. Kalap S.Kom.','L','96796','uiooyuioyuioy','1989-08-12'),(32,'admin1','P','789124789012','qpoewiur;lakdf.mn aeoriqpr','1989-08-12');
/*!40000 ALTER TABLE `tb_pegawai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pelanggan`
--

DROP TABLE IF EXISTS `tb_pelanggan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_pelanggan` (
  `kode_pelanggan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `tgl_lahir` date NOT NULL,
  PRIMARY KEY (`kode_pelanggan`),
  KEY `data` (`nama_pelanggan`,`jenis_kelamin`,`no_telepon`,`alamat`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pelanggan`
--

LOCK TABLES `tb_pelanggan` WRITE;
/*!40000 ALTER TABLE `tb_pelanggan` DISABLE KEYS */;
INSERT INTO `tb_pelanggan` VALUES (1,'David Jones','L','223456789','Bandung','1987-06-26'),(2,'Peter Tork','L','223456788','Cirebon','1987-06-26'),(3,'Micky Dolenz','L','223456787','Depok','1987-06-26'),(4,'Mike Nesmith','L','223456786','Garut','1987-06-26'),(5,'Cofang Wilson','L','223456542','New Castle','1987-06-26'),(6,'Micky Mouse','L','758456787','Disney Island','1987-06-26'),(7,'Michael Jordan','L','999456786','New York','1987-06-26'),(8,'Peter Parker','L','221456788','New York','1987-06-26'),(9,'Lavinia Hamill','P','221456788','Sheldon Lakes','1987-06-26'),(10,'Dr. Imogen Doyle','L','0578610464','Kai Way','1987-06-26'),(11,'Miss Dessie Lakin','P','18023545890','Hintz Squares','1994-11-26'),(12,'Ms. Bonnie Hoeger','P','3384833744','Norene Plains','1994-11-26'),(13,'Bart Kling','L','8452460713','Braun Dam','1994-11-26'),(14,'Suzanne Keebler','P','16060323293','Shanahan Spring','1994-11-26'),(15,'Ruthie Rosenbaum','P','19590322425','Jeffry Port','1994-11-26'),(16,'Carmine Hessel','P','19447980246','Waters Station','1994-11-26'),(17,'Makenzie Veum DVM','P','7442297840','Kristin Points','1994-11-26'),(18,'Bernhard Schroeder Sr.','L','19225165601','Boehm Street','1994-11-26'),(19,'Jaqueline Hartmann','P','15738435641','Kshlerin Cove','1994-11-26'),(20,'Amanda Swift','P','1549034596','Mason Field','1994-11-26'),(21,'Era Jaskolski','P','5490039172','Danielle Bridge','1976-01-26'),(22,'Leon Nienow','L','6642252696','Paucek Highway','1976-01-26'),(23,'Adrain Rohan','L','1111602849','Gorczany Neck','1976-01-26'),(24,'Cleveland Sipes','L','9000461833','Dax Highway','1976-01-26'),(25,'Dr. Camron Dibbert','L','2017608961','Camren Springs','1976-01-26'),(26,'Estelle Spinka DDS','P','9977873530','Hertha Garden','1976-01-26'),(28,'a\'i','P','7809431','jl manis kidul, kuningan 12345','1976-01-26'),(29,'gudang7','P','5173907491234','qpeworzlkj asdf.,nqwer34314','1995-08-15');
/*!40000 ALTER TABLE `tb_pelanggan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pembelian`
--

DROP TABLE IF EXISTS `tb_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_pembelian` (
  `kode_faktur` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_beli` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kode_pegawai` int(11) NOT NULL,
  PRIMARY KEY (`kode_faktur`),
  KEY `kode_pegawai` (`kode_pegawai`),
  KEY `cari` (`tanggal_beli`),
  CONSTRAINT `tb_pembelian_ibfk_1` FOREIGN KEY (`kode_pegawai`) REFERENCES `tb_pegawai` (`kode_pegawai`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pembelian`
--

LOCK TABLES `tb_pembelian` WRITE;
/*!40000 ALTER TABLE `tb_pembelian` DISABLE KEYS */;
INSERT INTO `tb_pembelian` VALUES (1,'2018-05-30 05:05:20',2),(2,'2018-05-30 05:00:14',2),(3,'2018-05-30 05:05:20',2),(4,'2018-05-30 05:05:20',2);
/*!40000 ALTER TABLE `tb_pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_penjualan`
--

DROP TABLE IF EXISTS `tb_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_penjualan` (
  `kode_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kode_pelanggan` int(11) NOT NULL,
  `kode_pegawai` int(11) NOT NULL,
  PRIMARY KEY (`kode_transaksi`),
  KEY `kode_pelanggan` (`kode_pelanggan`),
  KEY `kode_pegawai` (`kode_pegawai`),
  KEY `cari` (`tanggal_transaksi`),
  CONSTRAINT `tb_penjualan_ibfk_1` FOREIGN KEY (`kode_pelanggan`) REFERENCES `tb_pelanggan` (`kode_pelanggan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tb_penjualan_ibfk_2` FOREIGN KEY (`kode_pegawai`) REFERENCES `tb_pegawai` (`kode_pegawai`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_penjualan`
--

LOCK TABLES `tb_penjualan` WRITE;
/*!40000 ALTER TABLE `tb_penjualan` DISABLE KEYS */;
INSERT INTO `tb_penjualan` VALUES (1,'2018-05-30 05:04:45',1,3),(2,'2018-05-30 05:04:45',2,3),(3,'2018-05-30 05:04:45',3,3),(4,'2018-05-30 05:04:45',4,3);
/*!40000 ALTER TABLE `tb_penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_supplier`
--

DROP TABLE IF EXISTS `tb_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_supplier` (
  `kode_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(150) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`kode_supplier`),
  UNIQUE KEY `nama_supplier` (`nama_supplier`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_supplier`
--

LOCK TABLES `tb_supplier` WRITE;
/*!40000 ALTER TABLE `tb_supplier` DISABLE KEYS */;
INSERT INTO `tb_supplier` VALUES (1,'AQUA','Bandung','323456789','aqua@email.com'),(2,'INDOFOOD','Cirebon','323456788','indofood@email.com'),(3,'ABC','Depok','323456787','abc@email.com'),(4,'MAYORA','Garut','323456786','mayora@email.com');
/*!40000 ALTER TABLE `tb_supplier` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-27  7:02:16
