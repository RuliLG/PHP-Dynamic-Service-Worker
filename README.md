# Dynamic Service Worker with PHP

I've recently finished the Google Developer Challenge Scholarship, where they taught us how Service Workers work. I found it pretty amazing (if you don't know what a Service Worker is, you can take a look here: [https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API]()), so I developed a PHP solution to generate dynamic Service Workers.

Let's take a look at it:

## Functions
### h function
`h` function is pretty simple: It takes a filepath, for example `/css/style.css` and gets the *last modified time* in seconds and append it to the filename, so the original filepath is now `/css/style-hsh{MODIFIED_TIME_BASE_36}.css`.

## Service Worker
It's formed of 3 main files:

### ServiceWorker.php
PHP Class with all the logic of hashing files, checking they actually do exist, cache version...

You don't have to worry about this. You only need to change anything if you want to set a default file list of items to be cached.

### sw.php
PHP File that generate a dynamic Service Worker JS file on the fly. It works by creating a ServiceWorker object with the list of files to be cached.

```php
require_once "ServiceWorker.php";

$files = array();
$sw = new ServiceWorker($files);
```

### sw-controller.php
Nothing special here, just JS stuff.

## That's great, but how do I implement it?
- First of all, copy the `sw` folder somewhere in your server.
- In the .htaccess file:

```
<IfModule mod_rewrite.c>
    RewriteEngine on
    
    RewriteRule ^sw.js$ {PATH_TO_SW_FOLDER}/sw/sw.php [L] # Redirects sw.js file to the php file
    RewriteRule ^(.+)/sw-controller.js$ {PATH_TO_SW_FOLDER}/sw/sw-controller.php [L] # Redirects sw-controller.js to the php file
    RewriteRule ^(.+)-hsh([A-Za-z0-9]+).(.+)$ $1.$3 [L] # Redirects the hashed file to the unhashed file

</IfModule>
```

- At the end of the `body` tag:

```php
<script src="<?=h('/js/sw-controller.js');?>"></script>
```

- When loading assets:

```php
<link rel="stylesheet" href="<?=h('{ASSET}');?>">
```

And you're all done!
