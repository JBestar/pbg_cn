<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

use App\Models\ConfSite_Model;
use App\Models\Member_Model;
use App\Models\Sess_Model;
use App\Models\Notice_Model;

class BaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['url', 'session', 'common_helper'];
	protected $session ;

	protected $member_model;
	protected $confsite_model;
	protected $sess_model;
	protected $notice_model;
	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		
		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
		
		$this->session = session();

		$this->member_model = new Member_Model();
		$this->confsite_model = new ConfSite_Model();
		$this->sess_model = new Sess_Model();
		$this->notice_model = new Notice_Model();

		// Admin UI language (A): session locale, fallback member.mb_lang, default ko
		$locale = $this->session->get('locale');
		if (!$locale && $this->session->get('uid')) {
			$m = $this->member_model->getByUid($this->session->get('uid'));
			if ($m && !empty($m->mb_lang)) {
				$locale = $m->mb_lang;
				$this->session->set('locale', $locale);
			}
		}
		if (!in_array($locale, ['ko', 'zh', 'en'], true)) {
			$locale = 'ko';
		}
		service('request')->setLocale($locale);
		$this->adminLocale = $locale;
	}

	protected $adminLocale = 'ko';
}
