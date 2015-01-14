<?php
/**
 * ownCloud - galleryplus
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Olivier Paroz <owncloud@interfasys.ch>
 *
 * @copyright Olivier Paroz 2014-2015
 */

namespace OCA\GalleryPlus\AppInfo;


use OCP\IContainer;

use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;

use OCA\GalleryPlus\Controller\PageController;
use OCA\GalleryPlus\Controller\ServiceController;
use OCA\GalleryPlus\Controller\PublicServiceController;
use OCA\GalleryPlus\Preview\Preview;
use OCA\GalleryPlus\Service\EnvironmentService;
use OCA\GalleryPlus\Service\InfoService;
use OCA\GalleryPlus\Service\ThumbnailService;
use OCA\GalleryPlus\Service\PreviewService;
use OCA\GalleryPlus\Middleware\SharingCheckMiddleware;
use OCA\GalleryPlus\Middleware\TokenCheckMiddleware;
use OCA\GalleryPlus\Utility\SmarterLogger;
use OCA\GalleryPlus\Utility\Normalizer;


/**
 * Class Application
 *
 * @package OCA\GalleryPlus\AppInfo
 */
class Application extends App {

	/**
	 * Constructor
	 *
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = []) {
		parent::__construct('galleryplus', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService(
			'PageController', function (IContainer $c) {
			return new PageController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('Environment'),
				$c->query('URLGenerator')
			);
		}
		);
		$container->registerService(
			'ServiceController', function (IContainer $c) {
			return new ServiceController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('InfoService'),
				$c->query('ThumbnailService'),
				$c->query('PreviewService'),
				$c->query('URLGenerator')
			);
		}
		);
		$container->registerService(
			'PublicServiceController', function (IContainer $c) {
			return new PublicServiceController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('InfoService'),
				$c->query('ThumbnailService'),
				$c->query('PreviewService'),
				$c->query('URLGenerator')
			);
		}
		);

		/**
		 * Core
		 */
		$container->registerService(
			'UserId', function (IContainer $c) {
			return $c->query('Session')
					 ->get('user_id');
		}
		);
		$container->registerService(
			'Token', function (IContainer $c) {
			return $c->query('Request')
					 ->getParam('token');
		}
		);
		$container->registerService(
			'UserManager', function (IAppContainer $c) {
			// This can retrieve information about any user, not just the one logged-in
			return $c->getServer()
					 ->getUserManager();
		}
		);
		$container->registerService(
			'Session', function (IAppContainer $c) {
			return $c->getServer()
					 ->getSession();
		}
		);
		$container->registerService(
			'L10N', function (IAppContainer $c) {
			return $c->getServer()
					 ->getL10N('gallery'); // Keep the same translations
		}
		);
		$container->registerService(
			'URLGenerator', function (IAppContainer $c) {
			return $c->getServer()
					 ->getURLGenerator();
		}
		);
		$container->registerService(
			'Logger', function (IAppContainer $c) {
			return $c->getServer()
					 ->getLogger();
		}
		);
		$container->registerService(
			'Normalizer', function () {
			return new Normalizer();
		}
		);
		$container->registerService(
			'SmarterLogger', function (IContainer $c) {
			return new SmarterLogger(
				$c->query('AppName'),
				$c->query('Logger'),
				$c->query('Normalizer')
			);
		}
		);
		$container->registerService(
			'RootFolder', function (IAppContainer $c) {
			return $c->getServer()
					 ->getRootFolder();
		}
		);
		$container->registerService(
			'UserFolder', function (IAppContainer $c) {
			return $c->getServer()
					 ->getUserFolder($c->query('UserId'));
		}
		);
		$container->registerService(
			'PreviewManager', function (IAppContainer $c) {
			return $c->getServer()
					 ->getPreviewManager();
		}
		);
		$container->registerService(
			'CustomPreviewManager', function (IContainer $c) {
			return new Preview(
				$c->query('Config'),
				$c->query('SmarterLogger')
			);
		}
		);
		$container->registerService(
			'WebRoot', function (IAppContainer $c) {
			return $c->getServer()
					 ->getWebRoot();
		}
		);
		$container->registerService(
			'Hasher', function (IAppContainer $c) {
			return $c->getServer()
					 ->getHasher();
		}
		);
		$container->registerService(
			'Config', function (IAppContainer $c) {
			return $c->getServer()
					 ->getConfig();
		}
		);

		/**
		 * Services
		 */
		// Everything we need to do to set up the environment before processing the request
		$container->registerService(
			'Environment', function (IAppContainer $c) {
			return new EnvironmentService(
				$c->query('AppName'),
				$c->query('UserId'),
				$c->query('UserFolder'),
				$c->query('UserManager'),
				$c->getServer(),
				$c->query('Hasher'),
				$c->query('Session'),
				$c->query('SmarterLogger')
			);
		}
		);
		/*// The same thing as above, but in OC8, hopefully. See https://github.com/owncloud/core/issues/12676
		$container->registerService(
			'Environment', function (IAppContainer $c) {
			$token = $c->query('Token');

			return $c
				->getServer()
				->getEnvironment($token);
		}
		);*/
		$container->registerService(
			'InfoService', function (IContainer $c) {
			return new InfoService(
				$c->query('AppName'),
				$c->query('UserFolder'),
				$c->query('Environment'),
				$c->query('SmarterLogger'),
				$c->query('PreviewManager')
			);
		}
		);
		$container->registerService(
			'ThumbnailService', function (IAppContainer $c) {
			return new ThumbnailService(
				$c->query('AppName'),
				$c->query('SmarterLogger'),
				$c->getServer()
				  ->createEventSource(),
				$c->query('PreviewService')
			);
		}
		);
		$container->registerService(
			'PreviewService', function (IContainer $c) {
			return new PreviewService(
				$c->query('AppName'),
				$c->query('Environment'),
				$c->query('SmarterLogger'),
				$c->query('CustomPreviewManager')
			);
		}
		);

		/**
		 * Middleware
		 */
		$container->registerService(
			'SharingCheckMiddleware',
			function (IAppContainer $c) {
				return new SharingCheckMiddleware(
					$c->getAppName(),
					$c->query('Request'),
					$c->query('Config'),
					$c->query('ControllerMethodReflector'),
					$c->query('URLGenerator'),
					$c->query('SmarterLogger')
				);
			}
		);
		$container->registerService(
			'TokenCheckMiddleware',
			function (IContainer $c) {
				return new TokenCheckMiddleware(
					$c->query('AppName'),
					$c->query('Request'),
					$c->query('Environment'),
					$c->query('ControllerMethodReflector'),
					$c->query('URLGenerator'),
					$c->query('SmarterLogger')
				);
			}
		);

		// executed in the order that it is registered
		$container->registerMiddleware('SharingCheckMiddleware');
		$container->registerMiddleware('TokenCheckMiddleware');
	}

}