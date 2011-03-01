USE [master]
GO

/****** Object:  Database [sentiment_engine]    Script Date: 02/24/2011 18:22:01 ******/
IF  EXISTS (SELECT name FROM sys.databases WHERE name = N'sentiment_engine')
DROP DATABASE [sentiment_engine]
GO

USE [master]
GO

/****** Object:  Database [sentiment_engine]    Script Date: 02/24/2011 18:22:01 ******/
CREATE DATABASE [sentiment_engine] ON  PRIMARY 
( NAME = N'sentiment_engine', FILENAME = N'c:\Program Files\Microsoft SQL Server\MSSQL10.SQLEXPRESS\MSSQL\DATA\sentiment_engine.mdf' , SIZE = 3072KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'sentiment_engine_log', FILENAME = N'c:\Program Files\Microsoft SQL Server\MSSQL10.SQLEXPRESS\MSSQL\DATA\sentiment_engine_log.ldf' , SIZE = 1024KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO

ALTER DATABASE [sentiment_engine] SET COMPATIBILITY_LEVEL = 100
GO

IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [sentiment_engine].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO

ALTER DATABASE [sentiment_engine] SET ANSI_NULL_DEFAULT OFF 
GO

ALTER DATABASE [sentiment_engine] SET ANSI_NULLS OFF 
GO

ALTER DATABASE [sentiment_engine] SET ANSI_PADDING OFF 
GO

ALTER DATABASE [sentiment_engine] SET ANSI_WARNINGS OFF 
GO

ALTER DATABASE [sentiment_engine] SET ARITHABORT OFF 
GO

ALTER DATABASE [sentiment_engine] SET AUTO_CLOSE OFF 
GO

ALTER DATABASE [sentiment_engine] SET AUTO_CREATE_STATISTICS ON 
GO

ALTER DATABASE [sentiment_engine] SET AUTO_SHRINK OFF 
GO

ALTER DATABASE [sentiment_engine] SET AUTO_UPDATE_STATISTICS ON 
GO

ALTER DATABASE [sentiment_engine] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO

ALTER DATABASE [sentiment_engine] SET CURSOR_DEFAULT  GLOBAL 
GO

ALTER DATABASE [sentiment_engine] SET CONCAT_NULL_YIELDS_NULL OFF 
GO

ALTER DATABASE [sentiment_engine] SET NUMERIC_ROUNDABORT OFF 
GO

ALTER DATABASE [sentiment_engine] SET QUOTED_IDENTIFIER OFF 
GO

ALTER DATABASE [sentiment_engine] SET RECURSIVE_TRIGGERS OFF 
GO

ALTER DATABASE [sentiment_engine] SET  DISABLE_BROKER 
GO

ALTER DATABASE [sentiment_engine] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO

ALTER DATABASE [sentiment_engine] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO

ALTER DATABASE [sentiment_engine] SET TRUSTWORTHY OFF 
GO

ALTER DATABASE [sentiment_engine] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO

ALTER DATABASE [sentiment_engine] SET PARAMETERIZATION SIMPLE 
GO

ALTER DATABASE [sentiment_engine] SET READ_COMMITTED_SNAPSHOT OFF 
GO

ALTER DATABASE [sentiment_engine] SET HONOR_BROKER_PRIORITY OFF 
GO

ALTER DATABASE [sentiment_engine] SET  READ_WRITE 
GO

ALTER DATABASE [sentiment_engine] SET RECOVERY SIMPLE 
GO

ALTER DATABASE [sentiment_engine] SET  MULTI_USER 
GO

ALTER DATABASE [sentiment_engine] SET PAGE_VERIFY CHECKSUM  
GO

ALTER DATABASE [sentiment_engine] SET DB_CHAINING OFF 
GO

