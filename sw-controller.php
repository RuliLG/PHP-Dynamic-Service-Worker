<?
header('Content-Type: application/javascript');
?>
function _registerServiceWorker() {
	if (!navigator.serviceWorker) return;
	navigator.serviceWorker.register('/sw.js').then(function(reg) {
		if (!navigator.serviceWorker.controller) {
			return;
		}

		if (reg.waiting) {
			_updateReady(reg.waiting);
			return;
		}

		if (reg.installing) {
			_trackInstalling(reg.installing);
			return;
		}

		reg.addEventListener('updatefound', function() {
			_trackInstalling(reg.installing);
		});
	});

	// Ensure refresh is only called once.
	// This works around a bug in "force update on reload".
	var refreshing;
	navigator.serviceWorker.addEventListener('controllerchange', function() {
		if (refreshing) return;
		window.location.reload();
		refreshing = true;
	});
};

_registerServiceWorker();

function _trackInstalling(worker) {
	worker.addEventListener('statechange', function() {
		if (worker.state == 'installed') {
			_updateReady(worker);
		}
	});
};

function _updateReady(worker) {
	worker.postMessage({action: 'skipWaiting'});
};