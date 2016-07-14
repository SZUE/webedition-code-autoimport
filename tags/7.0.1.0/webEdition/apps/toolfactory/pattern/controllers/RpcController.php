/**
 * Base Rpc Controller
 *
 * @category   app
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class RpcController extends Zend_Controller_Action
{
	/**
	 * The default action
	 */
	public function indexAction()
	{
		$jsonOutput = we_net_rpc_JsonRpc::getReply('<?php echo $TOOLNAME;?>');
		$this->getResponse()->setHeader('Content-Type', 'application/json', true)->appendBody($jsonOutput);
	}

}
