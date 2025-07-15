# Perodex

Perodex is an application for trading digital assets. In this first iteration of the project, a proof of concept will be written using PHP without frameworks or external libraries.

## Getting Started

The first step for setting up the development environment is to install the PHP programming language.

### Install PHP

#### macOS

An easy way to install PHP on macOS is through the [Homebrew](https://brew.sh) packaging manager.

Further instuctions can be found on the [PHP website](https://www.php.net/manual/en/install.macosx.packages.php).

#### Windows

Installation on Windows requires the download of a zip file and manual updating of the Path environment variable.

To install PHP, first go to the [PHP website](https://php.net). 

In the Downloads site, find the zip file for PHP version 8.4.

- [Download Link](https://windows.php.net/download#php-8.4)

Next, extract the files and move the folder to the C:\ drive.

Lastly, update the Path environment variable.

Video walkthrough: 
[(YouTube) How to install PHP in 1 minute](https://www.youtube.com/watch?v=n04w2SzGr_U)


## Discord API

There are a few settings to update in the PHP installation to connect with the Discord API.

- Update the `php.ini` configuration file to enable cURL
- Provide a list of trusted certificate authorities via a `caert.pem` file


### Enable cURL in Windows PHP Installation

To use cURL, find the `php.ini-development` file in the PHP location in the C:\ drive. Create a copy and name it `php.ini`. 

Remove the colon commenting out the `;extension=curl` line. 

Also remove the colon commenting out the `;extension_dir = "ext"` line.

Save the file.


### Download the CA bundle

Download the CA bundle from <https://curl.se/ca/caert.pem>

Save the file as `caert.pem` in the PHP folder (e.g., `C:\php-8.4.10\extras\ssl\`).

In the `php.ini` file, find the `;curl.cainfo =` and replace it with:

```
curl.cainfo = "C:\php-8.4.10\extras\ssl\cacert.pem"
```

