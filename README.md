# Localement Suisse

### Useful links
- [Behance](https://www.behance.net/gallery/203646405/Localement-Suisse?share=1)
- [Tech Doc](./LocalementSuisse_DocTech.pdf)



## Project
The project is a e-commerce website where people will be able to purchase Swiss made products.

## DB
To get the database, get the [db.sql](./DB) file from the DB directory.

Don't forget to update your correct information in the [db.php](./db.php) file, to make the project able to get access to the database.

If you want to log in with a client account:
```
email: pintokevin2002@hotmail.com
password: 12345678
It's also an admin account
```

When you access the admin dashboard, you'll need to login again. Just to be sure you're the right person.

If you want to log in with a seller account:
```
email: pintokevin2002@hotmail.com
password: 12345678
But you'll need to check that you want to connect as a seller
```


## Dependencies 
To use the project, you'll have to get those libraries:
```json
"phpmailer/phpmailer": "^6.9",
"stichoza/google-translate-php": "^5.1",
"erusev/parsedown": "^1.7"
```
You can find those in the [composer.json](./composer.json) file.

## ImageMagick
The project use the PHP extension `ImagIck` to handle images.

[You can check this website to download ImageMagick, there's even the explanation how to install it.](https://mlocati.github.io/articles/php-windows-imagick.html)

### 1. Installing the `php_imagick.dll` file
To start off, you'll need to save the `php_imagick.dll` file to the `ext` directory of your PHP installation.

### 2. Setting up the others DLL files
From the `PATH` directory, copy all the files and paste them into the root directory of your PHP (that's where your `php.exe` is).

### 3. Setting your `php.ini`
In your `php.ini` file, you'll need to add this line:

`extension=php_imagick.dll`

### 4. Restart the Apache/NGINX Windows service (if applicable)
Yeah, you should probably do that.

### 5. You've now successfully installed ImageMagick
If you're not sure, you can try to use this code

```php
<?php
$image = new Imagick();
$image->newImage(1, 1, new ImagickPixel('#ffffff'));
$image->setImageFormat('png');
$pngData = $image->getImagesBlob();
echo strpos($pngData, "\x89PNG\r\n\x1a\n") === 0 ? 'Ok' : 'Failed'; 
```

If you have successfully installed ImageMagick, you should end up with a `ok` on your page.
