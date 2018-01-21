<?
require_once "ServiceWorker.php";

// Files to cache
$files = array();
$sw = new ServiceWorker($files);

header('Content-Type: application/javascript');
?>

var staticCacheName = "<?=$sw->getCacheName();?>-<?=$sw->getVersion();?>";

self.addEventListener("install", function(event) {
  event.waitUntil(
    caches.open(staticCacheName).then(function(cache) {
      return cache.addAll([
        "/",
        <? foreach ($sw->getFiles() as $file):?>
        "/<?=$file;?>",
        <? endforeach;?>
      ]);
    })
  );
});

self.addEventListener("activate", function(event) {
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.filter(function(cacheName) {
          return cacheName.startsWith("<?=$sw->getCacheName();?>-") &&
            cacheName != staticCacheName;
        }).map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    })
  );
});

self.addEventListener("fetch", function(event) {
  event.respondWith(
    caches.match(event.request).then(function(response) {
      return response || fetch(event.request);
    })
  );
});

self.addEventListener("message", function(event) {
  if (event.data.action === "skipWaiting") {
    self.skipWaiting();
  }
});