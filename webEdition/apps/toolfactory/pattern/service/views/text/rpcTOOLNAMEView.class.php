
class rpc<?php echo $TOOLNAME;?>View extends we_rpc_view
{
	function getResponse(we_rpc_response $response)
	{
		$html = 'Hello World! My name is <?php echo $TOOLNAME;?> and I am a webEdition-Application.';

		return $html;

	}
}