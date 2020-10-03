<?php
namespace App\Controllers;

use CodeIgniter\Controller;

// Stela models
use App\Models\Summary;

// Stela libraries
use App\Libraries\Maincommands;
use App\Libraries\Commandlist;
use App\Libraries\Openweathermap;
use App\Libraries\Summarylib;

class BaseController extends Controller
{
    // Helpers
	protected $helpers = [];

	// Models
    protected $Summarymodel;

    // Libraries
	protected $Openweathermap;
    protected $Maincommands;
    protected $Commandlist;
    protected $Summarylib;

    // Misc
    protected $AuthorizedChats;
    protected $Verbose;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do not edit this line
		parent::initController($request, $response, $logger);

        // Authorized chats
        // to get the chat id with stela, send the command stela debug id
        $this->AuthorizedChats = array(
            '-471997115',
            '-461336699'
        );

        // Verbose mode
        $this->Verbose = false;

        // Init models
        $this->Summarymodel   = new Summary();

		// Init libraries
        $this->Openweathermap = new Openweathermap();
        $this->Maincommands   = new Maincommands();
        $this->Commandlist    = new Commandlist();
        $this->Summarylib     = new Summarylib();
	}
}
